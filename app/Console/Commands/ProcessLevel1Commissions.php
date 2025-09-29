<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlanSelection;

class ProcessLevel1Commissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:process-level1 {--user-id= : Process for specific user ID} {--all : Process all Level 1 commissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Level 1 commissions (60% pool commission, 40% pool wallet)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $processAll = $this->option('all');

        $this->info('🚀 Starting Level 1 Commission Processing...');
        $this->newLine();

        if ($userId) {
            $this->processUserLevel1Commission($userId);
        } elseif ($processAll) {
            $this->processAllLevel1Commissions();
        } else {
            $this->error('Please specify --user-id=ID or --all');
            return 1;
        }

        $this->newLine();
        $this->info('✅ Level 1 commission processing completed!');
        return 0;
    }

    /**
     * Process Level 1 commission for a specific user
     */
    protected function processUserLevel1Commission($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User ID {$userId} not found!");
            return;
        }

        $this->info("👤 Processing Level 1 commission for: {$user->name} (ID: {$user->id})");
        
        // Get first approved plan
        $firstPlan = PlanSelection::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('created_at')
            ->first();

        if (!$firstPlan) {
            $this->warn("⚠️  No approved plans found for this user");
            return;
        }

        $this->info("📋 First Plan: {$firstPlan->plan_name} - \${$firstPlan->plan_amount}");

        // Check if user has referrer
        if (!$user->referred_by) {
            $this->warn("⚠️  User has no referrer - no commission to distribute");
            return;
        }

        $referrer = User::find($user->referred_by);
        if (!$referrer) {
            $this->warn("⚠️  Referrer not found");
            return;
        }

        $this->info("👤 Referrer: {$referrer->name} (ID: {$referrer->id})");
        $this->line("   Current Pool Commission: \${$referrer->referral_commission_balance}");
        $this->line("   Current Pool Wallet: \${$referrer->pool_wallet_amount}");

        // Check if commission already distributed
        $expectedCommission = $firstPlan->plan_amount;
        $expectedPoolCommission = $expectedCommission * 0.6;
        $expectedPoolWallet = $expectedCommission * 0.4;

        // Check if commission was already distributed by looking at referrer's balance
        $hasCommission = $referrer->referral_commission_balance >= $expectedPoolCommission && 
                        $referrer->pool_wallet_amount >= $expectedPoolWallet;

        if ($hasCommission) {
            $this->warn("⚠️  Commission already distributed for this plan");
            return;
        }

        // Distribute commission
        try {
            $this->info("💰 Distributing Level 1 commission...");
            $result = $referrer->distributeCommission($firstPlan->plan_amount, 100);
            
            if ($result['success']) {
                $this->info("✅ Level 1 commission distributed successfully!");
                $this->line("   Pool Commission (60%): \${$result['pool_commission']}");
                $this->line("   Pool Wallet (40%): \${$result['pool_wallet_commission']}");
                
                // Refresh referrer data
                $referrer->refresh();
                $this->line("   New Pool Commission: \${$referrer->referral_commission_balance}");
                $this->line("   New Pool Wallet: \${$referrer->pool_wallet_amount}");
            } else {
                $this->error("❌ Commission distribution failed");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error distributing commission: " . $e->getMessage());
        }
    }

    /**
     * Process Level 1 commissions for all eligible users
     */
    protected function processAllLevel1Commissions()
    {
        $this->info("🌍 Processing Level 1 commissions for all eligible users...");
        
        // Find all users with referrers and approved plans
        $eligibleUsers = User::whereNotNull('referred_by')
            ->whereHas('planSelections', function($q) {
                $q->where('status', 'approved');
            })
            ->get();

        $this->info("📊 Found {$eligibleUsers->count()} users with referrers and approved plans");

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($eligibleUsers as $user) {
            $this->newLine();
            $this->info("👤 Processing: {$user->name} (ID: {$user->id})");
            
            try {
                // Get first plan
                $firstPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->first();

                if (!$firstPlan) {
                    $this->warn("⚠️  No approved plans found for {$user->name}");
                    $skipped++;
                    continue;
                }

                $referrer = User::find($user->referred_by);
                if (!$referrer) {
                    $this->warn("⚠️  Referrer not found for {$user->name}");
                    $skipped++;
                    continue;
                }

                // Check if commission already distributed
                $expectedCommission = $firstPlan->plan_amount;
                $expectedPoolCommission = $expectedCommission * 0.6;
                $expectedPoolWallet = $expectedCommission * 0.4;

                $hasCommission = $referrer->referral_commission_balance >= $expectedPoolCommission && 
                                $referrer->pool_wallet_amount >= $expectedPoolWallet;

                if ($hasCommission) {
                    $this->warn("⚠️  Commission already distributed for {$user->name}");
                    $skipped++;
                    continue;
                }

                // Distribute commission
                $result = $referrer->distributeCommission($firstPlan->plan_amount, 100);
                
                if ($result['success']) {
                    $this->info("✅ Commission distributed for {$user->name}");
                    $this->line("   Pool: \${$result['pool_commission']}, Wallet: \${$result['pool_wallet_commission']}");
                    $processed++;
                } else {
                    $this->error("❌ Failed for {$user->name}");
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing {$user->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->line("   ✅ Processed: {$processed}");
        $this->line("   ⚠️  Skipped: {$skipped}");
        $this->line("   ❌ Errors: {$errors}");
    }
}
