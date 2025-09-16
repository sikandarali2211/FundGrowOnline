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

        return view('user.index', compact(
            'totalReferrals',
            'directReferrals', 
            'newReferralsToday',
            'newReferralsWeek'
        ));
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
