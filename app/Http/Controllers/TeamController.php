<?php

namespace App\Http\Controllers;

use App\Models\User;

class TeamController extends Controller
{
    private function buildMatrix($users, $rootId, $maxChildren = 3, $type = 'l2')
    {
        $nodes = [];
        $childCount = [$rootId => 0];

        // Each queue item carries (parentId, depth)
        $queue = [[$rootId, 0]]; 

        foreach ($users as $user) {
            while (!empty($queue)) {
                [$parentId, $depth] = $queue[0]; // peek

                $count = $childCount[$parentId] ?? 0;

                if ($count < $maxChildren) {
                    $id = "{$type}-{$user->id}";
                    $nodes[] = [
                        'id'       => $id,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => $type,
                        'parentId' => $parentId,
                    ];

                    // update counts
                    $childCount[$parentId] = $count + 1;
                    $childCount[$id] = 0;

                    // only enqueue further if depth < 1 (root=0, children=1 → grandchildren=2 max)
                    if ($depth < 1) {
                        $queue[] = [$id, $depth + 1];
                    }

                    break;
                } else {
                    array_shift($queue); // parent full, pop
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 2 matrix for MAIN/ROOT user only
     * Places children and their referrals in the tree (children's children)
     * Does NOT place grandchildren's referrals (children's grandchildren)
     */
    public function buildLevel2ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        
        // Simple approach: Get first 12 Level 2+ users and place them in 1-3-9 structure
        // Exclude the main user (first user in database) from being placed in anyone's matrix
        $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 2)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id) // Exclude main user from BFS matrix
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 2nd plan (2nd approved plan selection)
                $secondPlan = $user->planSelections->skip(1)->first();
                return $secondPlan ? $secondPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        // Simple placement: First 3 under root, next 9 under the first 3
        $userIndex = 0;
        
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
                    $userId = "l2-{$user->id}";
                    
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l2',
                'parentId' => $rootUserId,
            ];
            
            $userIndex++;
        }
        
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l2-{$allReferredUsers[$parentIndex]->id}";
            
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                        $userId = "l2-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l2',
                    'parentId' => $parentUserId,
                ];
                
                $userIndex++;
            }
        }
        
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l2-{$allReferredUsers[$grandparentIndex]->id}";
            
            // Get the 3 children of this grandparent
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l2-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                
                // Place 3 users under each child (grandchildren)
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                        $userId = "l2-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l2',
                        'parentId' => $childUserId,
                    ];
                    
                    $userIndex++;
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 3 matrix for MAIN/ROOT user only - EXACTLY like Level 2
     * Places children and their referrals in the tree (children's children)
     * Does NOT place grandchildren's referrals (children's grandchildren)
     */
    public function buildLevel3ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        
        // Simple approach: Get first 12 Level 3+ users and place them in 1-3-9 structure
        // Exclude the main user (first user in database) from being placed in anyone's matrix
        $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 3)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id) // Exclude main user from BFS matrix
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 3rd plan (3rd approved plan selection)
                $thirdPlan = $user->planSelections->skip(2)->first();
                return $thirdPlan ? $thirdPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        // Simple placement: First 3 under root, next 9 under the first 3
        $userIndex = 0;
        
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
                    $userId = "l3-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l3',
                'parentId' => $rootUserId,
            ];
            
            $userIndex++;
        }
        
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l3-{$allReferredUsers[$parentIndex]->id}";
            
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                        $userId = "l3-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l3',
                    'parentId' => $parentUserId,
                ];
                
                $userIndex++;
            }
        }
        
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l3-{$allReferredUsers[$grandparentIndex]->id}";
            
            // Get the 3 children of this grandparent
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l3-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                
                // Place 3 users under each child (grandchildren)
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                            $userId = "l3-{$user->id}";
                            
                            $nodes[] = [
                                'id'       => $userId,
                                'real_id'  => $user->id,
                                'name'     => $user->name,
                                'code'     => $user->referral_code,
                                'joined'   => $user->created_at->format('M d, Y'),
                                'type'     => 'l3',
                        'parentId' => $childUserId,
                    ];
                    
                    $userIndex++;
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 4 matrix for MAIN/ROOT user only - EXACTLY like Level 2 and 3
     * Places children and their referrals in the tree (children's children)
     * Does NOT place grandchildren's referrals (children's grandchildren)
     */
    public function buildLevel4ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        
        // Simple approach: Get first 12 Level 4+ users and place them in 1-3-9 structure
        // Exclude the main user (first user in database) from being placed in anyone's matrix
        $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 4)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id) // Exclude main user from BFS matrix
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 4th plan (4th approved plan selection)
                $fourthPlan = $user->planSelections->skip(3)->first();
                return $fourthPlan ? $fourthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        // Simple placement: First 3 under root, next 9 under the first 3
        $userIndex = 0;
        
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
                    $userId = "l4-{$user->id}";
                    
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l4',
                'parentId' => $rootUserId,
            ];
            
            $userIndex++;
        }
        
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l4-{$allReferredUsers[$parentIndex]->id}";
            
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                        $userId = "l4-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l4',
                    'parentId' => $parentUserId,
                ];
                
                $userIndex++;
            }
        }
        
        // Place next 27 users under the 9 children (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l4-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                            $userId = "l4-{$user->id}";
                            
                            $nodes[] = [
                                'id'       => $userId,
                                'real_id'  => $user->id,
                                'name'     => $user->name,
                                'code'     => $user->referral_code,
                                'joined'   => $user->created_at->format('M d, Y'),
                                'type'     => 'l4',
                        'parentId' => $childUserId,
                    ];
                    
                    $userIndex++;
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 3 tree for CHILD user (when they view their own dashboard) - EXACTLY like Level 2
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel3TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel3ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l3-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 3 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 3 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 4 tree for CHILD user (when they view their own dashboard) - EXACTLY like Level 2 and 3
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel4TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel4ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l4-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 4 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 4 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 5 matrix for MAIN/ROOT user only - EXACTLY like Level 2, 3 and 4
     * Places children and their referrals in the tree (children's children)
     * Does NOT place grandchildren's referrals (children's grandchildren)
     */
    public function buildLevel5ForMainUser($rootUserId, $maxChildren = 3)
    {
            $nodes = [];
        
        // EXACTLY like Level 2: Get first 40 Level 5+ users and place them in 1-3-9-27 structure
        // Exclude the main user (first user in database) from being placed in anyone's matrix
        $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 5)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id) // Exclude main user from BFS matrix
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 5th plan (5th approved plan selection)
                $fifthPlan = $user->planSelections->skip(4)->first();
                return $fifthPlan ? $fifthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        // Simple placement: First 3 under root, next 9 under the first 3, next 27 under the 9
        $userIndex = 0;
        
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
                    $userId = "l5-{$user->id}";
                            
                            $nodes[] = [
                                'id'       => $userId,
                                'real_id'  => $user->id,
                                'name'     => $user->name,
                                'code'     => $user->referral_code,
                                'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l5',
                'parentId' => $rootUserId,
            ];
            
            $userIndex++;
        }
        
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l5-{$allReferredUsers[$parentIndex]->id}";
            
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                        $userId = "l5-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l5',
                    'parentId' => $parentUserId,
                ];
                
                $userIndex++;
            }
        }
        
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l5-{$allReferredUsers[$grandparentIndex]->id}";
            
            // Get the 3 children of this grandparent
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l5-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                
                // Place 3 users under each child (grandchildren)
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                            $userId = "l5-{$user->id}";
                            
                            $nodes[] = [
                                'id'       => $userId,
                                'real_id'  => $user->id,
                                'name'     => $user->name,
                                'code'     => $user->referral_code,
                                'joined'   => $user->created_at->format('M d, Y'),
                                'type'     => 'l5',
                        'parentId' => $childUserId,
                    ];
                    
                    $userIndex++;
                    }
                }
            }

            return $nodes;
        }
        
    /**
     * Build Level 5 tree for CHILD user (when they view their own dashboard) - EXACTLY like Level 2, 3 and 4
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel5TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel5ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l5-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 5 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 5 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 6 matrix for MAIN/ROOT user only - EXACTLY like Level 2, 3, 4 and 5
     * Places children and their referrals in the tree (children's children)
     * Does NOT place grandchildren's referrals (children's grandchildren)
     */
    public function buildLevel6ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 6)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id) // Exclude main user from BFS matrix
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 6th plan (6th approved plan selection)
                $sixthPlan = $user->planSelections->skip(5)->first();
                return $sixthPlan ? $sixthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l6-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l6',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l6-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l6-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l6',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l6-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l6-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l6-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l6',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 6 tree for CHILD user (when they view their own dashboard) - EXACTLY like Level 2, 3, 4 and 5
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel6TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel6ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l6-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 6 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 6 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 7 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6)
     */
    public function buildLevel7ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 7)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 7th plan (7th approved plan selection)
                $seventhPlan = $user->planSelections->skip(6)->first();
                return $seventhPlan ? $seventhPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l7-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l7',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l7-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l7-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l7',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l7-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l7-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l7-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l7',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 7 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6)
     */
    private function buildLevel7TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel7ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l7-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 7 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 7 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 8 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7)
     */
    public function buildLevel8ForMainUser($rootUserId, $maxChildren = 3)
    {
            $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 8)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 8th plan (8th approved plan selection)
                $eighthPlan = $user->planSelections->skip(7)->first();
                return $eighthPlan ? $eighthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l8-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l8',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l8-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l8-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l8',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l8-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l8-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l8-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l8',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 8 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7)
     */
    private function buildLevel8TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel8ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l8-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 8 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 8 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 9 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8)
     */
    public function buildLevel9ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 9)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 9th plan (9th approved plan selection)
                $ninthPlan = $user->planSelections->skip(8)->first();
                return $ninthPlan ? $ninthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l9-{$user->id}";
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l9',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l9-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l9-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l9',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l9-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l9-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l9-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l9',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 9 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8)
     */
    private function buildLevel9TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel9ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l9-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 9 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 9 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 10 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9)
     */
    public function buildLevel10ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 10)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 10th plan (10th approved plan selection)
                $tenthPlan = $user->planSelections->skip(9)->first();
                return $tenthPlan ? $tenthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l10-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l10',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l10-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l10-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l10',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l10-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l10-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l10-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l10',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
            return $nodes;
        }
        
    /**
     * Build Level 10 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9)
     */
    private function buildLevel10TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel10ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l10-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 10 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 10 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 11 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10)
     */
    public function buildLevel11ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 11)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 11th plan (11th approved plan selection)
                $eleventhPlan = $user->planSelections->skip(10)->first();
                return $eleventhPlan ? $eleventhPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l11-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l11',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l11-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l11-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l11',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l11-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l11-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l11-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l11',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 11 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10)
     */
    private function buildLevel11TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel11ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l11-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 11 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 11 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 12 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
     */
    public function buildLevel12ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 12)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 12th plan (12th approved plan selection)
                $twelfthPlan = $user->planSelections->skip(11)->first();
                return $twelfthPlan ? $twelfthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l12-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l12',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l12-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l12-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l12',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l12-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l12-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l12-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l12',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 12 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
     */
    private function buildLevel12TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel12ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l12-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 12 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 12 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Enhance the descendant nodes to make childUserId the root
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 13 BFS matrix for main user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)
     */
    public function buildLevel13ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 13)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 13th plan (13th approved plan selection)
                $thirteenthPlan = $user->planSelections->skip(12)->first();
                return $thirteenthPlan ? $thirteenthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l13-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l13',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l13-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l13-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l13',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l13-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l13-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l13-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l13',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 13 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)
     * Shows children that the child user has inside the main user's BFS matrix
     */
    private function buildLevel13TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel13ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l13-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 13 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 13 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 14 tree for MAIN user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13)
     */
    public function buildLevel14ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 14)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 14th plan (14th approved plan selection)
                $fourteenthPlan = $user->planSelections->skip(13)->first();
                return $fourteenthPlan ? $fourteenthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l14-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l14',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l14-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l14-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l14',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l14-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l14-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l14-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l14',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 14 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13)
     * Shows children that the child user has inside the main user's BFS matrix
     */
    private function buildLevel14TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel14ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l14-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 14 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 14 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 15 tree for MAIN user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14)
     */
    public function buildLevel15ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $mainUser = User::orderBy('created_at')->first();
        
        $allReferredUsers = User::whereHas('planSelections', fn($q) => $q->where('status', 'approved'), '>=', 15)
            ->where('id', '!=', $rootUserId)
            ->where('id', '!=', $mainUser->id)
            ->with(['planSelections' => function($query) {
                $query->where('status', 'approved')->orderBy('created_at');
            }])
            ->get()
            ->sortBy(function($user) {
                // Sort by when they bought their 15th plan (15th approved plan selection)
                $fifteenthPlan = $user->planSelections->skip(14)->first();
                return $fifteenthPlan ? $fifteenthPlan->created_at : $user->created_at;
            })
            ->values() // Reset array keys after sorting
            ->take(40); // Limit to 40 users

        $userIndex = 0;
        // Place first 3 users directly under root
        for ($i = 0; $i < 3 && $userIndex < count($allReferredUsers); $i++) {
            $user = $allReferredUsers[$userIndex];
            $userId = "l15-{$user->id}";
            $nodes[] = [
                'id'       => $userId,
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l15',
                'parentId' => $rootUserId,
            ];
            $userIndex++;
        }
        // Place next 9 users under the first 3 (3 under each)
        for ($parentIndex = 0; $parentIndex < 3 && $userIndex < count($allReferredUsers); $parentIndex++) {
            $parentUserId = "l15-{$allReferredUsers[$parentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $user = $allReferredUsers[$userIndex];
                $userId = "l15-{$user->id}";
                $nodes[] = [
                    'id'       => $userId,
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l15',
                    'parentId' => $parentUserId,
                ];
                $userIndex++;
            }
        }
        // Place next 27 users under the 9 grandchildren (3 under each)
        for ($grandparentIndex = 0; $grandparentIndex < 3 && $userIndex < count($allReferredUsers); $grandparentIndex++) {
            $grandparentUserId = "l15-{$allReferredUsers[$grandparentIndex]->id}";
            for ($childIndex = 0; $childIndex < 3 && $userIndex < count($allReferredUsers); $childIndex++) {
                $childUserId = "l15-{$allReferredUsers[3 + ($grandparentIndex * 3) + $childIndex]->id}";
                for ($grandchildIndex = 0; $grandchildIndex < 3 && $userIndex < count($allReferredUsers); $grandchildIndex++) {
                    $user = $allReferredUsers[$userIndex];
                    $userId = "l15-{$user->id}";
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l15',
                        'parentId' => $childUserId,
                    ];
                    $userIndex++;
                }
            }
        }
        return $nodes;
    }

    /**
     * Build Level 15 tree for child user (EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14)
     * Shows children that the child user has inside the main user's BFS matrix
     */
    private function buildLevel15TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel15ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l15-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Level 15 child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No Level 15 descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Build Level 2 tree for CHILD user (when they view their own dashboard)
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel2TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // First, get the child's descendants from the main tree
        $mainTreeNodes = $this->buildLevel2ForMainUser($mainUserId, $maxChildren);
        
        // Find the child node in the main tree
        $childNodeId = "l2-{$childUserId}";
        $descendantNodes = [];
        
        // Recursively find all descendants of the child
        $this->findDescendants($mainTreeNodes, $childNodeId, $descendantNodes);
        
        \Log::info("Child tree building", [
            'childUserId' => $childUserId,
            'mainUserId' => $mainUserId,
            'childNodeId' => $childNodeId,
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, return empty array (don't build BFS tree)
        if (empty($descendantNodes)) {
            \Log::info("No descendants found for child", [
                'childUserId' => $childUserId,
                'mainUserId' => $mainUserId
            ]);
            return [];
        }
        
        // Return the descendant nodes with updated parent IDs
        $enhancedNodes = [];
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            // For grandchildren, keep their original parent (which is a child of the child user)
            $enhancedNodes[] = $newNode;
        }
        
        return $enhancedNodes;
    }

    /**
     * Recursively find all descendants of a given node
     */
    private function findDescendants($nodes, $parentId, &$descendants)
    {
        foreach ($nodes as $node) {
            if ($node['parentId'] === $parentId) {
                $descendants[] = $node;
                // Recursively find children of this node
                $this->findDescendants($nodes, $node['id'], $descendants);
            }
        }
    }

    /**
     * Filter nodes to show only 1-3-9 structure (13 users max) for display
     * This preserves all functionality while limiting the visual display
     */
    private function limitDisplayToMatrix($nodes, $rootId, $maxDisplay = 12)
    {
        // Simply return first 13 nodes - no complex filtering
        return array_slice($nodes, 0, $maxDisplay);
    }

    public function index()
    {
        $me = auth()->user();

        // Check if current user has bought 2nd plan AND is NOT the first user (main user)
        $isLevel2OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 2;
        $isLevel3OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 3;
        $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
        $isMainUser = $me->id === $mainUser->id;
        
        if ($isLevel2OrAbove && !$isMainUser) {
            // Use the main user (first user) as the root for all child dashboards
            $mainUser = User::orderBy('created_at')->first(); // Get the first user (main user)
            
            // Build tree showing their branch from main tree + their grandchildren
            $allLevel2Nodes = $this->buildLevel2TreeForChild($me->id, $mainUser->id, 3);
            // Limit display to 1-3-9 structure (12 users max) while preserving all functionality
            $level2Nodes = $this->limitDisplayToMatrix($allLevel2Nodes, $me->id, 12);
            
            // Also build Level 3 for child dashboard if they have 3+ plans
            if ($isLevel3OrAbove) {
                $allLevel3Nodes = $this->buildLevel3TreeForChild($me->id, $mainUser->id, 3);
                $level3Nodes = $this->limitDisplayToMatrix($allLevel3Nodes, $me->id, 12);
            } else {
                $level3Nodes = [];
            }
            
            // Also build Level 4 for child dashboard if they have 4+ plans
            $isLevel4OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 4;
            if ($isLevel4OrAbove) {
                $allLevel4Nodes = $this->buildLevel4TreeForChild($me->id, $mainUser->id, 3);
                $level4Nodes = $this->limitDisplayToMatrix($allLevel4Nodes, $me->id, 12);
            } else {
                $level4Nodes = [];
            }
            
            // Also build Level 5 for child dashboard if they have 5+ plans
            $isLevel5OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 5;
            if ($isLevel5OrAbove) {
                $allLevel5Nodes = $this->buildLevel5TreeForChild($me->id, $mainUser->id, 3);
                $level5Nodes = $this->limitDisplayToMatrix($allLevel5Nodes, $me->id, 12);
            } else {
                $level5Nodes = [];
            }
            
            // Also build Level 6 for child dashboard if they have 6+ plans
            $isLevel6OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 6;
            if ($isLevel6OrAbove) {
                $allLevel6Nodes = $this->buildLevel6TreeForChild($me->id, $mainUser->id, 3);
                $level6Nodes = $this->limitDisplayToMatrix($allLevel6Nodes, $me->id, 12);
            } else {
                $level6Nodes = [];
            }
            
            // Also build Level 7 for child dashboard if they have 7+ plans
            $isLevel7OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 7;
            if ($isLevel7OrAbove) {
                $allLevel7Nodes = $this->buildLevel7TreeForChild($me->id, $mainUser->id, 3);
                $level7Nodes = $this->limitDisplayToMatrix($allLevel7Nodes, $me->id, 12);
            } else {
                $level7Nodes = [];
            }
            
            // Also build Level 8 for child dashboard if they have 8+ plans
            $isLevel8OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 8;
            if ($isLevel8OrAbove) {
                $allLevel8Nodes = $this->buildLevel8TreeForChild($me->id, $mainUser->id, 3);
                $level8Nodes = $this->limitDisplayToMatrix($allLevel8Nodes, $me->id, 12);
            } else {
                $level8Nodes = [];
            }
            
            // Also build Level 9 for child dashboard if they have 9+ plans
            $isLevel9OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 9;
            if ($isLevel9OrAbove) {
                $allLevel9Nodes = $this->buildLevel9TreeForChild($me->id, $mainUser->id, 3);
                $level9Nodes = $this->limitDisplayToMatrix($allLevel9Nodes, $me->id, 12);
            } else {
                $level9Nodes = [];
            }
            
            // Also build Level 10 for child dashboard if they have 10+ plans
            $isLevel10OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 10;
            if ($isLevel10OrAbove) {
                $allLevel10Nodes = $this->buildLevel10TreeForChild($me->id, $mainUser->id, 3);
                $level10Nodes = $this->limitDisplayToMatrix($allLevel10Nodes, $me->id, 12);
            } else {
                $level10Nodes = [];
            }
            
            // Also build Level 11 for child dashboard if they have 11+ plans
            $isLevel11OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 11;
            if ($isLevel11OrAbove) {
                $allLevel11Nodes = $this->buildLevel11TreeForChild($me->id, $mainUser->id, 3);
                $level11Nodes = $this->limitDisplayToMatrix($allLevel11Nodes, $me->id, 12);
            } else {
                $level11Nodes = [];
            }
            
            // Also build Level 12 for child dashboard if they have 12+ plans
            $isLevel12OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 12;
            if ($isLevel12OrAbove) {
                $allLevel12Nodes = $this->buildLevel12TreeForChild($me->id, $mainUser->id, 3);
                $level12Nodes = $this->limitDisplayToMatrix($allLevel12Nodes, $me->id, 12);
            } else {
                $level12Nodes = [];
            }
            
            // Also build Level 13 for child dashboard if they have 13+ plans
            $isLevel13OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 13;
            if ($isLevel13OrAbove) {
                $allLevel13Nodes = $this->buildLevel13TreeForChild($me->id, $mainUser->id, 3);
                $level13Nodes = $this->limitDisplayToMatrix($allLevel13Nodes, $me->id, 12);
            } else {
                $level13Nodes = [];
            }
            
            // Also build Level 14 for child dashboard if they have 14+ plans
            $isLevel14OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 14;
            if ($isLevel14OrAbove) {
                $allLevel14Nodes = $this->buildLevel14TreeForChild($me->id, $mainUser->id, 3);
                $level14Nodes = $this->limitDisplayToMatrix($allLevel14Nodes, $me->id, 12);
            } else {
                $level14Nodes = [];
            }
            
            // Also build Level 15 for child dashboard if they have 15+ plans
            $isLevel15OrAbove = $me->planSelections()->where('status', 'approved')->count() >= 15;
            if ($isLevel15OrAbove) {
                $allLevel15Nodes = $this->buildLevel15TreeForChild($me->id, $mainUser->id, 3);
                $level15Nodes = $this->limitDisplayToMatrix($allLevel15Nodes, $me->id, 12);
            } else {
                $level15Nodes = [];
            }
            
            // Also build Level 1 for child dashboard (their direct referrals)
            $level1 = User::select('id','name','email','referral_code','created_at','referred_by')
                ->where('referred_by', $me->id)
                ->whereHas('planSelections', fn($q) => $q->where('status','approved'))
                ->orderBy('created_at')
                ->get();
        } else {
            // Main/root user viewing their dashboard
            // Build tree with pure BFS logic for Level 2
            $allLevel2Nodes = $this->buildLevel2ForMainUser($me->id, 3);
            // Limit display to 1-3-9 structure (12 users max) while preserving all functionality
            $level2Nodes = $this->limitDisplayToMatrix($allLevel2Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 3 - EXACTLY like Level 2
            $allLevel3Nodes = $this->buildLevel3ForMainUser($me->id, 3);
            $level3Nodes = $this->limitDisplayToMatrix($allLevel3Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 4 - EXACTLY like Level 2 and 3
            $allLevel4Nodes = $this->buildLevel4ForMainUser($me->id, 3);
            $level4Nodes = $this->limitDisplayToMatrix($allLevel4Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 5 - EXACTLY like Level 2, 3 and 4
            $allLevel5Nodes = $this->buildLevel5ForMainUser($me->id, 3);
            $level5Nodes = $this->limitDisplayToMatrix($allLevel5Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 6 - EXACTLY like Level 2, 3, 4 and 5
            $allLevel6Nodes = $this->buildLevel6ForMainUser($me->id, 3);
            $level6Nodes = $this->limitDisplayToMatrix($allLevel6Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 7 - EXACTLY like Level 2, 3, 4, 5 and 6
            $allLevel7Nodes = $this->buildLevel7ForMainUser($me->id, 3);
            $level7Nodes = $this->limitDisplayToMatrix($allLevel7Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 8 - EXACTLY like Level 2, 3, 4, 5, 6 and 7
            $allLevel8Nodes = $this->buildLevel8ForMainUser($me->id, 3);
            $level8Nodes = $this->limitDisplayToMatrix($allLevel8Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 9 - EXACTLY like Level 2, 3, 4, 5, 6, 7 and 8
            $allLevel9Nodes = $this->buildLevel9ForMainUser($me->id, 3);
            $level9Nodes = $this->limitDisplayToMatrix($allLevel9Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 10 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8 and 9
            $allLevel10Nodes = $this->buildLevel10ForMainUser($me->id, 3);
            $level10Nodes = $this->limitDisplayToMatrix($allLevel10Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 11 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9 and 10
            $allLevel11Nodes = $this->buildLevel11ForMainUser($me->id, 3);
            $level11Nodes = $this->limitDisplayToMatrix($allLevel11Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 12 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10 and 11
            $allLevel12Nodes = $this->buildLevel12ForMainUser($me->id, 3);
            $level12Nodes = $this->limitDisplayToMatrix($allLevel12Nodes, $me->id, 12);
            
            // Build tree with pure BFS logic for Level 13 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 and 12
            $allLevel13Nodes = $this->buildLevel13ForMainUser($me->id, 3);
            $level13Nodes = $this->limitDisplayToMatrix($allLevel13Nodes, $me->id, 12);

            // Build tree with pure BFS logic for Level 14 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 and 13
            $allLevel14Nodes = $this->buildLevel14ForMainUser($me->id, 3);
            $level14Nodes = $this->limitDisplayToMatrix($allLevel14Nodes, $me->id, 12);

            // Build tree with pure BFS logic for Level 15 - EXACTLY like Level 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 and 14
            $allLevel15Nodes = $this->buildLevel15ForMainUser($me->id, 3);
            $level15Nodes = $this->limitDisplayToMatrix($allLevel15Nodes, $me->id, 12);
        }
        
        // LEVEL 1: Untouched - all directs with ≥1 approved plan
        $level1 = User::select('id','name','email','referral_code','created_at','referred_by')
            ->where('referred_by', $me->id)
            ->whereHas('planSelections', fn($q) => $q->where('status','approved'))
            ->orderBy('created_at')
            ->get();

        // Root node (always the current logged-in user)
        $nodes = [[
            'id'       => $me->id,
            'real_id'  => $me->id,
            'name'     => $me->name,
            'code'     => $me->referral_code,
            'joined'   => $me->created_at->format('M d, Y'),
            'type'     => 'me',
            'parentId' => null,
        ]];

        // Level 1 nodes (unchanged) - only show if user has Level 1 referrals
        $l1Nodes = [];
        foreach ($level1 as $user) {
            $l1Nodes[] = [
                'id'       => $user->id . '-l1',
                'real_id'  => $user->id,
                'name'     => $user->name,
                'code'     => $user->referral_code,
                'joined'   => $user->created_at->format('M d, Y'),
                'type'     => 'l1',
                'parentId' => $me->id,
            ];
        }

        // Merge all - Level 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14 and 15 now use BFS nodes like Level 2
        $nodes = array_merge($nodes, $l1Nodes, $level2Nodes, $level3Nodes, $level4Nodes, $level5Nodes, $level6Nodes, $level7Nodes, $level8Nodes, $level9Nodes, $level10Nodes, $level11Nodes, $level12Nodes, $level13Nodes, $level14Nodes, $level15Nodes);

        // Calculate progress metrics
        $directCount = $level1->count();
        $maxDirects = 12;
        $progress = min(100, round(($directCount / $maxDirects) * 100));
        $toNext = max(0, $maxDirects - $directCount);
        $progressText = $toNext > 0 
            ? "Need {$toNext} more direct referral" . ($toNext > 1 ? 's' : '') . " to reach next level"
            : "Maximum direct referrals reached!";

        return view('user.team.index', compact(
            'me',
            'level1',
            'nodes',
            'directCount',
            'progress',
            'toNext',
            'progressText'
        ));
    }
}