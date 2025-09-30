<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

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

        return view('user.index', compact(
            'totalReferrals',
            'directReferrals',
            'newReferralsToday',
            'newReferralsWeek',
            'walletBalance',
            'balanceBreakdown',
            'adminWalletAddress'
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

        // Load investments too
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
        });

        return view('user.referralTeam.index', [
            'user'      => $user,
            'referrals' => $referrals->sortBy('tree_level')
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
}
