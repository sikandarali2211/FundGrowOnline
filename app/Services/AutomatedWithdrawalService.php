<?php

namespace App\Services;

use App\Models\WithdrawalRequest;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AutomatedWithdrawalService
{
    protected Web3Service $web3Service;
    protected BscScanService $bscScanService;
    protected int $maxRetries;
    protected int $retryDelay;

    public function __construct(Web3Service $web3Service, BscScanService $bscScanService)
    {
        $this->web3Service = $web3Service;
        $this->bscScanService = $bscScanService;
        $this->maxRetries = 3;
        $this->retryDelay = 30; // seconds
    }

    /**
     * Process all pending withdrawal requests automatically
     */
    public function processPendingWithdrawals(): array
    {
        $results = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Get all pending withdrawal requests
            $pendingWithdrawals = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Processing pending withdrawals', ['count' => $pendingWithdrawals->count()]);

            foreach ($pendingWithdrawals as $withdrawal) {
                $results['processed']++;
                
                try {
                    $result = $this->processWithdrawal($withdrawal);
                    
                    if ($result['success']) {
                        $results['successful']++;
                        Log::info('Withdrawal processed successfully', [
                            'withdrawal_id' => $withdrawal->id,
                            'user_id' => $withdrawal->user_id,
                            'amount' => $withdrawal->amount,
                            'transaction_hash' => $result['transaction_hash'] ?? null
                        ]);
                    } else {
                        $results['failed']++;
                        $results['errors'][] = [
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $result['error'] ?? 'Unknown error'
                        ];
                        Log::error('Withdrawal processing failed', [
                            'withdrawal_id' => $withdrawal->id,
                            'error' => $result['error'] ?? 'Unknown error'
                        ]);
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Exception during withdrawal processing', [
                        'withdrawal_id' => $withdrawal->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to process pending withdrawals: ' . $e->getMessage());
            $results['errors'][] = ['general_error' => $e->getMessage()];
        }

        return $results;
    }

    /**
     * Process a single withdrawal request
     */
    public function processWithdrawal(WithdrawalRequest $withdrawal): array
    {
        try {
            // Validate withdrawal request
            $validation = $this->validateWithdrawal($withdrawal);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['error']
                ];
            }

            // Check admin balance
            if (!$this->web3Service->hasSufficientBalance($withdrawal->amount)) {
                return [
                    'success' => false,
                    'error' => 'Insufficient admin wallet balance'
                ];
            }

            // Create and send transaction
            $transaction = $this->web3Service->createTransferTransaction(
                $withdrawal->wallet_address,
                $withdrawal->amount
            );

            $signedTx = $this->web3Service->signTransaction($transaction);
            $result = $this->web3Service->sendTransaction($signedTx);

            if ($result['success']) {
                // Update withdrawal status
                $this->updateWithdrawalStatus($withdrawal, WithdrawalRequest::STATUS_APPROVED, $result['transactionHash']);
                
                // Create transaction record
                $this->createTransactionRecord($withdrawal, $result['transactionHash']);

                return [
                    'success' => true,
                    'transaction_hash' => $result['transactionHash']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'Transaction failed'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Failed to process withdrawal: ' . $e->getMessage(), [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate withdrawal request
     */
    private function validateWithdrawal(WithdrawalRequest $withdrawal): array
    {
        // Check if withdrawal is in pending status
        if ($withdrawal->status !== WithdrawalRequest::STATUS_PENDING) {
            return [
                'valid' => false,
                'error' => 'Withdrawal is not in pending status'
            ];
        }

        // Check if user exists and is active
        $user = $withdrawal->user;
        if (!$user) {
            return [
                'valid' => false,
                'error' => 'User not found'
            ];
        }

        // Check if wallet address is valid
        if (!$this->isValidWalletAddress($withdrawal->wallet_address)) {
            return [
                'valid' => false,
                'error' => 'Invalid wallet address format'
            ];
        }

        // Check minimum withdrawal amount
        $minAmount = config('withdrawal.min_amount', 1.0);
        if ($withdrawal->amount < $minAmount) {
            return [
                'valid' => false,
                'error' => "Minimum withdrawal amount is $minAmount USDT"
            ];
        }

        // Check maximum withdrawal amount
        $maxAmount = config('withdrawal.max_amount', 10000.0);
        if ($withdrawal->amount > $maxAmount) {
            return [
                'valid' => false,
                'error' => "Maximum withdrawal amount is $maxAmount USDT"
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if wallet address is valid
     */
    private function isValidWalletAddress(string $address): bool
    {
        // Basic Ethereum address validation
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
    }

    /**
     * Update withdrawal status
     */
    private function updateWithdrawalStatus(WithdrawalRequest $withdrawal, string $status, ?string $transactionHash = null): void
    {
        DB::transaction(function () use ($withdrawal, $status, $transactionHash) {
            $withdrawal->status = $status;
            $withdrawal->processed_at = now();
            
            if ($transactionHash) {
                $withdrawal->transaction_hash = $transactionHash;
            }
            
            $withdrawal->save();
        });
    }

    /**
     * Create transaction record
     */
    private function createTransactionRecord(WithdrawalRequest $withdrawal, string $transactionHash): void
    {
        try {
            Transaction::create([
                'user_id' => $withdrawal->user_id,
                'transaction_hash' => $transactionHash,
                'from_address' => config('services.bscscan.admin_address'),
                'to_address' => $withdrawal->wallet_address,
                'amount' => $withdrawal->amount,
                'token_symbol' => 'USDT',
                'token_contract' => '0x55d398326f99059fF775485246999027B3197955',
                'status' => 'pending',
                'type' => 'withdrawal_transfer',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create transaction record: ' . $e->getMessage());
        }
    }

    /**
     * Monitor transaction confirmations
     */
    public function monitorTransactionConfirmations(): array
    {
        $results = [
            'checked' => 0,
            'confirmed' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Get all pending transactions
            $pendingTransactions = Transaction::where('status', 'pending')
                ->where('type', 'withdrawal_transfer')
                ->whereNotNull('transaction_hash')
                ->get();

            foreach ($pendingTransactions as $transaction) {
                $results['checked']++;
                
                try {
                    $isConfirmed = $this->web3Service->isTransactionConfirmed($transaction->transaction_hash);
                    
                    if ($isConfirmed) {
                        $this->updateTransactionStatus($transaction, 'confirmed');
                        $this->updateWithdrawalStatus(
                            WithdrawalRequest::where('transaction_hash', $transaction->transaction_hash)->first(),
                            WithdrawalRequest::STATUS_COMPLETED
                        );
                        $results['confirmed']++;
                    } else {
                        // Check if transaction is old enough to be considered failed
                        $hoursOld = $transaction->created_at->diffInHours(now());
                        if ($hoursOld > 24) { // 24 hours timeout
                            $this->updateTransactionStatus($transaction, 'failed');
                            $results['failed']++;
                        }
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Failed to check transaction confirmation: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to monitor transaction confirmations: ' . $e->getMessage());
            $results['errors'][] = ['general_error' => $e->getMessage()];
        }

        return $results;
    }

    /**
     * Update transaction status
     */
    private function updateTransactionStatus(Transaction $transaction, string $status): void
    {
        $transaction->status = $status;
        $transaction->updated_at = now();
        $transaction->save();
    }

    /**
     * Get withdrawal statistics
     */
    public function getWithdrawalStats(): array
    {
        $stats = [
            'total_pending' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'total_approved' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_APPROVED)->count(),
            'total_completed' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'total_rejected' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_REJECTED)->count(),
            'total_amount_pending' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount'),
            'total_amount_completed' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->sum('amount'),
            'admin_balance' => $this->web3Service->getUSDTBalance(config('services.bscscan.admin_address')),
            'network_status' => $this->web3Service->getNetworkStatus()
        ];

        return $stats;
    }

    /**
     * Process withdrawal with retry mechanism
     */
    public function processWithdrawalWithRetry(WithdrawalRequest $withdrawal): array
    {
        $attempts = 0;
        $lastError = null;

        while ($attempts < $this->maxRetries) {
            $attempts++;
            
            try {
                $result = $this->processWithdrawal($withdrawal);
                
                if ($result['success']) {
                    return $result;
                }
                
                $lastError = $result['error'] ?? 'Unknown error';
                
                if ($attempts < $this->maxRetries) {
                    Log::info('Retrying withdrawal processing', [
                        'withdrawal_id' => $withdrawal->id,
                        'attempt' => $attempts,
                        'error' => $lastError
                    ]);
                    
                    sleep($this->retryDelay);
                }
                
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                
                if ($attempts < $this->maxRetries) {
                    Log::info('Retrying withdrawal processing after exception', [
                        'withdrawal_id' => $withdrawal->id,
                        'attempt' => $attempts,
                        'error' => $lastError
                    ]);
                    
                    sleep($this->retryDelay);
                }
            }
        }

        // Mark withdrawal as failed after all retries
        $this->updateWithdrawalStatus($withdrawal, WithdrawalRequest::STATUS_REJECTED);
        
        return [
            'success' => false,
            'error' => "Failed after {$this->maxRetries} attempts: " . $lastError
        ];
    }
}
