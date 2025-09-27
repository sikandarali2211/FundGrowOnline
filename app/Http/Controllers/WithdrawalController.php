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
        
        // Get user's withdrawal history
        $withdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.withdrawal.index', compact('poolCommission', 'withdrawals'));
    }

    /**
     * Create withdrawal request
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_address' => 'required|string|min:42|max:42',
            'wallet_type' => 'required|in:trust,metamask,other'
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;
        $poolCommission = (float) ($user->referral_commission_balance ?? 0);

        // Check if user has sufficient balance
        if ($amount > $poolCommission) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Available: $' . number_format($poolCommission, 2)
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
            DB::transaction(function () use ($user, $amount, $request, $poolCommission) {
                // Create withdrawal request
                WithdrawalRequest::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'wallet_address' => $request->wallet_address,
                    'wallet_type' => $request->wallet_type,
                    'status' => WithdrawalRequest::STATUS_PENDING
                ]);

                // Deduct amount from user's pool commission
                $user->referral_commission_balance = max(0, $poolCommission - $amount);
                $user->save();

                Log::info('Withdrawal request created', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'wallet_address' => $request->wallet_address,
                    'wallet_type' => $request->wallet_type
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully'
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