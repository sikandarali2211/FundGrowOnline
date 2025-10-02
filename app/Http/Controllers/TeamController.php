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
    private function buildLevel2ForMainUser($rootUserId, $maxChildren = 3)
    {
        $nodes = [];
        $childCount = [$rootUserId => 0];
        $queue = [[$rootUserId, 0]];

        // Get all users referred by the root user and place them in BFS 1-3-9 matrix
        // This creates the proper hierarchy: first 3 direct, then 9 grandchildren, etc.
        $allReferredUsers = User::where('referred_by', $rootUserId)
            ->orderBy('created_at')
            ->get();

        // Debug: Log the BFS matrix order
        \Log::info("BFS Matrix Order", [
            'rootUserId' => $rootUserId,
            'allUsers' => $allReferredUsers->pluck('name', 'id')->toArray()
        ]);

        foreach ($allReferredUsers as $user) {
            // Place this user in the tree using BFS
            while (!empty($queue)) {
                [$parentId, $depth] = $queue[0];
                $count = $childCount[$parentId] ?? 0;

                if ($count < $maxChildren) {
                    $userId = "l2-{$user->id}";
                    
                    $nodes[] = [
                        'id'       => $userId,
                        'real_id'  => $user->id,
                        'name'     => $user->name,
                        'code'     => $user->referral_code,
                        'joined'   => $user->created_at->format('M d, Y'),
                        'type'     => 'l2',
                        'parentId' => $parentId,
                    ];

                    $childCount[$parentId] = $count + 1;
                    $childCount[$userId] = 0;

                    // Debug: Log when Shezil gets assigned users
                    if ($parentId == "l2-46" || $userId == "l2-46") {
                        \Log::info("Shezil assignment", [
                            'user' => $user->name,
                            'userId' => $userId,
                            'parentId' => $parentId,
                            'depth' => $depth
                        ]);
                    }

                    // Allow unlimited depth for BFS matrix - no 1-3-9 restriction
                    // This allows more users to be assigned to each child
                    $queue[] = [$userId, $depth + 1];

                    break;
                } else {
                    array_shift($queue);
                }
            }
        }
        
        // Now handle referrals from children (like Zayyyan referred by Shezil)
        // Get all users referred by the direct children
        $childReferrals = User::whereIn('referred_by', $allReferredUsers->pluck('id'))
            ->orderBy('created_at')
            ->get();
            
        foreach ($childReferrals as $user) {
            $referrerNodeId = "l2-{$user->referred_by}";
            
            // Check if the referrer exists in the tree and has space
            $referrerExists = false;
            foreach ($nodes as $node) {
                if ($node['id'] === $referrerNodeId) {
                    $referrerExists = true;
                    $referrerChildCount = $childCount[$referrerNodeId] ?? 0;
                    
                    if ($referrerChildCount < $maxChildren) {
                        // Referrer has space, place as direct child
                        $userId = "l2-{$user->id}";
                        
                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l2',
                            'parentId' => $referrerNodeId,
                        ];
                        
                        $childCount[$referrerNodeId] = $referrerChildCount + 1;
                        $childCount[$userId] = 0;
                        
                        // Debug: Log when Shezil gets assigned users
                        if ($referrerNodeId == "l2-46" || $userId == "l2-46") {
                            \Log::info("Shezil referral assignment", [
                                'user' => $user->name,
                                'userId' => $userId,
                                'parentId' => $referrerNodeId,
                                'referred_by' => $user->referred_by,
                                'referrerChildCount' => $referrerChildCount + 1
                            ]);
                        }
                    } else {
                        // Referrer has 3 children, find the first child with space
                        $grandchildParent = null;
                        foreach ($nodes as $potentialChild) {
                            if ($potentialChild['parentId'] === $referrerNodeId) {
                                $childNodeId = $potentialChild['id'];
                                $childNodeCount = $childCount[$childNodeId] ?? 0;
                                if ($childNodeCount < $maxChildren) {
                                    $grandchildParent = $childNodeId;
                                    break;
                                }
                            }
                        }
                        
                        if ($grandchildParent) {
                            // Place as grandchild
                            $userId = "l2-{$user->id}";
                            
                            $nodes[] = [
                                'id'       => $userId,
                                'real_id'  => $user->id,
                                'name'     => $user->name,
                                'code'     => $user->referral_code,
                                'joined'   => $user->created_at->format('M d, Y'),
                                'type'     => 'l2',
                                'parentId' => $grandchildParent,
                            ];
                            
                            $childCount[$grandchildParent] = ($childCount[$grandchildParent] ?? 0) + 1;
                            $childCount[$userId] = 0;
                            
                            // Debug: Log when Shezil gets assigned users
                            if ($grandchildParent == "l2-46" || $userId == "l2-46" || $referrerNodeId == "l2-46") {
                                \Log::info("Shezil grandchild assignment", [
                                    'user' => $user->name,
                                    'userId' => $userId,
                                    'parentId' => $grandchildParent,
                                    'referred_by' => $user->referred_by,
                                    'grandchildParent' => $grandchildParent
                                ]);
                            }
                        }
                    }
                    break;
                }
            }
        }

        return $nodes;
    }

    /**
     * Build Level 2 tree for CHILD user (when they view their own dashboard)
     * Shows the child's branch from main tree + their grandchildren
     */
    private function buildLevel2TreeForChild($childUserId, $mainUserId, $maxChildren = 3)
    {
        // Build the main user's full tree first
        $mainTreeNodes = $this->buildLevel2ForMainUser($mainUserId, 3);
        
        // Find the child's node ID in the main tree
        $childNodeId = "l2-{$childUserId}";
        
        // Build a parent -> children map
        $childrenMap = [];
        foreach ($mainTreeNodes as $node) {
            $parentId = $node['parentId'];
            if (!isset($childrenMap[$parentId])) {
                $childrenMap[$parentId] = [];
            }
            $childrenMap[$parentId][] = $node;
        }
        
        // Recursively collect all descendants of this child from the main tree
        $collectDescendants = function($parentId) use (&$collectDescendants, $childrenMap) {
            $descendants = [];
            if (isset($childrenMap[$parentId])) {
                foreach ($childrenMap[$parentId] as $child) {
                    $descendants[] = $child;
                    // Get this child's descendants
                    $grandchildren = $collectDescendants($child['id']);
                    $descendants = array_merge($descendants, $grandchildren);
                }
            }
            return $descendants;
        };
        
        // Get all nodes under this child in the main tree (these are child's assigned team)
        $descendantNodes = $collectDescendants($childNodeId);
        
        // Debug: Log what we found for this child
        \Log::info("Child tree debug", [
            'childUserId' => $childUserId,
            'childNodeId' => $childNodeId,
            'mainTreeNodesCount' => count($mainTreeNodes),
            'descendantNodesCount' => count($descendantNodes),
            'descendantNodes' => $descendantNodes
        ]);
        
        // If no descendants found in main tree, build child's own BFS tree from their referrals
        if (empty($descendantNodes)) {
            $nodes = [];
            $childCount = [$childUserId => 0];
            $queue = [[$childUserId, 0]]; // (parentId, depth)

            // Get all users referred by this child (their children)
            $childUsers = User::where('referred_by', $childUserId)
                ->orderBy('created_at')
                ->get();

            // Place children using BFS (1-3-9 matrix)
            foreach ($childUsers as $user) {
                while (!empty($queue)) {
                    [$parentId, $depth] = $queue[0];
                    $count = $childCount[$parentId] ?? 0;

                    if ($count < $maxChildren) {
                        $userId = "l2-{$user->id}";

                        $nodes[] = [
                            'id'       => $userId,
                            'real_id'  => $user->id,
                            'name'     => $user->name,
                            'code'     => $user->referral_code,
                            'joined'   => $user->created_at->format('M d, Y'),
                            'type'     => 'l2',
                            'parentId' => $parentId,
                        ];

                        $childCount[$parentId] = $count + 1;
                        $childCount[$userId] = 0;

                        // Allow unlimited depth for BFS matrix
                        $queue[] = [$userId, $depth + 1];

                        break;
                    } else {
                        array_shift($queue);
                    }
                }
            }

            return $nodes;
        }
        
        // Start building enhanced nodes - first add all existing descendants
        $enhancedNodes = [];
        
        // Update parent IDs for direct children of the child
        foreach ($descendantNodes as $node) {
            $newNode = $node;
            // Update parent reference: if parent was childNodeId, point to childUserId
            if ($node['parentId'] === $childNodeId) {
                $newNode['parentId'] = $childUserId;
            }
            $enhancedNodes[] = $newNode;
        }
        
        // Build a map to track which real_ids are already in the tree
        $existingRealIds = [];
        foreach ($enhancedNodes as $node) {
            $existingRealIds[$node['real_id']] = true;
        }
        
        // Track child counts for each node
        $childCounts = [];
        foreach ($enhancedNodes as $node) {
            if (!isset($childCounts[$node['id']])) {
                $childCounts[$node['id']] = 0;
            }
        }
        
        // Count existing children
        foreach ($enhancedNodes as $node) {
            if (isset($childCounts[$node['parentId']])) {
                $childCounts[$node['parentId']]++;
            }
        }
        
        // Now add grandchildren for EACH node (not just from descendantNodes)
        // This ensures child sees ALL their grandchildren, even if not in main tree due to 1-3-9 limit
        $nodesToCheck = $enhancedNodes; // Check all nodes we've added so far
        
        foreach ($nodesToCheck as $node) {
            $nodeRealId = $node['real_id'];
            $nodeId = $node['id'];
            
            // Get this node's referrals (grandchildren) - ALL Level 2+ referrals
            $grandchildReferrals = User::where('referred_by', $nodeRealId)
                ->where('level', '>=', 2)
                ->orderBy('created_at')
                ->get();
            
            $currentChildCount = $childCounts[$nodeId] ?? 0;
            
            // Add each grandchild that doesn't already exist
            foreach ($grandchildReferrals as $grandchild) {
                // Skip if already in tree
                if (isset($existingRealIds[$grandchild->id])) {
                    continue;
                }
                
                // Skip if parent already has max children (respect 1-3-9 matrix)
                if ($currentChildCount >= $maxChildren) {
                    break;
                }
                
                $grandchildId = "l2-{$grandchild->id}";
                
                $enhancedNodes[] = [
                    'id'       => $grandchildId,
                    'real_id'  => $grandchild->id,
                    'name'     => $grandchild->name,
                    'code'     => $grandchild->referral_code,
                    'joined'   => $grandchild->created_at->format('M d, Y'),
                    'type'     => 'l2',
                    'parentId' => $nodeId,
                ];
                
                // Mark as added
                $existingRealIds[$grandchild->id] = true;
                $currentChildCount++;
                $childCounts[$nodeId] = $currentChildCount;
            }
        }
        
        return $enhancedNodes;
    }

    public function index()
    {
        $me = auth()->user();

        // Check if current user is a child of someone (has referred_by) OR is Level 2+
        $isChildOfSomeone = $me->referred_by !== null;
        $isLevel2OrAbove = $me->level >= 2;
        
        if ($isChildOfSomeone) {
            // Find the main/root user (the one who referred this user)
            $mainUser = User::find($me->referred_by);
            
            if ($mainUser) {
            // Build tree showing their branch from main tree + their grandchildren
            $level2Nodes = $this->buildLevel2TreeForChild($me->id, $mainUser->id, 3);
            } else {
                // Fallback: build their own Level 2 tree if main user not found
                $level2Nodes = $this->buildLevel2TreeForChild($me->id, $me->id, 3);
            }
            
            // Also build Level 1 for child dashboard (their direct referrals)
            $level1 = User::select('id','name','email','referral_code','created_at','referred_by')
                ->where('referred_by', $me->id)
                ->whereHas('planSelections', fn($q) => $q->where('status','approved'))
                ->orderBy('created_at')
                ->get();
        } else {
            // Main/root user viewing their dashboard
            // Build tree with children and children's referrals (but not grandchildren's referrals)
            $level2Nodes = $this->buildLevel2ForMainUser($me->id, 3);
        }

        // LEVELS 3-15: Keep original logic (only for display purposes)
        $level3 = User::where('referred_by', $me->id)->where('level', '>=', 3)->orderBy('created_at')->get();
        $level4 = User::where('referred_by', $me->id)->where('level', '>=', 4)->orderBy('created_at')->get();
        $level5 = User::where('referred_by', $me->id)->where('level', '>=', 5)->orderBy('created_at')->get();
        $level6 = User::where('referred_by', $me->id)->where('level', '>=', 6)->orderBy('created_at')->get();
        $level7 = User::where('referred_by', $me->id)->where('level', '>=', 7)->orderBy('created_at')->get();
        $level8 = User::where('referred_by', $me->id)->where('level', '>=', 8)->orderBy('created_at')->get();
        $level9 = User::where('referred_by', $me->id)->where('level', '>=', 9)->orderBy('created_at')->get();
        $level10 = User::where('referred_by', $me->id)->where('level', '>=', 10)->orderBy('created_at')->get();
        $level11 = User::where('referred_by', $me->id)->where('level', '>=', 11)->orderBy('created_at')->get();
        $level12 = User::where('referred_by', $me->id)->where('level', '>=', 12)->orderBy('created_at')->get();
        $level13 = User::where('referred_by', $me->id)->where('level', '>=', 13)->orderBy('created_at')->get();
        $level14 = User::where('referred_by', $me->id)->where('level', '>=', 14)->orderBy('created_at')->get();
        $level15 = User::where('referred_by', $me->id)->where('level', '>=', 15)->orderBy('created_at')->get();
        
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

        // Build pyramids for levels 3-15 (original logic)
        $l3Nodes = $this->buildMatrix($level3, $me->id, 3, 'l3');
        $l4Nodes = $this->buildMatrix($level4, $me->id, 3, 'l4');
        $l5Nodes = $this->buildMatrix($level5, $me->id, 3, 'l5');
        $l6Nodes = $this->buildMatrix($level6, $me->id, 3, 'l6');
        $l7Nodes = $this->buildMatrix($level7, $me->id, 3, 'l7');
        $l8Nodes = $this->buildMatrix($level8, $me->id, 3, 'l8');
        $l9Nodes = $this->buildMatrix($level9, $me->id, 3, 'l9');
        $l10Nodes = $this->buildMatrix($level10, $me->id, 3, 'l10');
        $l11Nodes = $this->buildMatrix($level11, $me->id, 3, 'l11');
        $l12Nodes = $this->buildMatrix($level12, $me->id, 3, 'l12');
        $l13Nodes = $this->buildMatrix($level13, $me->id, 3, 'l13');
        $l14Nodes = $this->buildMatrix($level14, $me->id, 3, 'l14');
        $l15Nodes = $this->buildMatrix($level15, $me->id, 3, 'l15');

        // Merge all
        $nodes = array_merge($nodes, $l1Nodes, $level2Nodes, $l3Nodes, $l4Nodes, $l5Nodes, $l6Nodes, $l7Nodes, $l8Nodes, $l9Nodes, $l10Nodes, $l11Nodes, $l12Nodes, $l13Nodes, $l14Nodes, $l15Nodes);

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