<?php

namespace App\Http\Controllers;

use App\Models\User;

class TeamController extends Controller
{
    public function index()
    {
        $me = auth()->user();

        // Level 1: mere direct (referred_by ya sponsor_id, dono support)
        $level1 = User::select('id', 'name', 'email', 'referral_code', 'created_at', 'referred_by', 'sponsor_id')
            ->where(function ($q) use ($me) {
                $q->where('referred_by', $me->id)
                    ->orWhere('sponsor_id', $me->id);
            })
            ->orderBy('created_at')
            ->get();

        // Level 2: mere Level-1 ke direct
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
                // group key: jis L1 ne refer kiya (referred_by prefer, warna sponsor_id)
                ->groupBy(function ($u) {
                    return $u->referred_by ?? $u->sponsor_id;
                });
        }

        $directCount = $level1->count();
        $toNext   = max(0, 12 - $directCount);
        $progress = min(100, (int) round(($directCount / 12) * 100));

        return view('user.team.index', compact('me', 'level1', 'level2', 'directCount', 'toNext', 'progress'));
    }
}
