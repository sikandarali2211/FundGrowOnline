<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    /**
     * Show withdrawal form
     */
    public function index()
    {
        $user = Auth::user();
        $poolCommission = (float) ($user->referral_commission_balance ?? 0);
        $balanceWallet = (float) ($user->balance_wallet ?? 0);
        $totalAvailable = $poolCommission + $balanceWallet;
        
        // Get user's withdrawal history
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.withdrawal.index', compact('poolCommission', 'balanceWallet', 'totalAvailable', 'withdrawals'));
    }

    /**
     * Create withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_address' => 'required|string|min:42|max:42',
            'wallet_type' => 'required|in:trust,metamask,other',
            'withdrawal_source' => 'required|in:pool_commission,balance_wallet,both'
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount; // requested (gross) amount
        $poolCommission = (float) ($user->referral_commission_balance ?? 0);
        $balanceWallet = (float) ($user->balance_wallet ?? 0);
        $totalAvailable = $poolCommission + $balanceWallet;

        // Check if user has sufficient balance
        if ($amount > $totalAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Available: $' . number_format($totalAvailable, 2)
            ], 400);
        }

        // Check if user has a pending request
        $pendingRequest = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', WithdrawalRequest::STATUS_PENDING)
            ->first();

        if ($pendingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending withdrawal request'
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $amount, $request, $poolCommission, $balanceWallet) {
                // Apply 10% fee on requested amount
                $fee = round($amount * 0.10, 2);
                $netAmount = max(0, round($amount - $fee, 2));
                // Create withdrawal request
                WithdrawalRequest::create([
                    'user_id' => $user->id,
                    // Store NET amount to be paid out to the user
                    'amount' => $netAmount,
                    'wallet_address' => $request->wallet_address,
                    'wallet_type' => $request->wallet_type,
                    'status' => WithdrawalRequest::STATUS_PENDING,
                    'withdrawal_source' => $request->withdrawal_source,
                    'admin_notes' => trim((string)($request->admin_notes ?? '')) . ' | 10% fee $' . number_format($fee, 2) . ' deducted from $' . number_format($amount, 2)
                ]);

                // Deduct GROSS amount from the selected source(s)
                $remainingAmount = $amount;
                
                if ($request->withdrawal_source === 'pool_commission' || $request->withdrawal_source === 'both') {
                    $deductFromPool = min($remainingAmount, $poolCommission);
                    $user->referral_commission_balance = max(0, $poolCommission - $deductFromPool);
                    $remainingAmount -= $deductFromPool;
                }
                
                if ($request->withdrawal_source === 'balance_wallet' || $request->withdrawal_source === 'both') {
                    $deductFromBalance = min($remainingAmount, $balanceWallet);
                    $user->balance_wallet = max(0, $balanceWallet - $deductFromBalance);
                }
                
                $user->save();

                Log::info('Withdrawal request created', [
                    'user_id' => $user->id,
                    'requested_amount' => $amount,
                    'net_amount' => $netAmount,
                    'fee_amount' => $fee,
                    'wallet_address' => $request->wallet_address,
                    'wallet_type' => $request->wallet_type,
                    'withdrawal_source' => $request->withdrawal_source
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully (10% fee applied)'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create withdrawal request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create withdrawal request'
            ], 500);
        }
    }

    /**
     * Get user's withdrawal history
     */
    public function history()
    {
        $user = Auth::user();
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.withdrawal.history', compact('withdrawals'));
    }
}