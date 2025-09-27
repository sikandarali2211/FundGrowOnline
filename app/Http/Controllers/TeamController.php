<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LevelService;

class TeamController extends Controller
{
    public function index()
    {
        $me = auth()->user();

        // --- Level 1 (directs) - Only users with active plans ---
        $level1 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id')
            ->where(function ($q) use ($me) {
                $q->where('referred_by', $me->id)
                    ->orWhere('sponsor_id', $me->id);
            })
            ->whereHas('planSelections', function ($query) {
                $query->where('status', 'approved');
            })
            ->orderBy('created_at')
            ->get();

        // Level 2 removed - only showing Level 1 direct referrals

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
            'parentId' => null
        ];
        
        // Add Level 1 users only
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

        return view('user.team.index', compact(
            'me',
            'level1',
            'directCount',
            'toNext',
            'progress',
            'progressText',
            'nodes'
        ));
    }
}
