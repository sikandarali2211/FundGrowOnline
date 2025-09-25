<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private $bscApiUrl = 'https://api.bscscan.com/api';
    private $apiKey = 'Q4QV82H9XUYPCQYA347QFE1YGF9J2Q9XHU'; // Replace with your BSCScan API key

    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'txHash' => 'required|string',
            'fromAddress' => 'required|string',
            'toAddress' => 'required|string',
            'amount' => 'required|numeric',
            'tokenAddress' => 'nullable|string'
        ]);

        $result = $this->verifyTransactionInternal($request);
        
        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    private function verifyTransactionInternal(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
            'token_address' => 'nullable|string'
        ]);

        $txHash = $request->tx_hash;
        $fromAddress = $request->from_address;
        $toAddress = $request->to_address;
        $amount = $request->amount;
        $tokenAddress = $request->token_address;

        try {
            // Get transaction details from BSCScan
            $response = Http::get($this->bscApiUrl, [
                'module' => 'proxy',
                'action' => 'eth_getTransactionByHash',
                'txhash' => $txHash,
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'verified' => false,
                    'message' => 'Failed to fetch transaction from BSCScan'
                ];
            }

            $txData = $response->json();

            if (isset($txData['error'])) {
                return [
                    'success' => false,
                    'verified' => false,
                    'message' => 'Transaction not found or invalid'
                ];
            }

            // Verify transaction details
            $isValid = $this->validateTransaction($txData, $fromAddress, $toAddress, $amount, $tokenAddress);

            if ($isValid) {
                // Get transaction receipt for confirmation
                $receiptResponse = Http::get($this->bscApiUrl, [
                    'module' => 'proxy',
                    'action' => 'eth_getTransactionReceipt',
                    'txhash' => $txHash,
                    'apikey' => $this->apiKey
                ]);

                $receipt = $receiptResponse->json();
                $isConfirmed = isset($receipt['result']) && $receipt['result']['status'] === '0x1';

                return [
                    'success' => true,
                    'verified' => true,
                    'confirmed' => $isConfirmed,
                    'blockNumber' => $receipt['result']['blockNumber'] ?? null,
                    'gasUsed' => $receipt['result']['gasUsed'] ?? null,
                    'transaction' => $txData['result']
                ];
            } else {
                return [
                    'success' => false,
                    'verified' => false,
                    'message' => 'Transaction details do not match'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Transaction verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'verified' => false,
                'message' => 'Error verifying transaction: ' . $e->getMessage()
            ];
        }
    }

    private function validateTransaction($txData, $expectedFrom, $expectedTo, $expectedAmount, $tokenAddress = null)
    {
        $tx = $txData['result'];
        
        // Check from address
        if (strtolower($tx['from']) !== strtolower($expectedFrom)) {
            return false;
        }

        // Check to address
        if (strtolower($tx['to']) !== strtolower($expectedTo)) {
            return false;
        }

        // For BNB transactions
        if (!$tokenAddress) {
            $txAmount = hexdec($tx['value']);
            $expectedAmountWei = $expectedAmount * pow(10, 18);
            return abs($txAmount - $expectedAmountWei) < 1000; // Allow small difference for gas
        }

        // For BEP20 token transactions, we need to check the logs
        // This is a simplified check - in production, you'd decode the transfer event
        return true;
    }

    public function getTransactionStatus(Request $request)
    {
        $request->validate([
            'txHash' => 'required|string'
        ]);

        try {
            $response = Http::get($this->bscApiUrl, [
                'module' => 'proxy',
                'action' => 'eth_getTransactionReceipt',
                'txhash' => $request->txHash,
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch transaction status'
                ], 400);
            }

            $receipt = $response->json();

            if (isset($receipt['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            $isConfirmed = isset($receipt['result']) && $receipt['result']['status'] === '0x1';

            return response()->json([
                'success' => true,
                'confirmed' => $isConfirmed,
                'blockNumber' => $receipt['result']['blockNumber'] ?? null,
                'gasUsed' => $receipt['result']['gasUsed'] ?? null,
                'receipt' => $receipt['result']
            ]);

        } catch (\Exception $e) {
            Log::error('Transaction status check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking transaction status'
            ], 500);
        }
    }

    public function getBSCBalance(Request $request)
    {
        $request->validate([
            'address' => 'required|string'
        ]);

        try {
            $response = Http::get($this->bscApiUrl, [
                'module' => 'account',
                'action' => 'balance',
                'address' => $request->address,
                'tag' => 'latest',
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch balance'
                ], 400);
            }

            $data = $response->json();
            $balance = isset($data['result']) ? $data['result'] : '0';
            $balanceInBNB = hexdec($balance) / pow(10, 18);

            return response()->json([
                'success' => true,
                'balance' => $balanceInBNB,
                'balanceWei' => $balance
            ]);

        } catch (\Exception $e) {
            Log::error('Balance check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking balance'
            ], 500);
        }
    }

    public function getTransactionHistory(Request $request)
    {
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string|unique:transactions',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'amount' => 'required|numeric',
            'token_address' => 'nullable|string',
            'token_symbol' => 'nullable|string'
        ]);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'tx_hash' => $request->tx_hash,
            'from_address' => $request->from_address,
            'to_address' => $request->to_address,
            'amount' => $request->amount,
            'token_address' => $request->token_address,
            'token_symbol' => $request->token_symbol,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    public function updateTransactionStatus(Request $request, $txHash)
    {
        $transaction = Transaction::where('tx_hash', $txHash)->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,failed',
            'block_number' => 'nullable|integer',
            'gas_used' => 'nullable|string'
        ]);

        $updateData = [
            'status' => $request->status
        ];

        if ($request->block_number) {
            $updateData['block_number'] = $request->block_number;
        }

        if ($request->gas_used) {
            $updateData['gas_used'] = $request->gas_used;
        }

        if ($request->status === 'confirmed') {
            $updateData['confirmed_at'] = now();
        }

        $transaction->update($updateData);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    // Save wallet address to user profile
    public function saveWalletAddress(Request $request)
    {
        try {
            $request->validate([
                'wallet_address' => 'required|string|min:42|max:42'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Update user's wallet address
            $user->wallet_address = $request->wallet_address;
            $user->save();

            Log::info("Wallet address saved for user {$user->id}: {$request->wallet_address}");

            return response()->json([
                'success' => true,
                'message' => 'Wallet address saved successfully',
                'wallet_address' => $request->wallet_address
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid wallet address format',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Failed to save wallet address: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save wallet address'
            ], 500);
        }
    }

    /**
     * Send USDT transaction
     */
    public function sendTransaction(Request $request)
    {
        try {
            $request->validate([
                'recipient_address' => 'required|string|min:42|max:42',
                'amount' => 'required|numeric|min:0.01',
                'tx_hash' => 'required|string|unique:transactions'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user has sufficient balance
            $walletBalance = $this->calculateWalletBalance($user->id);
            if ($request->amount > $walletBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $request->tx_hash,
                'from_address' => $user->wallet_address,
                'to_address' => $request->recipient_address,
                'amount' => $request->amount,
                'token_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20
                'token_symbol' => 'USDT',
                'status' => 'pending',
                'type' => 'send'
            ]);

            Log::info("Send transaction created for user {$user->id}: {$request->tx_hash}");

            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully',
                'transaction' => $transaction
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Failed to record send transaction: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record transaction'
            ], 500);
        }
    }

    /**
     * Check for new transactions to admin wallet
     */
    public function checkAdminWalletTransactions()
    {
        try {
            // Get admin wallet address
            $admin = \App\Models\User::where('utype', 'ADM')
                ->whereNotNull('wallet_address')
                ->first();
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin wallet not found'
                ]);
            }

            $adminWalletAddress = $admin->wallet_address;
            
            // Get recent transactions from BSCScan API
            $response = Http::get($this->bscApiUrl, [
                'module' => 'account',
                'action' => 'txlist',
                'address' => $adminWalletAddress,
                'startblock' => 0,
                'endblock' => 99999999,
                'page' => 1,
                'offset' => 10,
                'sort' => 'desc',
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch transactions from BSCScan'
                ]);
            }

            $data = $response->json();
            $transactions = $data['result'] ?? [];

            $newTransactions = [];
            foreach ($transactions as $tx) {
                // Check if transaction is USDT (BEP20)
                if ($tx['input'] === '0xa9059cbb' && $tx['to'] === $adminWalletAddress) {
                    // Check if transaction already exists in our database
                    $existingTx = Transaction::where('tx_hash', $tx['hash'])->first();
                    
                    if (!$existingTx) {
                        // This is a new USDT transaction to admin wallet
                        $newTransactions[] = $tx;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'new_transactions' => $newTransactions,
                'admin_wallet' => $adminWalletAddress
            ]);

        } catch (\Exception $e) {
            Log::error('Admin wallet transaction check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check admin wallet transactions'
            ], 500);
        }
    }

    /**
     * Process detected transaction automatically
     */
    public function processDetectedTransaction(Request $request)
    {
        \Log::info('processDetectedTransaction called with data:', $request->all());
        \Log::info('User authenticated:', Auth::check());
        \Log::info('User ID:', Auth::id());
        
        $request->validate([
            'tx_hash' => 'required|string',
            'from_address' => 'required|string',
            'amount' => 'required|numeric',
            'block_number' => 'required|string'
        ]);

        try {
            $txHash = $request->tx_hash;
            $fromAddress = $request->from_address;
            $amount = $request->amount;
            $blockNumber = $request->block_number;
            
            \Log::info('Processing transaction:', [
                'tx_hash' => $txHash,
                'from_address' => $fromAddress,
                'amount' => $amount,
                'block_number' => $blockNumber
            ]);

            // Check if transaction already processed
            $existingTransaction = Transaction::where('tx_hash', $txHash)->first();
            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already processed'
                ]);
            }

            // Find user by wallet address
            $user = \App\Models\User::where('wallet_address', $fromAddress)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this wallet address'
                ]);
            }

            // Get admin wallet address
            $admin = \App\Models\User::where('utype', 'ADM')
                ->whereNotNull('wallet_address')
                ->first();

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $txHash,
                'from_address' => $fromAddress,
                'to_address' => $admin->wallet_address,
                'amount' => $amount,
                'token_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20
                'token_symbol' => 'USDT',
                'status' => 'confirmed',
                'block_number' => hexdec($blockNumber),
                'confirmed_at' => now()
            ]);

            // Update user wallet balance
            $this->updateUserWalletBalance($user->id, $amount);

            // Log for admin
            Log::info("Auto-detected topup transaction", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'tx_hash' => $txHash,
                'amount' => $amount,
                'from' => $fromAddress,
                'to' => $admin->wallet_address
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction processed successfully',
                'transaction' => $transaction,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Auto transaction processing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle user topup transaction to admin
     */
    public function processTopupTransaction(Request $request)
    {
        $request->validate([
            'tx_hash' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'from_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'to_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'token_address' => 'nullable|string',
            'token_symbol' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            $txHash = $request->tx_hash;
            $amount = $request->amount;
            $fromAddress = $request->from_address;
            $toAddress = $request->to_address;
            $tokenAddress = $request->token_address;
            $tokenSymbol = $request->token_symbol ?? 'USDT';

            // Verify transaction on blockchain
            $verificationResult = $this->verifyTransactionInternal($request);
            
            if (!$verificationResult['verified']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction verification failed: ' . $verificationResult['message']
                ], 400);
            }

            // Check if transaction already exists
            $existingTransaction = Transaction::where('tx_hash', $txHash)->first();
            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already processed'
                ], 400);
            }

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $txHash,
                'from_address' => $fromAddress,
                'to_address' => $toAddress,
                'amount' => $amount,
                'token_address' => $tokenAddress,
                'token_symbol' => $tokenSymbol,
                'status' => $verificationResult['confirmed'] ? 'confirmed' : 'pending',
                'block_number' => $verificationResult['blockNumber'] ?? null,
                'gas_used' => $verificationResult['gasUsed'] ?? null,
                'transaction_data' => $verificationResult['transaction'] ?? null,
                'confirmed_at' => $verificationResult['confirmed'] ? now() : null
            ]);

            // Update user wallet balance
            $this->updateUserWalletBalance($user->id, $amount);

            // Log transaction for admin
            Log::info("Topup transaction processed", [
                'user_id' => $user->id,
                'tx_hash' => $txHash,
                'amount' => $amount,
                'from' => $fromAddress,
                'to' => $toAddress
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction processed successfully',
                'transaction' => $transaction,
                'new_balance' => $this->calculateWalletBalance($user->id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Topup transaction processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user wallet balance after successful transaction
     */
    private function updateUserWalletBalance($userId, $amount)
    {
        try {
            // The transaction is already stored in the transactions table
            // The wallet balance will be calculated dynamically from the transactions
            // No need to store duplicate data
            
            Log::info("User wallet balance updated", [
                'user_id' => $userId,
                'amount_added' => $amount,
                'timestamp' => now()
            ]);
            
            // The balance calculation is now handled in UserController::calculateWalletBalance()
            // which includes confirmed transactions in the balance calculation
            
        } catch (\Exception $e) {
            Log::error('Failed to update user wallet balance: ' . $e->getMessage());
        }
    }

    /**
     * Connect user's wallet (Trust Wallet)
     */
    public function connectWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'wallet_type' => 'required|string|in:trust,metamask,other'
        ]);

        try {
            $user = Auth::user();
            $walletAddress = $request->wallet_address;
            $walletType = $request->wallet_type;

            // Update user's wallet address
            $user->wallet_address = $walletAddress;
            $user->wallet_type = $walletType;
            $user->save();

            Log::info("User {$user->id} connected {$walletType} wallet: {$walletAddress}");

            return response()->json([
                'success' => true,
                'message' => 'Wallet connected successfully',
                'wallet_address' => $walletAddress,
                'wallet_type' => $walletType
            ]);

        } catch (\Exception $e) {
            Log::error('Wallet connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect wallet'
            ], 500);
        }
    }

    /**
     * Calculate user's wallet balance from investments
     */
    private function calculateWalletBalance($userId)
    {
        try {
            // Get user's total investments
            $totalInvestment = \App\Models\PlanSelection::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('amount');

            // Get user's total returns (this would be calculated based on your business logic)
            $totalReturns = 0; // Implement your returns calculation logic here

            // Calculate wallet balance (investments + returns)
            $walletBalance = $totalInvestment + $totalReturns;

            return number_format($walletBalance, 2);
        } catch (\Exception $e) {
            Log::error("Failed to calculate wallet balance: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Disconnect wallet and remove wallet address from database
     */
    public function disconnectWallet(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Clear wallet address from database
            $user->wallet_address = null;
            $user->wallet_type = null;
            $user->save();

            Log::info("Wallet disconnected for user: {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Wallet disconnected successfully!',
                'data' => [
                    'user_id' => $user->id,
                    'wallet_address' => null,
                    'wallet_type' => null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to disconnect wallet: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect wallet. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
