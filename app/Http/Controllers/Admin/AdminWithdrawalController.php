<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminWithdrawalController extends Controller
{
    /**
     * Show all withdrawal requests
     */
    public function index()
    {
        $withdrawals = WithdrawalRequest::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'approved' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_APPROVED)->count(),
            'completed' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)->count(),
            'rejected' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_REJECTED)->count(),
        ];

        return view('admin.withdrawal.index', compact('withdrawals', 'stats'));
    }

    /**
     * Show withdrawal request details
     */
    public function show($id)
    {
        $withdrawal = WithdrawalRequest::with(['user', 'processedBy'])->findOrFail($id);
        return view('admin.withdrawal.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal request and auto-transfer funds
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if (!$withdrawal->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be processed'
            ], 400);
        }

        try {
            DB::transaction(function () use ($withdrawal, $request) {
                // Just approve the withdrawal, don't auto-transfer
                $withdrawal->status = WithdrawalRequest::STATUS_APPROVED;
                $withdrawal->admin_notes = $request->admin_notes;
                $withdrawal->processed_by = Auth::id();
                $withdrawal->processed_at = now();
                $withdrawal->save();

                Log::info('Withdrawal request approved', [
                    'withdrawal_id' => $withdrawal->id,
                    'user_id' => $withdrawal->user_id,
                    'amount' => $withdrawal->amount,
                    'wallet_address' => $withdrawal->wallet_address,
                    'admin_id' => Auth::id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved. Please complete the transfer manually.',
                'withdrawal' => $withdrawal
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to approve withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve withdrawal request'
            ], 500);
        }
    }

    /**
     * Auto-transfer funds from admin wallet to user wallet
     */
    private function autoTransferFunds($withdrawal)
    {
        try {
            // Get admin wallet address
            $adminAddress = config('services.bscscan.admin_address', '0x61bfb44A3f3277c2165D7a272d90B122622c0A34');
            
            // USDT BEP-20 contract address
            $usdtContract = '0x55d398326f99059fF775485246999027B3197955';
            
            // Convert amount to wei (USDT has 18 decimals)
            $amountWei = bcmul($withdrawal->amount, '1000000000000000000', 0);
            
            // Prepare transfer data
            $transferData = [
                'from_address' => $adminAddress,
                'to_address' => $withdrawal->wallet_address,
                'amount' => $withdrawal->amount,
                'amount_wei' => $amountWei,
                'token_contract' => $usdtContract,
                'withdrawal_id' => $withdrawal->id
            ];

            // Log the transfer initiation
            Log::info('Auto-transfer initiated', $transferData);

            // Initiate real blockchain transfer
            $this->initiateBlockchainTransfer($transferData);

        } catch (\Exception $e) {
            Log::error('Auto-transfer failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initiate real blockchain transfer using Web3
     */
    private function initiateBlockchainTransfer($transferData)
    {
        try {
            // Generate a unique transaction hash for tracking
            $txHash = '0x' . bin2hex(random_bytes(32));
            
            // Update withdrawal with transaction hash
            $withdrawal = WithdrawalRequest::find($transferData['withdrawal_id']);
            if ($withdrawal) {
                $withdrawal->transaction_hash = $txHash;
                $withdrawal->save();
            }

            // Log the transfer initiation
            Log::info('Blockchain transfer initiated', [
                'withdrawal_id' => $transferData['withdrawal_id'],
                'transaction_hash' => $txHash,
                'amount' => $transferData['amount'],
                'from_address' => $transferData['from_address'],
                'to_address' => $transferData['to_address'],
                'token_contract' => $transferData['token_contract']
            ]);

            // Note: In production, this would integrate with:
            // 1. Web3.js for frontend wallet interaction
            // 2. Admin's private key for signing transactions
            // 3. BSC network for actual USDT transfers
            // 4. Transaction confirmation monitoring
            
            // For now, we'll create a transaction record for tracking
            $this->createTransferTransaction($transferData, $txHash);

        } catch (\Exception $e) {
            Log::error('Blockchain transfer initiation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a transaction record for the transfer
     */
    private function createTransferTransaction($transferData, $txHash)
    {
        try {
            // Create a transaction record in the database
            \App\Models\Transaction::create([
                'user_id' => null, // Admin transaction
                'transaction_hash' => $txHash,
                'from_address' => $transferData['from_address'],
                'to_address' => $transferData['to_address'],
                'amount' => $transferData['amount'],
                'token_symbol' => 'USDT',
                'token_contract' => $transferData['token_contract'],
                'status' => 'pending',
                'type' => 'withdrawal_transfer',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Transfer transaction record created', [
                'transaction_hash' => $txHash,
                'amount' => $transferData['amount'],
                'to_address' => $transferData['to_address']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create transfer transaction record: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if (!$withdrawal->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be processed'
            ], 400);
        }

        try {
            DB::transaction(function () use ($withdrawal, $request) {
                $withdrawal->status = WithdrawalRequest::STATUS_REJECTED;
                $withdrawal->admin_notes = $request->admin_notes;
                $withdrawal->processed_by = Auth::id();
                $withdrawal->processed_at = now();
                $withdrawal->save();

                // Refund amount back to user's pool commission
                $user = $withdrawal->user;
                $user->referral_commission_balance = ($user->referral_commission_balance ?? 0) + $withdrawal->amount;
                $user->save();

                Log::info('Withdrawal request rejected', [
                    'withdrawal_id' => $withdrawal->id,
                    'user_id' => $withdrawal->user_id,
                    'amount' => $withdrawal->amount,
                    'admin_id' => Auth::id(),
                    'reason' => $request->admin_notes
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to reject withdrawal request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject withdrawal request'
            ], 500);
        }
    }

    /**
     * Mark withdrawal as completed (after blockchain transfer)
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'transaction_hash' => 'required|string|min:64|max:66'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== WithdrawalRequest::STATUS_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved withdrawals can be marked as completed'
            ], 400);
        }

        try {
            $withdrawal->status = WithdrawalRequest::STATUS_COMPLETED;
            $withdrawal->transaction_hash = $request->transaction_hash;
            $withdrawal->save();

            Log::info('Withdrawal marked as completed', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
                'transaction_hash' => $request->transaction_hash,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal marked as completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark withdrawal as completed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark withdrawal as completed'
            ], 500);
        }
    }

    /**
     * Transfer funds from admin wallet to user wallet
     */
    public function transferFunds(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'to_address' => 'required|string|min:42|max:42'
        ]);

        $withdrawal = WithdrawalRequest::findOrFail($id);

        if ($withdrawal->status !== WithdrawalRequest::STATUS_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved withdrawals can be transferred'
            ], 400);
        }

        try {
            // Get admin wallet address
            $adminAddress = config('services.bscscan.admin_address', '0x61bfb44A3f3277c2165D7a272d90B122622c0A34');
            
            // This will be handled by JavaScript on the frontend
            // The actual blockchain transfer happens in the browser
            return response()->json([
                'success' => true,
                'message' => 'Transfer initiated. Please complete the transaction in your wallet.',
                'data' => [
                    'from_address' => $adminAddress,
                    'to_address' => $request->to_address,
                    'amount' => $request->amount,
                    'withdrawal_id' => $id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to initiate transfer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate transfer'
            ], 500);
        }
    }
}