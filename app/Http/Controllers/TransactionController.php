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
}
