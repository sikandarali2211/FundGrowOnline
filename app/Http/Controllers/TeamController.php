<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LevelService;

class TeamController extends Controller
{
private function assignReferral(array &$nodes, array &$childCount, array $possibleParents, $user, $maxChildren = 3)
    {
        $queue = $possibleParents;

        while (!empty($queue)) {
            $parentId = array_shift($queue);

            $count = $childCount[$parentId] ?? 0;

            if ($count < $maxChildren) {
                // Place user under this parent (BFS style)
                $nodes[] = [
                    'id'       => "l2-{$user->id}", // unique id
                    'real_id'  => $user->id,
                    'name'     => $user->name,
                    'code'     => $user->referral_code,
                    'joined'   => $user->created_at->format('M d, Y'),
                    'type'     => 'l2',
                    'parentId' => $parentId,
                ];
                $childCount[$parentId] = $count + 1;
                $childCount[$user->id] = 0; // init new child
                return true;
            }

            // enqueue children of this parent
            foreach ($nodes as $n) {
                if ($n['parentId'] === $parentId) {
                    $queue[] = $n['id'];
                }
            }
        }

        return false;
    }

    public function index()
    {
        $me = auth()->user();

        // --- Level 1 (all directs with ≥1 plan)
        $level1 = User::select('id','name','email','referral_code','created_at','referred_by')
            ->where('referred_by', $me->id)
            ->whereHas('planSelections', fn($q) => $q->where('status','approved'))
            ->orderBy('created_at')
            ->get();

        // --- Level 2 (directs of root with ≥2 plans)
        $level2 = User::select('id','name','email','referral_code','created_at','referred_by')
            ->where('referred_by', $me->id) // ✅ only root’s directs
            ->whereHas('planSelections', fn($q) => $q->where('status','approved'), '>=', 2)
            ->orderBy('created_at')
            ->get();

        // --- Root node (always there)
        $nodes = [[
            'id'       => $me->id,
            'real_id'  => $me->id,
            'name'     => $me->name,
            'code'     => $me->referral_code,
            'joined'   => $me->created_at->format('M d, Y'),
            'type'     => 'me',
            'parentId' => null,
        ]];

        // --- Level 1 chart nodes (for L1 chart only)
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

        // --- Level 2 BFS matrix ---
        $l2Nodes = [];
        $childCount = [$me->id => 0];
        $possibleParents = [$me->id]; // ✅ start only with root

        foreach ($level2 as $user) {
            $this->assignReferral($l2Nodes, $childCount, $possibleParents, $user, 3);
        }

        // --- Merge
        $nodes = array_merge($nodes, $l1Nodes, $l2Nodes);

        return view('user.team.index', compact(
            'me',
            'level1',
            'level2',
            'nodes'
        ));
    }
}
