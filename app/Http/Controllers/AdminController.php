<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PlanPayment;
use App\Models\PaymentTransaction;
use App\Models\UserInvestment;
use App\Services\BscScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalUsers      = User::count();
        $newUsersToday   = User::whereDate('created_at', today())->count();
        $newUsers7Days   = User::where('created_at', '>=', now()->subDays(7))->count();

        // Chart data for users
        $usersChartData = [];
        $usersChartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $usersChartLabels[] = $date->format('M d');
            $usersChartData[] = User::whereDate('created_at', $date)->count();
        }

        // Chart data for sales (using plan selections as proxy)
        $salesChartData = [];
        $salesChartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $salesChartLabels[] = $date->format('M d');
            $salesChartData[] = \App\Models\PlanSelection::whereDate('created_at', $date)->sum('plan_amount') ?? 0;
        }

        // Get admin wallet balance from blockchain
        $adminBalance = $this->getAdminWalletBalance();

        // Debug data
        \Log::info('Chart Data:', [
            'usersChartLabels' => $usersChartLabels,
            'usersChartData' => $usersChartData,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'adminBalance' => $adminBalance
        ]);

        return view('admin.index', compact(
            'totalUsers', 
            'newUsersToday', 
            'newUsers7Days',
            'usersChartData',
            'usersChartLabels',
            'salesChartData',
            'salesChartLabels',
            'adminBalance'
        ));
    }

    /**
     * Get admin wallet balance from blockchain
     */
    private function getAdminWalletBalance()
    {
        try {
            $bsc = new BscScanService();
            $adminAddress = config('services.bscscan.admin_address', '0x3Bb750C42f9B80CbEd7003c004eaeAdc76c9b4Fd');
            
            // Get BNB balance
            $balanceWei = Cache::remember("admin:bnb:balance", 30, function() use ($bsc, $adminAddress) {
                return $bsc->getBalanceWei($adminAddress);
            });
            
            $bnbBalance = BscScanService::formatAmount($balanceWei, 18);
            
            // Get USDT balance (BEP-20)
            $usdtContract = '0x55d398326f99059fF775485246999027B3197955';
            $usdtBalance = Cache::remember("admin:usdt:balance", 30, function() use ($bsc, $adminAddress, $usdtContract) {
                $tokenTxs = $bsc->getTokenTx($adminAddress, 1, 1000, 'desc', $usdtContract);
                // Calculate current USDT balance from transactions
                $balance = 0;
                foreach ($tokenTxs as $tx) {
                    if (strtolower($tx['to'] ?? '') === strtolower($adminAddress)) {
                        $balance += (float) BscScanService::formatAmount($tx['value'] ?? '0', 18);
                    } elseif (strtolower($tx['from'] ?? '') === strtolower($adminAddress)) {
                        $balance -= (float) BscScanService::formatAmount($tx['value'] ?? '0', 18);
                    }
                }
                return max(0, $balance);
            });
            
            return [
                'bnb' => number_format($bnbBalance, 4),
                'usdt' => number_format($usdtBalance, 2),
                'total_usd' => number_format($usdtBalance, 2) // Assuming USDT = USD
            ];
            
        } catch (\Exception $e) {
            \Log::error('Failed to get admin wallet balance: ' . $e->getMessage());
            return [
                'bnb' => '0.0000',
                'usdt' => '0.00',
                'total_usd' => '0.00'
            ];
        }
    }
    public function showUserPlan($userId)
    {
        // Fetch the user and their selected plan
        $user = User::findOrFail($userId);
        $plan = PlanPayment::where('user_id', $userId)->latest()->first(); // Assumes the latest plan is the active one

        return view('admin.user_plan', compact('user', 'plan'));
    }

    public function updatePlanStatus(Request $request, $planId)
    {
        // Find the plan and update its status
        $plan = PlanPayment::findOrFail($planId);
        $plan->status = $request->status; // status can be 'active', 'blocked', or 'rejected'
        $plan->save();

        return redirect()->back()->with('success', 'Plan status updated successfully');
    }
}
