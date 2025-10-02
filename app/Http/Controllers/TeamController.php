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

    public function index()
    {
        $me = auth()->user();

        $level2 = User::where('referred_by', $me->id)->where('level', '>=', 2)->orderBy('created_at')->get();
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
        
        // --- Level 1 (all directs with ≥1 plan)
        $level1 = User::select('id','name','email','referral_code','created_at','referred_by')
            ->where('referred_by', $me->id)
            ->whereHas('planSelections', fn($q) => $q->where('status','approved'))
            ->orderBy('created_at')
            ->get();

        // --- Root node
        $nodes = [[
            'id'       => $me->id,
            'real_id'  => $me->id,
            'name'     => $me->name,
            'code'     => $me->referral_code,
            'joined'   => $me->created_at->format('M d, Y'),
            'type'     => 'me',
            'parentId' => null,
        ]];

        // --- Level 1 nodes (direct referrals)
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

        // --- Build pyramids
        $l2Nodes = $this->buildMatrix($level2, $me->id, 3, 'l2');
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
        $nodes = array_merge($nodes, $l1Nodes, $l2Nodes, $l3Nodes, $l4Nodes, $l5Nodes, $l6Nodes, $l7Nodes, $l8Nodes, $l9Nodes, $l10Nodes, $l11Nodes, $l12Nodes, $l13Nodes, $l14Nodes, $l15Nodes);

        return view('user.team.index', compact(
            'me',
            'level1',
            'level2',
            'level3',
            'level4',
            'level5',
            'level6',
            'level7',
            'level8',
            'level9',
            'level10',
            'level11',
            'level12',
            'level13',
            'level14',
            'level15',
            'nodes'
        ));
    }
}
