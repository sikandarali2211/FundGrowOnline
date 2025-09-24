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
    private $apiKey = 'YourBSCScanAPIKey'; // Replace with your BSCScan API key

    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'txHash' => 'required|string',
            'fromAddress' => 'required|string',
            'toAddress' => 'required|string',
            'amount' => 'required|numeric',
            'tokenAddress' => 'nullable|string'
        ]);

        $txHash = $request->txHash;
        $fromAddress = $request->fromAddress;
        $toAddress = $request->toAddress;
        $amount = $request->amount;
        $tokenAddress = $request->tokenAddress;

        try {
            // Get transaction details from BSCScan
            $response = Http::get($this->bscApiUrl, [
                'module' => 'proxy',
                'action' => 'eth_getTransactionByHash',
                'txhash' => $txHash,
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch transaction from BSCScan'
                ], 400);
            }

            $txData = $response->json();

            if (isset($txData['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found or invalid'
                ], 404);
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

                return response()->json([
                    'success' => true,
                    'verified' => true,
                    'confirmed' => $isConfirmed,
                    'blockNumber' => $receipt['result']['blockNumber'] ?? null,
                    'gasUsed' => $receipt['result']['gasUsed'] ?? null,
                    'transaction' => $txData['result']
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'verified' => false,
                    'message' => 'Transaction details do not match'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Transaction verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error verifying transaction'
            ], 500);
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
}
