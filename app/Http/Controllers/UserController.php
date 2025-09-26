<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Calculate total users referred by this user (all levels)
        $totalReferrals = $this->getTotalReferrals($user->id);
        
        // Calculate direct referrals (Level 1) - Only Active users
        $directReferrals = User::where(function($q) use ($user) {
                $q->where('referred_by', $user->id)
                  ->orWhere('sponsor_id', $user->id);
            })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->count();
        
        // Calculate new referrals today - Only Active users
        $newReferralsToday = User::where(function($q) use ($user) {
                $q->where('referred_by', $user->id)
                  ->orWhere('sponsor_id', $user->id);
            })
            ->whereHas('activationInfo', function ($query) {
                $query->where('status', 'Active');
            })
            ->whereDate('created_at', today())
            ->count();
        
        // Calculate new referrals this week - Only Active users
        $newReferralsWeek = User::where(function($q) use ($user) {
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
            // Get user's total investment amount from user_investments table
            $totalInvestment = \App\Models\UserInvestment::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('amount');
            
            // Get user's total returns from plan payments (if table exists)
            $totalReturns = 0; // For now, we'll focus on investments and transactions
            
            // Get user's total sent amounts from transactions (topup amounts)
            $totalSentAmount = \App\Models\Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->sum('amount');
            
            // Get user's total received amounts from transactions (if any)
            $totalReceivedAmount = 0; // For now, we'll focus on sent amounts
            
            // Get current pool wallet amount
            $poolAmount = (float) \App\Models\User::where('id', $userId)->value('pool_wallet_amount') ?? 0;
            
            // Calculate wallet balance (investments + returns + sent amounts - pool amount)
            // The sent amounts represent the user's contribution to the system
            // Pool amount is subtracted because it's no longer available in balance wallet
            $walletBalance = $totalInvestment + $totalReturns + $totalSentAmount - $poolAmount;
            
            return number_format($walletBalance, 2);
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
            // Get user's total investment amount from user_investments table
            $totalInvestment = \App\Models\UserInvestment::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('amount');
            
            // Get user's total returns from plan payments (if table exists)
            $totalReturns = 0; // For now, we'll focus on investments and transactions
            
            // Get user's total sent amounts from transactions (topup amounts)
            $totalSentAmount = \App\Models\Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->sum('amount');
            
            // Get user's total received amounts from transactions (if any)
            $totalReceivedAmount = 0; // For now, we'll focus on sent amounts
            
            // Get recent transactions for display
            $recentTransactions = \App\Models\Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['amount', 'token_symbol', 'from_address', 'to_address', 'created_at']);
            
            return [
                'total_investment' => number_format($totalInvestment, 2),
                'total_returns' => number_format($totalReturns, 2),
                'total_sent' => number_format($totalSentAmount, 2),
                'total_received' => number_format($totalReceivedAmount, 2),
                'recent_transactions' => $recentTransactions
            ];
        } catch (\Exception $e) {
            \Log::error("Failed to get balance breakdown for user {$userId}: " . $e->getMessage());
            return [
                'total_investment' => '0.00',
                'total_returns' => '0.00',
                'total_sent' => '0.00',
                'total_received' => '0.00',
                'recent_transactions' => collect()
            ];
        }
    }

    /**
     * Recursively calculate total referrals across all levels - Only Active users
     */
    private function getTotalReferrals($userId)
    {
        $directReferrals = User::where(function($q) use ($userId) {
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
