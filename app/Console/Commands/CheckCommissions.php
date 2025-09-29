<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlanSelection;
use App\Models\CommissionTransaction;
use App\Models\GlobalPool;

class CheckCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:check {--user-id= : Check commissions for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check commission status and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');

        $this->info('📊 Commission Status Check');
        $this->newLine();

        if ($userId) {
            $this->checkUserCommissions($userId);
        } else {
            $this->checkAllCommissions();
        }

        return 0;
    }

    /**
     * Check commissions for a specific user
     */
    protected function checkUserCommissions($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User ID {$userId} not found!");
            return;
        }

        $this->info("👤 User: {$user->name} (ID: {$user->id})");
        $this->info("🎯 Level: {$user->level}");
        $this->newLine();

        // Check approved plans
        $approvedPlans = PlanSelection::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('created_at')
            ->get();

        $this->info("📋 Approved Plans: {$approvedPlans->count()}");
        foreach ($approvedPlans as $plan) {
            $this->line("   - {$plan->plan_name}: \${$plan->plan_amount} ({$plan->created_at->format('Y-m-d H:i')})");
        }
        $this->newLine();

        // Check balances
        $this->info("💰 Current Balances:");
        $this->line("   Pool Commission: \${$user->referral_commission_balance}");
        $this->line("   Pool Wallet: \${$user->pool_wallet_amount}");
        $this->line("   Profit Wallet: \${$user->profit_wallet}");
        $this->line("   Total Commission Earned: \${$user->total_commission_earned}");
        $this->newLine();

        // Check commission transactions
        $commissions = CommissionTransaction::where('user_id', $userId)->get();
        $this->info("💸 Commission Transactions: {$commissions->count()}");
        
        if ($commissions->count() > 0) {
            foreach ($commissions as $commission) {
                $this->line("   - {$commission->commission_type}: \${$commission->total_commission}");
                $this->line("     Pool: \${$commission->pool_commission}, Profit: \${$commission->profit_commission}, Global: \${$commission->global_pool_commission}");
                $this->line("     Date: {$commission->created_at->format('Y-m-d H:i')}");
                $this->line("     Description: {$commission->description}");
                $this->newLine();
            }
        }

        // Check if eligible for commission
        if ($approvedPlans->count() >= 2) {
            $this->info("✅ User is eligible for Level 2 commission");
            
            $secondPlan = $approvedPlans->skip(1)->first();
            $existingCommission = CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $secondPlan->id)
                ->where('commission_type', 'second_plan')
                ->first();

            if ($existingCommission) {
                $this->info("✅ Commission already processed for second plan");
            } else {
                $this->warn("⚠️  Commission not processed for second plan yet");
                $this->line("   Run: php artisan commissions:process --user-id={$userId}");
            }
        } else {
            $this->warn("⚠️  User needs 2 approved plans for Level 2 commission");
        }
    }

    /**
     * Check all commission statistics
     */
    protected function checkAllCommissions()
    {
        // Global statistics
        $this->info("🌍 Global Commission Statistics");
        $this->newLine();

        // Total users
        $totalUsers = User::count();
        $this->info("👥 Total Users: {$totalUsers}");

        // Level 2 users
        $level2Users = User::where('level', 2)->count();
        $this->info("🎯 Level 2 Users: {$level2Users}");

        // Users with 2+ approved plans
        $eligibleUsers = User::whereHas('planSelections', function ($query) {
            $query->where('status', 'approved');
        }, '>=', 2)->count();
        $this->info("✅ Users with 2+ approved plans: {$eligibleUsers}");

        // Commission transactions
        $totalCommissions = CommissionTransaction::count();
        $this->info("💸 Total Commission Transactions: {$totalCommissions}");

        $totalPoolCommission = CommissionTransaction::sum('pool_commission');
        $totalProfitCommission = CommissionTransaction::sum('profit_commission');
        $totalGlobalCommission = CommissionTransaction::sum('global_pool_commission');
        
        $this->newLine();
        $this->info("💰 Commission Distribution:");
        $this->line("   Pool Commission (60%): \${$totalPoolCommission}");
        $this->line("   Profit Commission (30%): \${$totalProfitCommission}");
        $this->line("   Global Pool (10%): \${$totalGlobalCommission}");
        $this->line("   Total Distributed: \${" . ($totalPoolCommission + $totalProfitCommission + $totalGlobalCommission) . "}");

        // Global pool status
        $globalPool = GlobalPool::first();
        if ($globalPool) {
            $this->newLine();
            $this->info("🌐 Global Pool Status:");
            $this->line("   Total Amount: \${$globalPool->total_amount}");
            $this->line("   Transaction Count: {$globalPool->transaction_count}");
            $this->line("   Last Contribution: \${$globalPool->last_contribution}");
            $this->line("   Last Updated: {$globalPool->last_updated}");
        } else {
            $this->warn("⚠️  No global pool data found");
        }

        // Recent transactions
        $this->newLine();
        $this->info("📈 Recent Commission Transactions (Last 5):");
        $recentCommissions = CommissionTransaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentCommissions->count() > 0) {
            foreach ($recentCommissions as $commission) {
                $this->line("   - {$commission->user->name}: \${$commission->total_commission} ({$commission->commission_type}) - {$commission->created_at->format('Y-m-d H:i')}");
            }
        } else {
            $this->line("   No recent transactions found");
        }
    }
}
