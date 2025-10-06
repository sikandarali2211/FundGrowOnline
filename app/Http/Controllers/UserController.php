<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('referrer');

        // Calculate total users referred by this user (all levels)
        $totalReferrals = $this->getTotalReferrals($user->id);

        // Calculate direct referrals (Level 1) - Only Active users
        $directReferrals = User::where(function ($q) use ($user) {
            $q->where('referred_by', $user->id)
                ->orWhere('sponsor_id', $user->id);
        })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->count();

        // Calculate new referrals today - Only Active users
        $newReferralsToday = User::where(function ($q) use ($user) {
            $q->where('referred_by', $user->id)
                ->orWhere('sponsor_id', $user->id);
        })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->whereDate('created_at', today())
            ->count();

        // Calculate new referrals this week - Only Active users
        $newReferralsWeek = User::where(function ($q) use ($user) {
            $q->where('referred_by', $user->id)
                ->orWhere('sponsor_id', $user->id);
        })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Calculate wallet balance from user investments and transactions
        $walletBalance = $this->calculateWalletBalance($user->id);

        // Get detailed balance breakdown
        $balanceBreakdown = $this->getBalanceBreakdown($user->id);

        // Get admin wallet address from database
        $adminWalletAddress = $this->getAdminWalletAddress();

        // Calculate active and pending plan users from referrals
        $activePlanUsers = User::where(function ($q) use ($user) {
            $q->where('referred_by', $user->id)
                ->orWhere('sponsor_id', $user->id);
        })
            ->whereHas('planSelections', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

        $pendingPlanUsers = User::where(function ($q) use ($user) {
            $q->where('referred_by', $user->id)
                ->orWhere('sponsor_id', $user->id);
        })
            ->whereHas('planSelections', function ($query) {
                $query->where('status', 'pending');
            })
            ->whereDoesntHave('planSelections', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

        // Calculate total withdrawals for the user
        $totalWithdrawals = \App\Models\WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        return view('user.index', compact(
            'totalReferrals',
            'directReferrals',
            'newReferralsToday',
            'newReferralsWeek',
            'walletBalance',
            'balanceBreakdown',
            'adminWalletAddress',
            'activePlanUsers',
            'pendingPlanUsers',
            'totalWithdrawals'
        ));
    }

    /**
     * Get admin wallet address from database
     */
    private function getAdminWalletAddress()
    {
        try {
            // Find admin user by utype = 'ADM' and get their wallet address
            $admin = User::where('utype', 'ADM')
                ->whereNotNull('wallet_address')
                ->first();

            return $admin ? $admin->wallet_address : '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6'; // Fallback address
        } catch (\Exception $e) {
            // If there's any error, return fallback address
            return '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6';
        }
    }

    /**
     * Get admin wallet address via AJAX
     */
    public function getAdminWalletAddressAjax()
    {
        try {
            $adminWalletAddress = $this->getAdminWalletAddress();

            return response()->json([
                'success' => true,
                'admin_wallet_address' => $adminWalletAddress,
                'is_live' => $adminWalletAddress !== '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admin wallet address',
                'admin_wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
                'is_live' => false
            ]);
        }
    }

    /**
     * Calculate user's wallet balance from investments and transactions
     */
    private function calculateWalletBalance($userId)
    {
        try {
            // Get user's balance_wallet directly from database
            // This field is updated when commission is distributed
            $user = \App\Models\User::find($userId);
            $balanceWallet = (float) ($user->balance_wallet ?? 0);

            \Log::info('Balance calculation', [
                'user_id' => $userId,
                'balance_wallet' => $balanceWallet,
                'pool_wallet' => $user->pool_wallet_amount ?? 0
            ]);

            return number_format($balanceWallet, 2);
        } catch (\Exception $e) {
            \Log::error("Failed to calculate wallet balance for user {$userId}: " . $e->getMessage());
            // If there's any error, return 0
            return '0.00';
        }
    }

    /**
     * Get detailed balance breakdown for user dashboard
     */
    private function getBalanceBreakdown($userId)
    {
        try {
            // Get user's balance_wallet and pool_wallet_amount directly from database
            $user = \App\Models\User::find($userId);
            $balanceWallet = (float) ($user->balance_wallet ?? 0);
            $poolWallet = (float) ($user->pool_wallet_amount ?? 0);

            // Get user's total sent amounts from transactions (topup amounts)
            $totalSentAmount = \App\Models\Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->sum('amount');

            // Get recent transactions for display
            $recentTransactions = \App\Models\Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['amount', 'token_symbol', 'from_address', 'to_address', 'created_at']);

            // Calculate user's own money (topup + exchange) - separate from referral commissions
            $referralCommissionBalance = (float) ($user->referral_commission_balance ?? 0);
            $referralCommissionPool = (float) ($user->referral_commission_pool ?? 0);
            // User's own money is just the balance_wallet (no need to subtract commission)
            $userOwnMoney = $balanceWallet;

            // Pool commission (60% of referral commissions)
            $poolCommission = $referralCommissionBalance;

            // Pool wallet = only user's own pool money (no referral commission)
            $userOwnPoolMoney = $poolWallet;

            return [
                'balance_wallet' => number_format($userOwnMoney, 2),
                'pool_wallet' => number_format($userOwnPoolMoney, 2),
                'pool_commission' => number_format($poolCommission, 2),
                'total_balance' => number_format($userOwnMoney, 2), // Same as balance_wallet
                'total_sent' => number_format($totalSentAmount, 2),
                'total_investment' => '0.00', // Not shown
                'total_returns' => '0.00', // Not shown
                'recent_transactions' => $recentTransactions
            ];
        } catch (\Exception $e) {
            \Log::error("Failed to get balance breakdown for user {$userId}: " . $e->getMessage());
            return [
                'balance_wallet' => '0.00',
                'pool_wallet' => '0.00',
                'pool_commission' => '0.00',
                'total_balance' => '0.00',
                'total_sent' => '0.00',
                'total_investment' => '0.00',
                'total_returns' => '0.00',
                'recent_transactions' => collect()
            ];
        }
    }

    /**
     * Recursively calculate total referrals across all levels - Only Active users
     */
    private function getTotalReferrals($userId)
    {
        $directReferrals = User::where(function ($q) use ($userId) {
            $q->where('referred_by', $userId)
                ->orWhere('sponsor_id', $userId);
        })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->get();

        $total = $directReferrals->count();

        foreach ($directReferrals as $referral) {
            $total += $this->getTotalReferrals($referral->id);
        }

        return $total;
    }

    public function referralTeam()
    {
        $user = auth()->user();

        // Load investments and commission transactions with referrer relationship
        $allUsers = User::with(['referrer', 'investments'])->get();

        $referrals = $allUsers->filter(function ($u) use ($user) {
            return $this->calculateReferralLevel($u, $user) !== null;
        });

        $referrals->each(function ($referral) use ($user) {
            // Tree depth (Level 1 = direct, Level 2 = indirect, etc.)
            $referral->tree_level = $this->calculateReferralLevel($referral, $user);

            // Highest plan purchased
            $highestPlan = $referral->investments
                ->where('status', UserInvestment::STATUS_ACTIVE)
                ->map(function ($inv) {
                    return $inv->plan_id ?? $inv->investment_plan_id;
                })
                ->max();


            if ($highestPlan) {
                $referral->plan_status = 'Active';
                $referral->plan_level  = $highestPlan;
            } else {
                $pending = $referral->investments
                    ->where('status', UserInvestment::STATUS_PENDING)
                    ->first();
                $referral->plan_status = $pending ? 'Pending' : 'No Plan';
                $referral->plan_level  = null;
            }

            // Get total commission earned from this referral
            $commissions = \App\Models\CommissionTransaction::where('user_id', $user->id)
                ->whereHas('planSelection', function($q) use ($referral) {
                    $q->where('user_id', $referral->id);
                })
                ->get();

            $referral->total_commission = $commissions->sum('total_commission');
            $referral->commission_details = $commissions->map(function($comm) {
                $level = '';
                if (str_contains($comm->description, 'Level 2')) $level = 'L2';
                elseif (str_contains($comm->description, 'Level 3')) $level = 'L3';
                elseif (str_contains($comm->description, 'Level 4')) $level = 'L4';
                elseif (str_contains($comm->description, 'Level 5')) $level = 'L5';
                elseif (str_contains($comm->description, 'Level 6')) $level = 'L6';
                elseif (str_contains($comm->description, 'Level 7')) $level = 'L7';
                elseif (str_contains($comm->description, 'Level 8')) $level = 'L8';
                elseif (str_contains($comm->description, 'Level 9')) $level = 'L9';
                elseif (str_contains($comm->description, 'Level 10')) $level = 'L10';
                elseif (str_contains($comm->description, 'Level 11')) $level = 'L11';
                elseif (str_contains($comm->description, 'Level 12')) $level = 'L12';
                elseif (str_contains($comm->description, 'Level 13')) $level = 'L13';
                elseif (str_contains($comm->description, 'Level 14')) $level = 'L14';
                elseif (str_contains($comm->description, 'Level 15')) $level = 'L15';
                return [
                    'amount' => $comm->total_commission,
                    'level' => $level
                ];
            });
        });

        // Calculate total commission from all levels
        $totalCommissionByLevel = [];
        for ($i = 2; $i <= 15; $i++) {
            $levelCommissions = \App\Models\CommissionTransaction::where('user_id', $user->id)
                ->where('description', 'like', "%Level {$i}%")
                ->sum('total_commission');
            $totalCommissionByLevel[$i] = $levelCommissions;
        }

        $totalCommissionAllLevels = array_sum($totalCommissionByLevel);

        return view('user.referralTeam.index', [
            'user'      => $user,
            'referrals' => $referrals->sortBy('tree_level'),
            'totalCommissionByLevel' => $totalCommissionByLevel,
            'totalCommissionAllLevels' => $totalCommissionAllLevels
        ]);
    }

    private function calculateReferralLevel($referral, $currentUser, $level = 1)
    {
        if ($referral->referred_by == $currentUser->id) {
            return $level;
        }

        $parent = User::find($referral->referred_by);

        if (!$parent) {
            return null;
        }

        if ($parent->id == $currentUser->id) {
            return $level;
        }

        return $this->calculateReferralLevel($parent, $currentUser, $level + 1);
    }


    public function referralLink()
    {
        $user = auth()->user();


        $referralUrl = route('register', ['ref' => $user->referral_code]);

        // Alternative (agar route name issue ho): $referralUrl = url('/register').'?ref='.$user->referral_code;

        return view('user.referrallink.index', [
            'user'        => $user,
            'referralUrl' => $referralUrl,
        ]);
    }

    /**
     * Show the change email form
     */
    public function changeEmail()
    {
        $user = auth()->user();
        return view('user.change-email', compact('user'));
    }

    /**
     * Update user's email
     */
    public function updateEmail(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_email' => 'required|email|in:' . $user->email,
            'new_email' => 'required|email|unique:users,email,' . $user->id,
            'confirm_email' => 'required|email|same:new_email'
        ], [
            'current_email.in' => 'Current email does not match your account email.',
            'new_email.unique' => 'This email is already taken by another user.',
            'confirm_email.same' => 'Email confirmation does not match the new email.'
        ]);

        try {
            // Update the email
            $user->email = $request->new_email;
            $user->save();

            return redirect()->route('user.change.email')
                ->with('success', 'Email updated successfully! Your new email is: ' . $request->new_email);

        } catch (\Exception $e) {
            return redirect()->route('user.change.email')
                ->with('error', 'Failed to update email. Please try again.');
        }
    }
}
