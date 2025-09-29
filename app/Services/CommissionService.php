<?php

namespace App\Services;

use App\Models\User;
use App\Models\PlanSelection;
use App\Models\GlobalPool;
use App\Models\CommissionTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    // Commission percentages
    const POOL_COMMISSION_PERCENTAGE = 60; // 60% to pool commission
    const PROFIT_COMMISSION_PERCENTAGE = 30; // 30% to profit (not used for L2 buyer)
    const GLOBAL_POOL_PERCENTAGE = 10; // 10% to global pool

    /**
     * Process commission when user purchases second plan
     */
    public function processSecondPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // New L2 rule: Buyer gets NO commission on their own second plan.
            // Only Global Pool receives 10% and user level is updated to 2.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 2 (no commission to buyer)
                $user->level = 2;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for second plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_SECOND_PLAN,
                    'description' => "Second plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Second plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 2
                ]);
            });
            
            return [
                'success' => true,
                'message' => 'Commission processed successfully',
                'data' => [
                    'user_id' => $user->id,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 2
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process second plan commission: ' . $e->getMessage(), [
                'plan_selection_id' => $planSelection->id,
                'user_id' => $planSelection->user_id
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process referral chain commission
     * When User B's referral buys second plan, both User A and User B get 30% each
     */
    public function processReferralChainCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user; // User who bought the plan
            $planAmount = (float) $planSelection->plan_amount;
            
            $results = [];
            
            // Apply only for SECOND plan purchases of the buyer
            if ($this->isSecondPlanPurchase($user)) {
                // Get the referral chain: A1 -> A -> YOU
                $referralChain = $this->getReferralChain($user);
                
                if (count($referralChain) >= 1) {
                    // A1's immediate upline (A) gets 30%
                    $immediateUpline = $referralChain[0];
                    $immediateCommission = $planAmount * 0.30;
                    $immediatePoolCommission = $immediateCommission * 0.60;
                    $immediatePoolWallet = $immediateCommission * 0.40;

                    DB::transaction(function () use ($immediateUpline, $planSelection, $immediatePoolCommission, $immediatePoolWallet, $immediateCommission) {
                        $immediateUpline->referral_commission_balance = ($immediateUpline->referral_commission_balance ?? 0) + $immediatePoolCommission;
                        $immediateUpline->referral_commission_pool = ($immediateUpline->referral_commission_pool ?? 0) + $immediatePoolCommission;
                        $immediateUpline->pool_wallet_amount = ($immediateUpline->pool_wallet_amount ?? 0) + $immediatePoolWallet;
                        $immediateUpline->total_commission_earned = ($immediateUpline->total_commission_earned ?? 0) + $immediateCommission;
                        $immediateUpline->save();

                        CommissionTransaction::create([
                            'user_id' => $immediateUpline->id,
                            'plan_selection_id' => $planSelection->id,
                            'total_commission' => $immediateCommission,
                            'pool_commission' => $immediatePoolCommission,
                            'profit_commission' => 0,
                            'global_pool_commission' => 0,
                            'commission_type' => CommissionTransaction::TYPE_REFERRAL_CHAIN,
                            'description' => "Level 3 immediate upline commission from {$planSelection->user->name}"
                        ]);
                    });

                    $results[] = [
                        'user_id' => $immediateUpline->id,
                        'user_name' => $immediateUpline->name,
                        'commission_amount' => $immediateCommission,
                        'level' => 'immediate_upline'
                    ];

                    // If there's a second level upline (YOU), they also get 30%
                    if (count($referralChain) >= 2) {
                        $secondUpline = $referralChain[1];
                        $secondCommission = $planAmount * 0.30;
                        $secondPoolCommission = $secondCommission * 0.60;
                        $secondPoolWallet = $secondCommission * 0.40;

                        DB::transaction(function () use ($secondUpline, $planSelection, $secondPoolCommission, $secondPoolWallet, $secondCommission) {
                            $secondUpline->referral_commission_balance = ($secondUpline->referral_commission_balance ?? 0) + $secondPoolCommission;
                            $secondUpline->referral_commission_pool = ($secondUpline->referral_commission_pool ?? 0) + $secondPoolCommission;
                            $secondUpline->pool_wallet_amount = ($secondUpline->pool_wallet_amount ?? 0) + $secondPoolWallet;
                            $secondUpline->total_commission_earned = ($secondUpline->total_commission_earned ?? 0) + $secondCommission;
                            $secondUpline->save();

                            CommissionTransaction::create([
                                'user_id' => $secondUpline->id,
                                'plan_selection_id' => $planSelection->id,
                                'total_commission' => $secondCommission,
                                'pool_commission' => $secondPoolCommission,
                                'profit_commission' => 0,
                                'global_pool_commission' => 0,
                                'commission_type' => CommissionTransaction::TYPE_REFERRAL_CHAIN,
                                'description' => "Level 3 second upline commission from {$planSelection->user->name}"
                            ]);
                        });

                        $results[] = [
                            'user_id' => $secondUpline->id,
                            'user_name' => $secondUpline->name,
                            'commission_amount' => $secondCommission,
                            'level' => 'second_upline'
                        ];
                    }

                    Log::info('Multi-level referral chain commission processed', [
                        'buyer_user_id' => $user->id,
                        'buyer_user_name' => $user->name,
                        'plan_amount' => $planAmount,
                        'referral_chain_length' => count($referralChain),
                        'commissions_distributed' => count($results)
                    ]);
                }
            } else {
                // For non-second plan purchases, do nothing here (Level 1 handled elsewhere)
            }
            
            return [
                'success' => true,
                'message' => 'Referral/upline commission processed successfully',
                'data' => $results
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process referral chain commission: ' . $e->getMessage(), [
                'plan_selection_id' => $planSelection->id,
                'user_id' => $planSelection->user_id
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get referral chain (up to 2 levels up)
     */
    private function getReferralChain(User $user): array
    {
        $chain = [];
        $currentUser = $user;
        
        // Go up to 2 levels in the referral chain
        for ($i = 0; $i < 2; $i++) {
            if ($currentUser->referred_by) {
                $referrer = User::find($currentUser->referred_by);
                if ($referrer) {
                    $chain[] = $referrer;
                    $currentUser = $referrer;
                } else {
                    break;
                }
            } else {
                break;
            }
        }
        
        return $chain;
    }

    /**
     * Check if user is purchasing second plan
     */
    public function isSecondPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 2; // Second plan
    }

    /**
     * Get commission statistics for a user
     */
    public function getUserCommissionStats(User $user): array
    {
        $commissionTransactions = CommissionTransaction::where('user_id', $user->id)->get();
        
        return [
            'total_commissions' => $commissionTransactions->count(),
            'total_pool_commission' => $commissionTransactions->sum('pool_commission'),
            'total_profit_commission' => $commissionTransactions->sum('profit_commission'),
            'total_global_pool_commission' => $commissionTransactions->sum('global_pool_commission'),
            'recent_transactions' => $commissionTransactions->take(5)
        ];
    }

    /**
     * Get global pool statistics
     */
    public function getGlobalPoolStats(): array
    {
        return GlobalPool::getStatistics();
    }

    /**
     * Process all commissions for a plan purchase
     */
    public function processAllCommissions(PlanSelection $planSelection): array
    {
        $results = [];
        
        // Check if this is a second plan purchase
        if ($this->isSecondPlanPurchase($planSelection->user)) {
            // Process second plan commission
            $secondPlanResult = $this->processSecondPlanCommission($planSelection);
            $results['second_plan'] = $secondPlanResult;
        }
        
        // Always process referral chain commission
        $referralChainResult = $this->processReferralChainCommission($planSelection);
        $results['referral_chain'] = $referralChainResult;
        
        return $results;
    }
}
