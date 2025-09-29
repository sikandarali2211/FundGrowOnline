<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LevelService;

class TeamController extends Controller
{
    public function index()
    {
        $me = auth()->user();

        // --- Level 1 (directs) - All direct referrals with approved plans ---
        $level1 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id', 'level')
            ->where('referred_by', $me->id) // Only direct referrals
            ->where('level', 1) // Exclude users who already became Level 2
            ->whereHas('planSelections', function ($query) {
                $query->where('status', 'approved');
            })
            ->orderBy('created_at')
            ->get();

        // --- Level 2 (your directs who purchased second plan) ---
        $level2 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id', 'level')
            ->where('referred_by', $me->id)
            ->whereHas('planSelections', function ($query) {
                $query->where('status', 'approved');
            }, '>=', 2)
            ->orderBy('created_at')
            ->get();

        // --- NEW: Recalculate and persist level as per rules ---
        $newLevel = LevelService::recalcAndSave($me);
        $me->level = $newLevel; // update local instance

        // --- Progress for UI (dynamic) ---
        [$toNext, $progress, $progressText] = LevelService::progress($me);

        $directCount = $level1->count();

        // Build nodes array for the org chart
        $nodes = [];
        
        // Add current user as root
        $nodes[] = [
            'id' => $me->id,
            'name' => $me->name,
            'code' => $me->referral_code,
            'joined' => $me->created_at->format('M d, Y'),
            'type' => 'me',
            'parentId' => null,
            'level' => $me->level
        ];
        
        // Add Level 1 users (A, B, C)
        foreach ($level1 as $user) {
            $nodes[] = [
                'id' => $user->id,
                'name' => $user->name,
                'code' => $user->referral_code,
                'joined' => $user->created_at->format('M d, Y'),
                'type' => 'l1',
                'parentId' => $me->id
            ];
        }

        // Add Level 2 users (second plan purchased)
        // Display rule: visually place Level 2 users under Level 1 users
        // in a round-robin manner (max 3 children per Level 1),
        // without changing actual referrals in DB.
        $l1Ids = $level1->pluck('id')->values();
        $childCountByL1 = [];
        foreach ($l1Ids as $lid) { $childCountByL1[$lid] = 0; }
        $l1Index = 0;

        foreach ($level2 as $user) {
            // Find next L1 with available slot (<3). If none, attach to current user as fallback.
            $assignedParentId = $me->id;
            if ($l1Ids->count() > 0) {
                // Try up to N times to find a slot
                $tries = 0;
                while ($tries < $l1Ids->count()) {
                    $candidateId = $l1Ids[$l1Index % $l1Ids->count()];
                    if (($childCountByL1[$candidateId] ?? 0) < 3) {
                        $assignedParentId = $candidateId;
                        $childCountByL1[$candidateId] = ($childCountByL1[$candidateId] ?? 0) + 1;
                        $l1Index++;
                        break;
                    }
                    $l1Index++;
                    $tries++;
                }
            }

            $nodes[] = [
                'id' => $user->id,
                'name' => $user->name,
                'code' => $user->referral_code,
                'joined' => $user->created_at->format('M d, Y'),
                'type' => 'l2',
                'parentId' => $assignedParentId,
            ];
        }

        return view('user.team.index', compact(
            'me',
            'level1',
            'level2',
            'directCount',
            'toNext',
            'progress',
            'progressText',
            'nodes'
        ));
    }
}
