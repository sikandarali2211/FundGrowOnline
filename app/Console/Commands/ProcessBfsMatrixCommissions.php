<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlanSelection;
use App\Models\GlobalPool;
use App\Models\CommissionTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBfsMatrixCommissions extends Command
{
    protected $signature = 'commissions:process-bfs-matrix {--dry-run : Show what would be processed without making changes}';
    protected $description = 'Process BFS matrix commissions for parents with 3 max children';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🌳 Processing BFS Matrix Commissions...');
        
        // Get all users who have 3 children in the BFS matrix
        $parentsWithFullMatrix = $this->getParentsWithFullMatrix();
        
        if (empty($parentsWithFullMatrix)) {
            $this->info('No parents found with full BFS matrix (3 children)');
            return Command::SUCCESS;
        }

        $this->info("Found " . count($parentsWithFullMatrix) . " parents with full BFS matrix");

        $totalProcessed = 0;
        $totalCommissions = 0;

        foreach ($parentsWithFullMatrix as $parentData) {
            $parent = $parentData['parent'];
            $child = $parentData['child'];
            $commissionAmount = $parentData['commission_amount'];
            $phase = $parentData['phase'];
            $position = $parentData['position'];

            $this->info("Processing parent: {$parent->name} (ID: {$parent->id})");
            $this->info("Child: {$child->name} (Phase {$phase}, Position {$position})");
            $this->info("Commission amount: $" . number_format($commissionAmount, 2));

            if (!$dryRun) {
                $result = $this->distributeBfsCommission($parent, $child, $commissionAmount, $phase, $position);
                if ($result['success']) {
                    if (!($result['skipped'] ?? false)) {
                        $totalProcessed++;
                        $totalCommissions += $commissionAmount;
                        $this->info("✅ Commission distributed successfully");
                    } else {
                        $this->info("⏭️  Skipped: " . ($result['message'] ?? 'Not eligible'));
                    }
                } else {
                    $this->error("❌ Failed to distribute commission: " . $result['message']);
                }
            } else {
                $totalProcessed++;
                $totalCommissions += $commissionAmount;
                $this->info("✅ Would distribute commission");
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("Parents processed: {$totalProcessed}");
        $this->info("Total commissions: $" . number_format($totalCommissions, 2));

        return Command::SUCCESS;
    }

    private function getParentsWithFullMatrix(): array
    {
        $parents = [];

        // Get all users who are Level 2 (have 2+ approved plans) OR Level 3 (have 3+ approved plans)
        $level2Users = User::where('level', 2)
            ->whereHas('planSelections', function($q) {
                $q->where('status', 'approved');
            }, '>=', 2)
            ->get();

        $level3Users = User::where('level', 3)
            ->whereHas('planSelections', function($q) {
                $q->where('status', 'approved');
            }, '>=', 3)
            ->get();

        $allUsers = $level2Users->merge($level3Users);

        foreach ($allUsers as $user) {
            // Get all direct referrals of this user
            $directReferrals = User::where('referred_by', $user->id)
                ->whereHas('planSelections', function($q) {
                    $q->where('status', 'approved');
                })
                ->get();

            // Check if this user has exactly 3 children in BFS matrix (Phase 1)
            if ($directReferrals->count() >= 3) {
                // Process Phase 1: 3 direct children
                $phase1Children = $directReferrals->take(3);
                foreach ($phase1Children as $index => $child) {
                    $childPlan = $child->planSelections()
                        ->where('status', 'approved')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($childPlan) {
                        $commissionAmount = (float) $childPlan->plan_amount;
                        
                        $parents[] = [
                            'parent' => $user,
                            'child' => $child,
                            'commission_amount' => $commissionAmount,
                            'trigger_plan' => $childPlan,
                            'phase' => 1,
                            'position' => $index + 1
                        ];
                    }
                }

                // Process Phase 2: Each of the 3 children's children
                foreach ($phase1Children as $phase1Child) {
                    $phase2Children = User::where('referred_by', $phase1Child->id)
                        ->whereHas('planSelections', function($q) {
                            $q->where('status', 'approved');
                        })
                        ->get();

                    if ($phase2Children->count() >= 3) {
                        $phase2Children = $phase2Children->take(3);
                        foreach ($phase2Children as $index => $child) {
                            $childPlan = $child->planSelections()
                                ->where('status', 'approved')
                                ->orderBy('created_at', 'desc')
                                ->first();

                            if ($childPlan) {
                                $commissionAmount = (float) $childPlan->plan_amount;
                                
                                $parents[] = [
                                    'parent' => $user,
                                    'child' => $child,
                                    'commission_amount' => $commissionAmount,
                                    'trigger_plan' => $childPlan,
                                    'phase' => 2,
                                    'position' => $index + 1,
                                    'phase1_parent' => $phase1Child
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $parents;
    }

    private function distributeBfsCommission(User $parent, $child, float $commissionAmount, int $phase, int $position): array
    {
        try {
            DB::beginTransaction();

            // Abdullah (main parent) gets 30% commission on each placement
            // No need to check uplines - Abdullah is the main parent getting commission

            // Calculate 30% commission for Abdullah (main parent)
            $parentCommission = $commissionAmount * 0.30;
            $poolCommission = $parentCommission * 0.60;
            $poolWallet = $parentCommission * 0.40;

            // Distribute to Abdullah (main parent)
            $parent->referral_commission_balance = ($parent->referral_commission_balance ?? 0) + $poolCommission;
            $parent->referral_commission_pool = ($parent->referral_commission_pool ?? 0) + $poolCommission;
            $parent->pool_wallet_amount = ($parent->pool_wallet_amount ?? 0) + $poolWallet;
            $parent->total_commission_earned = ($parent->total_commission_earned ?? 0) + $parentCommission;
            $parent->save();

            // No global pool addition in this cron

            // Create commission transaction record
            $triggerPlan = $child->planSelections()
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->first();

            CommissionTransaction::create([
                'user_id' => $parent->id,
                'plan_selection_id' => $triggerPlan->id,
                'total_commission' => $parentCommission,
                'pool_commission' => $poolCommission,
                'profit_commission' => 0,
                'global_pool_commission' => 0,
                'commission_type' => 'bfs_matrix',
                'description' => "BFS Matrix commission - Phase {$phase}, Position {$position} from {$child->name}'s plan"
            ]);

            // No global pool transaction record

            DB::commit();

            Log::info('BFS Matrix commission distributed', [
                'parent_id' => $parent->id,
                'parent_name' => $parent->name,
                'child_id' => $child->id,
                'child_name' => $child->name,
                'phase' => $phase,
                'position' => $position,
                'commission_amount' => $commissionAmount,
                'parent_commission' => $parentCommission
            ]);

            return [
                'success' => true,
                'message' => 'Commission distributed successfully',
                'parent' => $parent->name,
                'child' => $child->name,
                'phase' => $phase,
                'position' => $position,
                'commission_amount' => $commissionAmount
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BFS Matrix commission distribution failed', [
                'parent_id' => $parent->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to distribute commission: ' . $e->getMessage()
            ];
        }
    }

    private function getReferralChain(User $user): array
    {
        $chain = [];
        $currentUser = $user;

        while ($currentUser && $currentUser->referred_by) {
            $parent = User::find($currentUser->referred_by);
            if ($parent) {
                $chain[] = $parent;
                $currentUser = $parent;
            } else {
                break;
            }
        }

        return $chain;
    }
}
