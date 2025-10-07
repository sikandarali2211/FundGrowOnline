<?php

namespace App\Services;

use App\Models\User;
use App\Models\PlanSelection;
use App\Models\GlobalPool;
use App\Models\CommissionTransaction;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    // Commission percentages
    const POOL_COMMISSION_PERCENTAGE = 60; // 60% to pool commission
    const PROFIT_COMMISSION_PERCENTAGE = 30; // 30% to profit (not used for L2 buyer)
    const GLOBAL_POOL_PERCENTAGE = 10; // 10% to global pool

    /**
     * Find the root user of the matrix that contains the given user
     */
    private function findMatrixRootUser(User $user): ?User
    {
        $currentUser = $user;
        $visited = []; // Prevent infinite loops
        
        while ($currentUser && !in_array($currentUser->id, $visited)) {
            $visited[] = $currentUser->id;
            
            // If this user has no referrer, they are the root
            if (!$currentUser->referred_by) {
                return $currentUser;
            }
            
            $referrer = User::find($currentUser->referred_by);
            
            // If referrer doesn't exist, current user is the root
            if (!$referrer) {
                return $currentUser;
            }
            
            // If referrer is not at level 2+, current user is the root of the matrix
            if ($referrer->level < 2) {
                return $currentUser;
            }
            
            $currentUser = $referrer;
        }
        
        // Fallback: return the user's direct referrer if we can't find a proper root
        return User::find($user->referred_by);
    }


    /**
     * Find the 2 parents (parent and grandparent) in the Level 2 matrix for a user
     */
    private function findMatrixParents(User $user): array
    {
        try {
            // Find the actual root user of the matrix that contains this user
            $rootUser = $this->findMatrixRootUser($user);
            
            if (!$rootUser) {
                return []; // No root user found
            }
            
            // Use direct matrix structure approach - build the matrix and find the user's actual placement
            $teamController = new TeamController();
            $matrixNodes = $teamController->buildLevel2ForMainUser($rootUser->id, 3);
            
            // Find the user's node in the matrix
            $userNodeId = "l2-{$user->id}";
            $userNode = null;
            
            foreach ($matrixNodes as $node) {
                if ($node['id'] === $userNodeId) {
                    $userNode = $node;
                    break;
                }
            }
            
            if (!$userNode) {
                Log::error('User not found in matrix - user may not be at level 2 yet', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_level' => $user->level,
                    'matrix_nodes_count' => count($matrixNodes),
                    'matrix_nodes' => array_map(function($n) { 
                        return [
                            'id' => $n['id'], 
                            'real_id' => $n['real_id'], 
                            'name' => $n['name']
                        ]; 
                    }, $matrixNodes)
                ]);
                
                // If user is not in matrix, they might not be at level 2 yet
                // Let's use the BFS logic to find where they would be placed
                return $this->findBFSMatrixParents($user, $rootUser);
            }
            
            // Find the parent and grandparent from the actual matrix structure
            $parent = null;
            $grandparent = null;
            
            Log::info('Matrix structure analysis', [
                'user_node' => $userNode,
                'user_parent_id' => $userNode['parentId'],
                'all_matrix_nodes' => array_map(function($n) { 
                    return [
                        'id' => $n['id'], 
                        'real_id' => $n['real_id'], 
                        'name' => $n['name'], 
                        'parentId' => $n['parentId']
                    ]; 
                }, $matrixNodes)
            ]);
            
            // Find parent node - the node whose id matches the user's parentId
            $parentNode = null;
            foreach ($matrixNodes as $node) {
                if ($node['id'] === $userNode['parentId']) {
                    $parentNode = $node;
                    $parent = User::find($node['real_id']);
                    break;
                }
            }
            
            Log::info('Parent node found', [
                'parent_node' => $parentNode,
                'parent_user' => $parent ? ['id' => $parent->id, 'name' => $parent->name] : null
            ]);
            
            // If parent is found, find grandparent
            if ($parentNode) {
                // Find the grandparent node - the node whose id matches the parent's parentId
                foreach ($matrixNodes as $node) {
                    if ($node['id'] === $parentNode['parentId']) {
                        $grandparent = User::find($node['real_id']);
                        break;
                    }
                }
            }
            
            Log::info('Grandparent found', [
                'grandparent_user' => $grandparent ? ['id' => $grandparent->id, 'name' => $grandparent->name] : null
            ]);
            
            $parents = [];
            
            // Add parent (if found)
            if ($parent) {
                $parents[] = $parent;
            }
            
            // Add grandparent (if found)
            if ($grandparent) {
                $parents[] = $grandparent;
            }
            
            Log::info('Matrix parents found using direct matrix structure', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'root_user_id' => $rootUser->id,
                'root_user_name' => $rootUser->name,
                'user_node' => $userNode,
                'parent_id' => $parent?->id,
                'parent_name' => $parent?->name,
                'grandparent_id' => $grandparent?->id,
                'grandparent_name' => $grandparent?->name,
                'parents_count' => count($parents),
                'parents' => array_map(function($p) { return ['id' => $p->id, 'name' => $p->name]; }, $parents)
            ]);
            
            return $parents;
            
        } catch (\Exception $e) {
            Log::error('Error finding matrix parents', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Find matrix parents using the exact same BFS logic as TeamController
     */
    private function findBFSMatrixParents(User $user, User $rootUser): array
    {
        try {
            // Get all users referred by the root user who are at level 2 or above
            // Also include the current user if they are reaching level 2
            $allReferredUsers = User::where('referred_by', $rootUser->id)
                ->where('level', '>=', 2)
                ->orderBy('created_at')
                ->get();
            
            // If the current user is not in the list (because they just reached level 2), add them
            $currentUserInList = $allReferredUsers->contains('id', $user->id);
            if (!$currentUserInList && $user->referred_by == $rootUser->id) {
                $allReferredUsers->push($user);
            }
            
            Log::info('BFS simulation starting', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'root_user_id' => $rootUser->id,
                'root_user_name' => $rootUser->name,
                'all_referred_users' => $allReferredUsers->pluck('name', 'id')->toArray()
            ]);
            
            // Use the exact same BFS logic as TeamController
            $childCount = [$rootUser->id => 0];
            $queue = [[$rootUser->id, 0]]; // (parentId, depth)
            $userParent = null;
            $userGrandparent = null;
            
            foreach ($allReferredUsers as $referredUser) {
                while (!empty($queue)) {
                    [$parentId, $depth] = $queue[0];
                    $count = $childCount[$parentId] ?? 0;
                    
                    if ($count < 3) {
                        // This is where the user would be placed
                        if ($referredUser->id == $user->id) {
                            Log::info('Found target user in BFS placement', [
                                'user_id' => $user->id,
                                'user_name' => $user->name,
                                'parent_id' => $parentId,
                                'depth' => $depth,
                                'root_user_id' => $rootUser->id
                            ]);
                            
                            // Found our user! Now find the parent and grandparent
                            if ($parentId == $rootUser->id) {
                                // User is directly under root user
                                $userParent = $rootUser; // Root user is the parent
                                $userGrandparent = null; // No grandparent for direct children
                                
                                Log::info('User placed directly under root', [
                                    'user_id' => $user->id,
                                    'parent_id' => $userParent->id,
                                    'parent_name' => $userParent->name
                                ]);
                            } else {
                                // Parent is not root user - this is the actual matrix parent
                                // The parentId is in format "l2-{user_id}", so we need to extract the user_id
                                $parentUserId = str_replace('l2-', '', $parentId);
                                $userParent = User::find($parentUserId);
                                
                                Log::info('User placed under child of root', [
                                    'user_id' => $user->id,
                                    'parent_id' => $parentId,
                                    'parent_user_id' => $parentUserId,
                                    'parent_name' => $userParent?->name,
                                    'parent_referred_by' => $userParent?->referred_by
                                ]);
                                
                                // Find the grandparent (parent of the parent)
                                if ($userParent && $userParent->referred_by) {
                                    $userGrandparent = User::find($userParent->referred_by);
                                } else {
                                    $userGrandparent = $rootUser; // Fallback to root user
                                }
                                
                                Log::info('Grandparent identified', [
                                    'grandparent_id' => $userGrandparent?->id,
                                    'grandparent_name' => $userGrandparent?->name
                                ]);
                            }
                            break 2; // Break out of both loops
                        }
                        
                        $childCount[$parentId] = $count + 1;
                        $childCount["l2-{$referredUser->id}"] = 0;
                        
                        // Only enqueue further if depth < 2
                        if ($depth < 2) {
                            $queue[] = ["l2-{$referredUser->id}", $depth + 1];
                        }
                        
                        break;
                    } else {
                        array_shift($queue);
                    }
                }
            }
            
            $parents = [];
            
            // Add parent (if found)
            if ($userParent) {
                $parents[] = $userParent;
            }
            
            // Add grandparent (if found)
            if ($userGrandparent) {
                $parents[] = $userGrandparent;
            }
            
            Log::info('BFS matrix parent finding result', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'root_user_id' => $rootUser->id,
                'root_user_name' => $rootUser->name,
                'user_parent_id' => $userParent?->id,
                'user_parent_name' => $userParent?->name,
                'user_grandparent_id' => $userGrandparent?->id,
                'user_grandparent_name' => $userGrandparent?->name,
                'parents_count' => count($parents)
            ]);
            
            return $parents;
            
        } catch (\Exception $e) {
            Log::error('Error in BFS matrix parent finding', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Process commission when user purchases second plan
     */
    public function processSecondPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // New L2 rule: Buyer gets NO commission on their own second plan.
            // Commission distribution (ONLY for Level 2+ referrals):
            // - Referrer gets 30% (60% pool commission, 40% pool wallet) - ONLY if user reaches Level 2+
            // - Matrix parents (parent and grandparent) get 30% each (60% pool commission, 40% pool wallet)
            // - Global Pool receives 10%
            // - User level is updated to 2
            // NOTE: Level 1 referrals remain completely untouched
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            // Find matrix parents (parent and grandparent in Level 2 matrix)
            $matrixParents = $this->findMatrixParents($user);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission,
                $matrixParents
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
                
                // Process commission for referrer (30% with 60/40 split) - ONLY for Level 2+ referrals
                // Level 1 referrals should remain completely untouched
                if ($user->referred_by && $user->level >= 2) {
                    $referrer = User::find($user->referred_by);
                    if ($referrer) {
                        $referrerCommission = $planAmount * 0.30; // 30% commission
                        $referrerPoolCommission = $referrerCommission * 0.60; // 60% to pool commission
                        $referrerPoolWallet = $referrerCommission * 0.40; // 40% to pool wallet
                        
                        // Update referrer's balances
                        $referrer->referral_commission_balance = ($referrer->referral_commission_balance ?? 0) + $referrerPoolCommission;
                        $referrer->referral_commission_pool = ($referrer->referral_commission_pool ?? 0) + $referrerPoolCommission;
                        $referrer->pool_wallet_amount = ($referrer->pool_wallet_amount ?? 0) + $referrerPoolWallet;
                        $referrer->total_commission_earned = ($referrer->total_commission_earned ?? 0) + $referrerCommission;
                        $referrer->save();
                        
                        // Record commission transaction for referrer
                        CommissionTransaction::create([
                            'user_id' => $referrer->id,
                            'plan_selection_id' => $planSelection->id,
                            'total_commission' => $referrerCommission,
                            'pool_commission' => $referrerPoolCommission,
                            'profit_commission' => 0,
                            'global_pool_commission' => 0,
                            'commission_type' => CommissionTransaction::TYPE_REFERRAL_CHAIN,
                            'description' => "Referrer commission from {$user->name}'s second plan (Level 2+)"
                        ]);
                    }
                }
                
                // Process commission for matrix parents (30% each)
                foreach ($matrixParents as $index => $parent) {
                    $parentCommission = $planAmount * 0.30; // 30% commission
                    $parentPoolCommission = $parentCommission * 0.60; // 60% to pool commission
                    $parentPoolWallet = $parentCommission * 0.40; // 40% to pool wallet
                    
                    // Update parent's balances
                    $parent->referral_commission_balance = ($parent->referral_commission_balance ?? 0) + $parentPoolCommission;
                    $parent->referral_commission_pool = ($parent->referral_commission_pool ?? 0) + $parentPoolCommission;
                    $parent->pool_wallet_amount = ($parent->pool_wallet_amount ?? 0) + $parentPoolWallet;
                    $parent->total_commission_earned = ($parent->total_commission_earned ?? 0) + $parentCommission;
                    $parent->save();
                    
                    // Record commission transaction for parent
                    CommissionTransaction::create([
                        'user_id' => $parent->id,
                        'plan_selection_id' => $planSelection->id,
                        'total_commission' => $parentCommission,
                        'pool_commission' => $parentPoolCommission,
                        'profit_commission' => 0,
                        'global_pool_commission' => 0,
                        'commission_type' => CommissionTransaction::TYPE_REFERRAL_CHAIN,
                        'description' => "Level 2 matrix " . ($index === 0 ? 'parent' : 'grandparent') . " commission from {$user->name}'s second plan"
                    ]);
                }
                
                Log::info('Second plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 2,
                    'referrer_id' => $user->referred_by,
                    'referrer_name' => $user->referred_by ? User::find($user->referred_by)?->name : null,
                    'matrix_root_user_id' => $this->findMatrixRootUser($user)?->id,
                    'matrix_root_user_name' => $this->findMatrixRootUser($user)?->name,
                    'matrix_parents_count' => count($matrixParents),
                    'matrix_parents' => array_map(function($p) { return ['id' => $p->id, 'name' => $p->name]; }, $matrixParents)
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
                    'new_level' => 2,
                    'referrer_id' => $user->referred_by,
                    'referrer_name' => $user->referred_by ? User::find($user->referred_by)?->name : null,
                    'matrix_root_user_id' => $this->findMatrixRootUser($user)?->id,
                    'matrix_root_user_name' => $this->findMatrixRootUser($user)?->name,
                    'matrix_parents_count' => count($matrixParents),
                    'matrix_parents' => array_map(function($p) { return ['id' => $p->id, 'name' => $p->name]; }, $matrixParents)
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
     * Process commission when user purchases third plan
     */
    public function processThirdPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L3 rule: Buyer gets NO commission on their own third plan.
            // Only Global Pool receives 10% and user level is updated to 3.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 3 (no commission to buyer)
                $user->level = 3;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for third plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_THIRD_PLAN,
                    'description' => "Third plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Third plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 3
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
                    'new_level' => 3
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process third plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases fourth plan
     */
    public function processFourthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L4 rule: Buyer gets NO commission on their own fourth plan.
            // Only Global Pool receives 10% and user level is updated to 4.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 4 (no commission to buyer)
                $user->level = 4;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for fourth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_FOURTH_PLAN,
                    'description' => "Fourth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Fourth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 4
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
                    'new_level' => 4
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process fourth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases sixth plan
     */
    public function processSixthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L6 rule: Buyer gets NO commission on their own sixth plan.
            // Only Global Pool receives 10% and user level is updated to 6.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 6 (no commission to buyer)
                $user->level = 6;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for sixth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_SIXTH_PLAN,
                    'description' => "Sixth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Sixth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 6
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
                    'new_level' => 6
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process sixth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases fifth plan
     */
    public function processFifthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L5 rule: Buyer gets NO commission on their own fifth plan.
            // Only Global Pool receives 10% and user level is updated to 5.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 5 (no commission to buyer)
                $user->level = 5;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for fifth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_FIFTH_PLAN,
                    'description' => "Fifth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Fifth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 5
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
                    'new_level' => 5
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process fifth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases seventh plan
     */
    public function processSeventhPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L7 rule: Buyer gets NO commission on their own seventh plan.
            // Only Global Pool receives 10% and user level is updated to 7.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 7 (no commission to buyer)
                $user->level = 7;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for seventh plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_SEVENTH_PLAN,
                    'description' => "Seventh plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Seventh plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 7
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
                    'new_level' => 7
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process seventh plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases eighth plan
     */
    public function processEighthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L8 rule: Buyer gets NO commission on their own eighth plan.
            // Only Global Pool receives 10% and user level is updated to 8.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 8 (no commission to buyer)
                $user->level = 8;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for eighth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_EIGHTH_PLAN,
                    'description' => "Eighth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Eighth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 8
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
                    'new_level' => 8
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process eighth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases ninth plan
     */
    public function processNinthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L9 rule: Buyer gets NO commission on their own ninth plan.
            // Only Global Pool receives 10% and user level is updated to 9.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 9 (no commission to buyer)
                $user->level = 9;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for ninth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_NINTH_PLAN,
                    'description' => "Ninth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Ninth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 9
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
                    'new_level' => 9
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process ninth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases tenth plan
     */
    public function processTenthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L10 rule: Buyer gets NO commission on their own tenth plan.
            // Only Global Pool receives 10% and user level is updated to 10.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 10 (no commission to buyer)
                $user->level = 10;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for tenth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_TENTH_PLAN,
                    'description' => "Tenth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Tenth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 10
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
                    'new_level' => 10
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process tenth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases eleventh plan
     */
    public function processEleventhPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L11 rule: Buyer gets NO commission on their own eleventh plan.
            // Only Global Pool receives 10% and user level is updated to 11.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 11 (no commission to buyer)
                $user->level = 11;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for eleventh plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_ELEVENTH_PLAN,
                    'description' => "Eleventh plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Eleventh plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 11
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
                    'new_level' => 11
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process eleventh plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases twelfth plan
     */
    public function processTwelfthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L12 rule: Buyer gets NO commission on their own twelfth plan.
            // Only Global Pool receives 10% and user level is updated to 12.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 12 (no commission to buyer)
                $user->level = 12;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for twelfth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_TWELFTH_PLAN,
                    'description' => "Twelfth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Twelfth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 12
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
                    'new_level' => 12
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process twelfth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases thirteenth plan
     */
    public function processThirteenthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            
            // L13 rule: Buyer gets NO commission on their own thirteenth plan.
            // Only Global Pool receives 10% and user level is updated to 13.
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use (
                $user, 
                $planSelection, 
                $planAmount, 
                $globalPoolCommission
            ) {
                // Update user level to 13 (no commission to buyer)
                $user->level = 13;
                $user->save();
                
                // Add to global pool
                GlobalPool::addCommission($globalPoolCommission);
                
                // Record only Global Pool contribution for thirteenth plan (no user credit)
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0, // no user commission
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_THIRTEENTH_PLAN,
                    'description' => "Thirteenth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Thirteenth plan commission processed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 13
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
                    'new_level' => 13
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to process thirteenth plan commission: ' . $e->getMessage(), [
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
     * Process commission when user purchases fourteenth plan
     */
    public function processFourteenthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use ($user, $planSelection, $planAmount, $globalPoolCommission) {
                $user->level = 14;
                $user->save();
                GlobalPool::addCommission($globalPoolCommission);
                
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_FOURTEENTH_PLAN,
                    'description' => "Fourteenth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
                
                Log::info('Fourteenth plan commission processed', [
                    'user_id' => $user->id,
                    'plan_amount' => $planAmount,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 14
                ]);
            });
            
            return [
                'success' => true,
                'data' => [
                    'user_id' => $user->id,
                    'plan_amount' => $planAmount,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'new_level' => 14
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processFifteenthPlanCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user;
            $planAmount = (float) $planSelection->plan_amount;
            $globalPoolCommission = $planAmount * (self::GLOBAL_POOL_PERCENTAGE / 100);
            
            DB::transaction(function () use ($user, $planSelection, $globalPoolCommission) {
                $user->level = 15;
                $user->save();
                GlobalPool::addCommission($globalPoolCommission);
                
                CommissionTransaction::create([
                    'user_id' => $user->id,
                    'plan_selection_id' => $planSelection->id,
                    'total_commission' => 0,
                    'pool_commission' => 0,
                    'profit_commission' => 0,
                    'global_pool_commission' => $globalPoolCommission,
                    'commission_type' => CommissionTransaction::TYPE_FIFTEENTH_PLAN,
                    'description' => "Fifteenth plan (buyer gets 0). Global pool credited. Plan: {$planSelection->plan_name}"
                ]);
            });
            
            return ['success' => true, 'data' => ['user_id' => $user->id, 'plan_amount' => $planAmount, 'pool_commission' => 0, 'profit_commission' => 0, 'global_pool_commission' => $globalPoolCommission, 'new_level' => 15]];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Process referral chain commission
     * When User B's referral buys plans 2-15, both User A and User B get 30% each
     */
    public function processReferralChainCommission(PlanSelection $planSelection): array
    {
        try {
            $user = $planSelection->user; // User who bought the plan
            $planAmount = (float) $planSelection->plan_amount;
            
            $results = [];
            
            // Apply for SECOND plan purchases of the buyer (30% each)
            if ($this->isSecondPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for second plan
                $levelDescription = "Level 2";
            }
            // Apply for THIRD plan purchases of the buyer (30% each)
            elseif ($this->isThirdPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for third plan
                $levelDescription = "Level 3";
            }
            // Apply for FOURTH plan purchases of the buyer (30% each)
            elseif ($this->isFourthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for fourth plan
                $levelDescription = "Level 4";
            }
            // Apply for FIFTH plan purchases of the buyer (30% each)
            elseif ($this->isFifthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for fifth plan
                $levelDescription = "Level 5";
            }
            // Apply for SIXTH plan purchases of the buyer (30% each)
            elseif ($this->isSixthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for sixth plan
                $levelDescription = "Level 6";
            }
            // Apply for SEVENTH plan purchases of the buyer (30% each)
            elseif ($this->isSeventhPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for seventh plan
                $levelDescription = "Level 7";
            }
            // Apply for EIGHTH plan purchases of the buyer (30% each)
            elseif ($this->isEighthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for eighth plan
                $levelDescription = "Level 8";
            }
            // Apply for NINTH plan purchases of the buyer (30% each)
            elseif ($this->isNinthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for ninth plan
                $levelDescription = "Level 9";
            }
            // Apply for TENTH plan purchases of the buyer (30% each)
            elseif ($this->isTenthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for tenth plan
                $levelDescription = "Level 10";
            }
            // Apply for ELEVENTH plan purchases of the buyer (30% each)
            elseif ($this->isEleventhPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for eleventh plan
                $levelDescription = "Level 11";
            }
            // Apply for TWELFTH plan purchases of the buyer (30% each)
            elseif ($this->isTwelfthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for twelfth plan
                $levelDescription = "Level 12";
            }
            // Apply for THIRTEENTH plan purchases of the buyer (30% each)
            elseif ($this->isThirteenthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for thirteenth plan
                $levelDescription = "Level 13";
            }
            // Apply for FOURTEENTH plan purchases of the buyer (30% each)
            elseif ($this->isFourteenthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for fourteenth plan
                $levelDescription = "Level 14";
            }
            // Apply for FIFTEENTH plan purchases of the buyer (30% each)
            elseif ($this->isFifteenthPlanPurchase($user)) {
                $commissionPercentage = 0.30; // 30% for fifteenth plan
                $levelDescription = "Level 15";
            }
            else {
                // For non-second through fifteenth plan purchases, do nothing here (Level 1 handled elsewhere)
                return [
                    'success' => true,
                    'message' => 'No referral chain commission applicable',
                    'data' => []
                ];
            }

            if (isset($commissionPercentage)) {
                // Get the referral chain: A1 -> A -> YOU
                $referralChain = $this->getReferralChain($user);
                
                if (count($referralChain) >= 1) {
                    // A1's immediate upline (A) gets commission based on plan level
                    $immediateUpline = $referralChain[0];
                    $immediateCommission = $planAmount * $commissionPercentage;
                    $immediatePoolCommission = $immediateCommission * 0.60;
                    $immediatePoolWallet = $immediateCommission * 0.40;

                    DB::transaction(function () use ($immediateUpline, $planSelection, $immediatePoolCommission, $immediatePoolWallet, $immediateCommission, $levelDescription) {
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
                            'description' => "{$levelDescription} immediate upline commission from {$planSelection->user->name}"
                        ]);
                    });

                    $results[] = [
                        'user_id' => $immediateUpline->id,
                        'user_name' => $immediateUpline->name,
                        'commission_amount' => $immediateCommission,
                        'level' => 'immediate_upline'
                    ];

                    // If there's a second level upline (YOU), they also get the same percentage
                    if (count($referralChain) >= 2) {
                        $secondUpline = $referralChain[1];
                        $secondCommission = $planAmount * $commissionPercentage;
                        $secondPoolCommission = $secondCommission * 0.60;
                        $secondPoolWallet = $secondCommission * 0.40;

                        DB::transaction(function () use ($secondUpline, $planSelection, $secondPoolCommission, $secondPoolWallet, $secondCommission, $levelDescription) {
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
                                'description' => "{$levelDescription} second upline commission from {$planSelection->user->name}"
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
                        'commission_percentage' => $commissionPercentage * 100,
                        'level_description' => $levelDescription,
                        'referral_chain_length' => count($referralChain),
                        'commissions_distributed' => count($results)
                    ]);
                }
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
     * Check if user is purchasing third plan
     */
    public function isThirdPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 3; // Third plan
    }

    /**
     * Check if user is purchasing fourth plan
     */
    public function isFourthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 4; // Fourth plan
    }

    /**
     * Check if user is purchasing fifth plan
     */
    public function isFifthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 5; // Fifth plan
    }

    /**
     * Check if user is purchasing sixth plan
     */
    public function isSixthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 6; // Sixth plan
    }

    /**
     * Check if user is purchasing seventh plan
     */
    public function isSeventhPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 7; // Seventh plan
    }

    /**
     * Check if user is purchasing eighth plan
     */
    public function isEighthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 8; // Eighth plan
    }

    /**
     * Check if user is purchasing ninth plan
     */
    public function isNinthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 9; // Ninth plan
    }

    /**
     * Check if user is purchasing tenth plan
     */
    public function isTenthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 10; // Tenth plan
    }

    /**
     * Check if user is purchasing eleventh plan
     */
    public function isEleventhPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 11; // Eleventh plan
    }

    /**
     * Check if user is purchasing twelfth plan
     */
    public function isTwelfthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 12; // Twelfth plan
    }

    /**
     * Check if user is purchasing thirteenth plan
     */
    public function isThirteenthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 13; // Thirteenth plan
    }

    public function isFourteenthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 14; // Fourteenth plan
    }

    public function isFifteenthPlanPurchase(User $user): bool
    {
        $planCount = PlanSelection::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
            
        return $planCount === 15; // Fifteenth plan
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
        
        // Check if this is a third plan purchase
        if ($this->isThirdPlanPurchase($planSelection->user)) {
            // Process third plan commission
            $thirdPlanResult = $this->processThirdPlanCommission($planSelection);
            $results['third_plan'] = $thirdPlanResult;
        }
        
        // Check if this is a fourth plan purchase
        if ($this->isFourthPlanPurchase($planSelection->user)) {
            // Process fourth plan commission
            $fourthPlanResult = $this->processFourthPlanCommission($planSelection);
            $results['fourth_plan'] = $fourthPlanResult;
        }
        
        // Check if this is a fifth plan purchase
        if ($this->isFifthPlanPurchase($planSelection->user)) {
            // Process fifth plan commission
            $fifthPlanResult = $this->processFifthPlanCommission($planSelection);
            $results['fifth_plan'] = $fifthPlanResult;
        }
        
        // Check if this is a sixth plan purchase
        if ($this->isSixthPlanPurchase($planSelection->user)) {
            // Process sixth plan commission
            $sixthPlanResult = $this->processSixthPlanCommission($planSelection);
            $results['sixth_plan'] = $sixthPlanResult;
        }
        
        // Check if this is a seventh plan purchase
        if ($this->isSeventhPlanPurchase($planSelection->user)) {
            // Process seventh plan commission
            $seventhPlanResult = $this->processSeventhPlanCommission($planSelection);
            $results['seventh_plan'] = $seventhPlanResult;
        }
        
        // Check if this is an eighth plan purchase
        if ($this->isEighthPlanPurchase($planSelection->user)) {
            // Process eighth plan commission
            $eighthPlanResult = $this->processEighthPlanCommission($planSelection);
            $results['eighth_plan'] = $eighthPlanResult;
        }
        
        // Check if this is a ninth plan purchase
        if ($this->isNinthPlanPurchase($planSelection->user)) {
            // Process ninth plan commission
            $ninthPlanResult = $this->processNinthPlanCommission($planSelection);
            $results['ninth_plan'] = $ninthPlanResult;
        }
        
        // Check if this is a tenth plan purchase
        if ($this->isTenthPlanPurchase($planSelection->user)) {
            // Process tenth plan commission
            $tenthPlanResult = $this->processTenthPlanCommission($planSelection);
            $results['tenth_plan'] = $tenthPlanResult;
        }
        
        // Check if this is an eleventh plan purchase
        if ($this->isEleventhPlanPurchase($planSelection->user)) {
            // Process eleventh plan commission
            $eleventhPlanResult = $this->processEleventhPlanCommission($planSelection);
            $results['eleventh_plan'] = $eleventhPlanResult;
        }
        
        // Check if this is a twelfth plan purchase
        if ($this->isTwelfthPlanPurchase($planSelection->user)) {
            // Process twelfth plan commission
            $twelfthPlanResult = $this->processTwelfthPlanCommission($planSelection);
            $results['twelfth_plan'] = $twelfthPlanResult;
        }
        
        // Check if this is a thirteenth plan purchase
        if ($this->isThirteenthPlanPurchase($planSelection->user)) {
            $thirteenthPlanResult = $this->processThirteenthPlanCommission($planSelection);
            $results['thirteenth_plan'] = $thirteenthPlanResult;
        }
        
        // Check if this is a fourteenth plan purchase
        if ($this->isFourteenthPlanPurchase($planSelection->user)) {
            $fourteenthPlanResult = $this->processFourteenthPlanCommission($planSelection);
            $results['fourteenth_plan'] = $fourteenthPlanResult;
        }
        
        // Check if this is a fifteenth plan purchase
        if ($this->isFifteenthPlanPurchase($planSelection->user)) {
            $fifteenthPlanResult = $this->processFifteenthPlanCommission($planSelection);
            $results['fifteenth_plan'] = $fifteenthPlanResult;
        }
        
        // Always process referral chain commission
        $referralChainResult = $this->processReferralChainCommission($planSelection);
        $results['referral_chain'] = $referralChainResult;
        
        return $results;
    }
}
