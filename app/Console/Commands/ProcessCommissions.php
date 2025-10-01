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
    protected $description = 'Process commissions for users who purchased plans from Level 2 through Level 15';

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
        }

        // Check for Level 3 (third plan) eligibility
        if ($approvedPlans->count() >= 3) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 3 commission");
            
            // Get the third plan
            $thirdPlan = $approvedPlans->skip(2)->first();
            $this->info("🎯 Third plan: {$thirdPlan->plan_name} - \${$thirdPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $thirdPlan->id)
                ->where('commission_type', 'third_plan')
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
                $this->info("💰 Processing Level 3 commission...");
                $result = $this->commissionService->processThirdPlanCommission($thirdPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 3 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 3;
                    $user->save();
                    $this->info("🎯 User level updated to 3");
                } else {
                    $this->error("❌ Level 3 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 3 commission: " . $e->getMessage());
            }
        }

        // Check for Level 4 (fourth plan) eligibility
        if ($approvedPlans->count() >= 4) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 4 commission");
            
            // Get the fourth plan
            $fourthPlan = $approvedPlans->skip(3)->first();
            $this->info("🎯 Fourth plan: {$fourthPlan->plan_name} - \${$fourthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $fourthPlan->id)
                ->where('commission_type', 'fourth_plan')
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
                $this->info("💰 Processing Level 4 commission...");
                $result = $this->commissionService->processFourthPlanCommission($fourthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 4 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 4;
                    $user->save();
                    $this->info("🎯 User level updated to 4");
                } else {
                    $this->error("❌ Level 4 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 4 commission: " . $e->getMessage());
            }
        }

        // Check for Level 5 (fifth plan) eligibility
        if ($approvedPlans->count() >= 5) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 5 commission");
            
            // Get the fifth plan
            $fifthPlan = $approvedPlans->skip(4)->first();
            $this->info("🎯 Fifth plan: {$fifthPlan->plan_name} - \${$fifthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $fifthPlan->id)
                ->where('commission_type', 'fifth_plan')
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
                $this->info("💰 Processing Level 5 commission...");
                $result = $this->commissionService->processFifthPlanCommission($fifthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 5 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 5;
                    $user->save();
                    $this->info("🎯 User level updated to 5");
                } else {
                    $this->error("❌ Level 5 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 5 commission: " . $e->getMessage());
            }
        }

        // Check for Level 6 (sixth plan) eligibility
        if ($approvedPlans->count() >= 6) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 6 commission");
            
            // Get the sixth plan
            $sixthPlan = $approvedPlans->skip(5)->first();
            $this->info("🎯 Sixth plan: {$sixthPlan->plan_name} - \${$sixthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $sixthPlan->id)
                ->where('commission_type', 'sixth_plan')
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
                $this->info("💰 Processing Level 6 commission...");
                $result = $this->commissionService->processSixthPlanCommission($sixthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 6 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 6;
                    $user->save();
                    $this->info("🎯 User level updated to 6");
                } else {
                    $this->error("❌ Level 6 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 6 commission: " . $e->getMessage());
            }
        }

        // Check for Level 7 (seventh plan) eligibility
        if ($approvedPlans->count() >= 7) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 7 commission");
            
            // Get the seventh plan
            $seventhPlan = $approvedPlans->skip(6)->first();
            $this->info("🎯 Seventh plan: {$seventhPlan->plan_name} - \${$seventhPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $seventhPlan->id)
                ->where('commission_type', 'seventh_plan')
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
                $this->info("💰 Processing Level 7 commission...");
                $result = $this->commissionService->processSeventhPlanCommission($seventhPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 7 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 7;
                    $user->save();
                    $this->info("🎯 User level updated to 7");
                } else {
                    $this->error("❌ Level 7 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 7 commission: " . $e->getMessage());
            }
        }

        // Check for Level 8 (eighth plan) eligibility
        if ($approvedPlans->count() >= 8) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 8 commission");
            
            // Get the eighth plan
            $eighthPlan = $approvedPlans->skip(7)->first();
            $this->info("🎯 Eighth plan: {$eighthPlan->plan_name} - \${$eighthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $eighthPlan->id)
                ->where('commission_type', 'eighth_plan')
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
                $this->info("💰 Processing Level 8 commission...");
                $result = $this->commissionService->processEighthPlanCommission($eighthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 8 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 8;
                    $user->save();
                    $this->info("🎯 User level updated to 8");
                } else {
                    $this->error("❌ Level 8 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 8 commission: " . $e->getMessage());
            }
        }

        // Check for Level 9 (ninth plan) eligibility
        if ($approvedPlans->count() >= 9) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 9 commission");
            
            // Get the ninth plan
            $ninthPlan = $approvedPlans->skip(8)->first();
            $this->info("🎯 Ninth plan: {$ninthPlan->plan_name} - \${$ninthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $ninthPlan->id)
                ->where('commission_type', 'ninth_plan')
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
                $this->info("💰 Processing Level 9 commission...");
                $result = $this->commissionService->processNinthPlanCommission($ninthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 9 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 9;
                    $user->save();
                    $this->info("🎯 User level updated to 9");
                } else {
                    $this->error("❌ Level 9 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 9 commission: " . $e->getMessage());
            }
        }

        // Check for Level 10 (tenth plan) eligibility
        if ($approvedPlans->count() >= 10) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 10 commission");
            
            // Get the tenth plan
            $tenthPlan = $approvedPlans->skip(9)->first();
            $this->info("🎯 Tenth plan: {$tenthPlan->plan_name} - \${$tenthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $tenthPlan->id)
                ->where('commission_type', 'tenth_plan')
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
                $this->info("💰 Processing Level 10 commission...");
                $result = $this->commissionService->processTenthPlanCommission($tenthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 10 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 10;
                    $user->save();
                    $this->info("🎯 User level updated to 10");
                } else {
                    $this->error("❌ Level 10 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 10 commission: " . $e->getMessage());
            }
        }

        // Check for Level 11 (eleventh plan) eligibility
        if ($approvedPlans->count() >= 11) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 11 commission");
            
            // Get the eleventh plan
            $eleventhPlan = $approvedPlans->skip(10)->first();
            $this->info("🎯 Eleventh plan: {$eleventhPlan->plan_name} - \${$eleventhPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $eleventhPlan->id)
                ->where('commission_type', 'eleventh_plan')
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
                $this->info("💰 Processing Level 11 commission...");
                $result = $this->commissionService->processEleventhPlanCommission($eleventhPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 11 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 11;
                    $user->save();
                    $this->info("🎯 User level updated to 11");
                } else {
                    $this->error("❌ Level 11 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 11 commission: " . $e->getMessage());
            }
        }

        // Check for Level 12 (twelfth plan) eligibility
        if ($approvedPlans->count() >= 12) {
            $this->info("✅ User has {$approvedPlans->count()} approved plans - eligible for Level 12 commission");
            
            // Get the twelfth plan
            $twelfthPlan = $approvedPlans->skip(11)->first();
            $this->info("🎯 Twelfth plan: {$twelfthPlan->plan_name} - \${$twelfthPlan->plan_amount}");

            // Check if commission already exists
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $twelfthPlan->id)
                ->where('commission_type', 'twelfth_plan')
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
                $this->info("💰 Processing Level 12 commission...");
                $result = $this->commissionService->processTwelfthPlanCommission($twelfthPlan);
                
                if ($result['success']) {
                    $this->info("✅ Level 12 commission processed successfully!");
                    $this->line("   Pool Commission (60%): \${$result['data']['pool_commission']}");
                    $this->line("   Profit Commission (30%): \${$result['data']['profit_commission']}");
                    $this->line("   Global Pool (10%): \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 12;
                    $user->save();
                    $this->info("🎯 User level updated to 12");
        } else {
                    $this->error("❌ Level 12 commission processing failed: {$result['error']}");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error processing Level 12 commission: " . $e->getMessage());
            }
        }

        // Check for Level 13 (thirteenth plan) eligibility
        if ($approvedPlans->count() >= 13) {
            $thirteenthPlan = $approvedPlans->skip(12)->first();
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)
                ->where('plan_selection_id', $thirteenthPlan->id)
                ->where('commission_type', 'thirteenth_plan')
                ->first();
            if (!$existingCommission) {
                $result = $this->commissionService->processThirteenthPlanCommission($thirteenthPlan);
                if ($result['success']) {
                    $user->level = 13;
                    $user->save();
                    $this->info("✅ Level 13 processed - Global: \${$result['data']['global_pool_commission']}");
                }
            }
        }

        // Check for Level 14 (fourteenth plan) eligibility
        if ($approvedPlans->count() >= 14) {
            $fourteenthPlan = $approvedPlans->skip(13)->first();
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)->where('plan_selection_id', $fourteenthPlan->id)->where('commission_type', 'fourteenth_plan')->first();
            if (!$existingCommission) {
                $result = $this->commissionService->processFourteenthPlanCommission($fourteenthPlan);
                if ($result['success']) { $user->level = 14; $user->save(); $this->info("✅ L14 - Global: \${$result['data']['global_pool_commission']}"); }
            }
        }

        // Check for Level 15 (fifteenth plan) eligibility
        if ($approvedPlans->count() >= 15) {
            $fifteenthPlan = $approvedPlans->skip(14)->first();
            $existingCommission = \App\Models\CommissionTransaction::where('user_id', $userId)->where('plan_selection_id', $fifteenthPlan->id)->where('commission_type', 'fifteenth_plan')->first();
            if (!$existingCommission) {
                $result = $this->commissionService->processFifteenthPlanCommission($fifteenthPlan);
                if ($result['success']) { $user->level = 15; $user->save(); $this->info("✅ L15 - Global: \${$result['data']['global_pool_commission']}"); }
            }
        }

        if ($approvedPlans->count() < 2) {
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
                // Process Level 2 commission
                $secondPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(1)
                    ->first();

                if ($secondPlan) {
                // Check if commission already exists
                $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                    ->where('plan_selection_id', $secondPlan->id)
                    ->where('commission_type', 'second_plan')
                    ->first();

                    if (!$existingCommission) {
                        // Process Level 2 commission
                $result = $this->commissionService->processSecondPlanCommission($secondPlan);
                
                if ($result['success']) {
                            $this->info("✅ Level 2 commission processed for {$user->name}");
                    $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                    
                    // Update user level
                    $user->level = 2;
                    $user->save();
                    
                    $processed++;
                } else {
                            $this->error("❌ Level 2 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 2 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 3 commission
                $thirdPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(2)
                    ->first();

                if ($thirdPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $thirdPlan->id)
                        ->where('commission_type', 'third_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 3 commission
                        $result = $this->commissionService->processThirdPlanCommission($thirdPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 3 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 3;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 3 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 3 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 4 commission
                $fourthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(3)
                    ->first();

                if ($fourthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $fourthPlan->id)
                        ->where('commission_type', 'fourth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 4 commission
                        $result = $this->commissionService->processFourthPlanCommission($fourthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 4 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 4;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 4 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 4 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 5 commission
                $fifthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(4)
                    ->first();

                if ($fifthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $fifthPlan->id)
                        ->where('commission_type', 'fifth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 5 commission
                        $result = $this->commissionService->processFifthPlanCommission($fifthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 5 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 5;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 5 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 5 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 6 commission
                $sixthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(5)
                    ->first();

                if ($sixthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $sixthPlan->id)
                        ->where('commission_type', 'sixth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 6 commission
                        $result = $this->commissionService->processSixthPlanCommission($sixthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 6 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 6;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 6 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 6 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 7 commission
                $seventhPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(6)
                    ->first();

                if ($seventhPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $seventhPlan->id)
                        ->where('commission_type', 'seventh_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 7 commission
                        $result = $this->commissionService->processSeventhPlanCommission($seventhPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 7 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 7;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 7 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 7 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 8 commission
                $eighthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(7)
                    ->first();

                if ($eighthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $eighthPlan->id)
                        ->where('commission_type', 'eighth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 8 commission
                        $result = $this->commissionService->processEighthPlanCommission($eighthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 8 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 8;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 8 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 8 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 9 commission
                $ninthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(8)
                    ->first();

                if ($ninthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $ninthPlan->id)
                        ->where('commission_type', 'ninth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 9 commission
                        $result = $this->commissionService->processNinthPlanCommission($ninthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 9 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 9;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 9 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 9 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 10 commission
                $tenthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(9)
                    ->first();

                if ($tenthPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $tenthPlan->id)
                        ->where('commission_type', 'tenth_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 10 commission
                        $result = $this->commissionService->processTenthPlanCommission($tenthPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 10 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 10;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 10 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 10 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 11 commission
                $eleventhPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(10)
                    ->first();

                if ($eleventhPlan) {
                    // Check if commission already exists
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $eleventhPlan->id)
                        ->where('commission_type', 'eleventh_plan')
                        ->first();

                    if (!$existingCommission) {
                        // Process Level 11 commission
                        $result = $this->commissionService->processEleventhPlanCommission($eleventhPlan);
                        
                        if ($result['success']) {
                            $this->info("✅ Level 11 commission processed for {$user->name}");
                            $this->line("   Pool: \${$result['data']['pool_commission']}, Profit: \${$result['data']['profit_commission']}, Global: \${$result['data']['global_pool_commission']}");
                            
                            // Update user level
                            $user->level = 11;
                            $user->save();
                            
                            $processed++;
                        } else {
                            $this->error("❌ Level 11 failed for {$user->name}: {$result['error']}");
                            $errors++;
                        }
                    } else {
                        $this->warn("⚠️  Level 11 commission already exists for {$user->name}");
                        $skipped++;
                    }
                }

                // Process Level 12 commission
                $twelfthPlan = PlanSelection::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->skip(11)
                    ->first();

                if ($twelfthPlan) {
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)
                        ->where('plan_selection_id', $twelfthPlan->id)
                        ->where('commission_type', 'twelfth_plan')
                        ->first();

                    if (!$existingCommission) {
                        $result = $this->commissionService->processTwelfthPlanCommission($twelfthPlan);
                        if ($result['success']) {
                            $this->info("✅ Level 12 commission processed for {$user->name}");
                            $user->level = 12;
                            $user->save();
                            $processed++;
                        } else {
                    $errors++;
                        }
                    } else {
                        $skipped++;
                    }
                }

                // Process Level 13 commission
                $thirteenthPlan = PlanSelection::where('user_id', $user->id)->where('status', 'approved')->orderBy('created_at')->skip(12)->first();
                if ($thirteenthPlan) {
                    $existingCommission = \App\Models\CommissionTransaction::where('user_id', $user->id)->where('plan_selection_id', $thirteenthPlan->id)->where('commission_type', 'thirteenth_plan')->first();
                    if (!$existingCommission) {
                        $result = $this->commissionService->processThirteenthPlanCommission($thirteenthPlan);
                        if ($result['success']) { $user->level = 13; $user->save(); $processed++; } else { $errors++; }
                    } else { $skipped++; }
                }

                // Process Level 14 commission
                $fourteenthPlan = PlanSelection::where('user_id', $user->id)->where('status', 'approved')->orderBy('created_at')->skip(13)->first();
                if ($fourteenthPlan && !\App\Models\CommissionTransaction::where('user_id', $user->id)->where('plan_selection_id', $fourteenthPlan->id)->where('commission_type', 'fourteenth_plan')->exists()) {
                    $result = $this->commissionService->processFourteenthPlanCommission($fourteenthPlan);
                    if ($result['success']) { $user->level = 14; $user->save(); $processed++; } else { $errors++; }
                }

                // Process Level 15 commission
                $fifteenthPlan = PlanSelection::where('user_id', $user->id)->where('status', 'approved')->orderBy('created_at')->skip(14)->first();
                if ($fifteenthPlan && !\App\Models\CommissionTransaction::where('user_id', $user->id)->where('plan_selection_id', $fifteenthPlan->id)->where('commission_type', 'fifteenth_plan')->exists()) {
                    $result = $this->commissionService->processFifteenthPlanCommission($fifteenthPlan);
                    if ($result['success']) { $user->level = 15; $user->save(); $processed++; } else { $errors++; }
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
