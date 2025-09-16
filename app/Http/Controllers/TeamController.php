<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LevelService;

class TeamController extends Controller
{
    public function index()
    {
        $me = auth()->user();

        // --- Level 1 (directs) ---
        $level1 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id')
            ->where(function ($q) use ($me) {
                $q->where('referred_by', $me->id)
                    ->orWhere('sponsor_id', $me->id);
            })
            ->orderBy('created_at')
            ->get();

        // --- Level 2 (children of my directs) ---
        $level2 = collect();
        if ($level1->isNotEmpty()) {
            $l1Ids = $level1->pluck('id');
            $level2 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id')
                ->where(function ($q) use ($l1Ids) {
                    $q->whereIn('referred_by', $l1Ids)
                        ->orWhereIn('sponsor_id', $l1Ids);
                })
                ->orderBy('created_at')
                ->get()
                ->groupBy(fn($u) => $u->referred_by ?? $u->sponsor_id);
        }

        // --- NEW: Recalculate and persist level as per rules ---
        $newLevel = LevelService::recalcAndSave($me);
        $me->level = $newLevel; // update local instance

        // --- Progress for UI (dynamic) ---
        [$toNext, $progress, $progressText] = LevelService::progress($me);

        $directCount = $level1->count();

        return view('user.team.index', compact(
            'me',
            'level1',
            'level2',
            'directCount',
            'toNext',
            'progress',
            'progressText'
        ));
    }
}
