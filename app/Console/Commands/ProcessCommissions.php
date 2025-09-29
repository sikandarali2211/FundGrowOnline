<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlanSelection;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;

class ProcessCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:process {--user-id= : Process commissions for specific user ID} {--all : Process all pending commissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process commissions for users who purchased second plan';

    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        parent::__construct();
        $this->commissionService = $commissionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $processAll = $this->option('all');

        $this->info('🚀 Starting Commission Processing...');
        $this->newLine();

        if ($userId) {
            $this->processUserCommissions($userId);
        } elseif ($processAll) {
            $this->processAllCommissions();
        } else {
            $this->error('Please specify --user-id=ID or --all');
            return 1;
        }

        $this->newLine();
        $this->info('✅ Commission processing completed!');
        return 0;
    }

    /**
     * Process commissions for a specific user
     */
    protected function processUserCommissions($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User ID {$userId} not found!");
            return;
        }

        $this->info("👤 Processing commissions for: {$user->name} (ID: {$user->id})");
        
        // Get all approved plans for this user
        $approvedPlans = PlanSelection::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('created_at')
            ->get();

        $this->info("📋 Found {$approvedPlans->count()} approved plans");

        if ($approvedPlans->count() >= 2) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 2 commission");
            
            // Get the second plan
            $secondPlan = $approvedPlans->skip(1)->first();
            $this->info("🎯 Second plan: {$secondPlan->plan_name} - \${$secondPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $secondPlan->id)
                ->where('commission_type', 'second_plan')
                ->first();

            if ($existingCommission) {
                $this->warn("⚠️  Commission already exists for this plan:");
                $this->line("   Pool: \${$existingCommission->pool_commission}");
                $this->line("   Profit: \${$existingCommission->profit_commission}");
                $this->line("   Global: \${$existingCommission->global_pool_commission}");
                return;
            }

            // Process commission
            try {
                $this->info("💰 Processing commission...");
                $result = $this->commissionService->processSecondPlanCommission($secondPlan);
                
                if ($result['success']) {
                    $this->info("✅ Commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 2;
                    $user->save();
                    $this->info("🎯 User level updated to 2");
                } else {
                    $this->error("❌ Commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing commission: " . $e->getMessage());
            }
        } else {
            $this->warn("⚠️  User only has {$approvedPlans->count()} approved plans - needs 2 for Level 2 commission");
        }
    }

    /**
     * Process commissions for all eligible users
     */
    protected function processAllCommissions()
    {
        $this->info("🌍 Processing commissions for all eligible users...");
        
        // Find all users with 2+ approved plans
        $eligibleUsers = User::whereHas('planSelections', function ($query) {
            $query->where('status', 'approved');
        }, '>=', 2)->get();

        $this->info("📊 Found {$eligibleUsers->count()} users with 2+ approved plans");

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($eligibleUsers as $user) {
            $this->newLine();
            $this->info("👤 Processing: {$user->name} (ID: {$user->id})");
            
            try {
                // Get second plan
                $secondPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(1)
                    ->first();

                if (!$secondPlan) {
                    $this->warn("⚠️  No second plan found for {$user->name}");
                    $skipped++;
                    continue;
                }

                // Check if commission already exists
                $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                    ->where('plan_selection_id', $secondPlan->id)
                    ->where('commission_type', 'second_plan')
                    ->first();

                if ($existingCommission) {
                    $this->warn("⚠️  Commission already exists for {$user->name}");
                    $skipped++;
                    continue;
                }

                // Process commission
                $result = $this->commissionService->processSecondPlanCommission($secondPlan);
                
                if ($result['success']) {
                    $this->info("✅ Commission processed for {$user->name}");
                    $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 2;
                    $user->save();
                    
                    $processed++;
                } else {
                    $this->error("❌ Failed for {$user->name}: {$result['error']}");
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
