<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class LevelService
{
    /**
     * Rules ko yahan define karo (badlao as per need).
     * - 2 => ["directs" => 2]
     * - 3 => ["directs" => 3, "each_of_first" => ["count" => 3, "needs_directs" => 3]]
     *
     * Future (4..15) yahan add kar sakte ho.
     */
    public static function rules(): array
    {
        return [
            2 => ['directs' => 2],
            3 => ['directs' => 3, 'each_of_first' => ['count' => 3, 'needs_directs' => 3]],
            // 4 => [...],
            // 5 => [...],
            // ...
        ];
    }

    /** User ke direct referrals (L1) */
    public static function directs(User $u): Collection
    {
        return User::select('id','name')
            ->where('referred_by', $u->id)
            ->orWhere('sponsor_id', $u->id) // safety for old data
            ->orderBy('id')
            ->get();
    }

    /** In parent IDs ke direct bacche (ek pass me) */
    public static function childrenOf(Collection $parentIds): Collection
    {
        if ($parentIds->isEmpty()) return collect();
        return User::select('id','name','referred_by','sponsor_id')
            ->whereIn('referred_by', $parentIds)
            ->orWhereIn('sponsor_id', $parentIds)
            ->get();
    }

    /** Check kare: user given rule ko meet karta hai ya nahi */
    public static function meetsRule(User $u, array $rule): bool
    {
        $directs = self::directs($u);
        if (($rule['directs'] ?? 0) > 0 && $directs->count() < $rule['directs']) {
            return false;
        }

        // each_of_first: e.g. first 3 directs -> each must have >=3 directs
        if (isset($rule['each_of_first'])) {
            $needCount   = (int)($rule['each_of_first']['count'] ?? 0);
            $needDirects = (int)($rule['each_of_first']['needs_directs'] ?? 0);

            if ($needCount > 0 && $needDirects > 0) {
                $firstN = $directs->take($needCount);
                if ($firstN->count() < $needCount) return false;

                $children = self::childrenOf($firstN->pluck('id'));
                // group by parent
                $byParent = $children->groupBy(function ($c) {
                    return $c->referred_by ?? $c->sponsor_id;
                });

                foreach ($firstN as $parent) {
                    $cnt = ($byParent->get($parent->id)?->count()) ?? 0;
                    if ($cnt < $needDirects) return false;
                }
            }
        }

        return true;
    }

    /** Calculate max level achievable per current rules */
    public static function computeLevel(User $u): int
    {
        $level = (int)($u->level ?? 1);
        foreach (self::rules() as $lvl => $rule) {
            if (self::meetsRule($u, $rule)) {
                $level = max($level, (int)$lvl);
            }
        }
        return max(1, $level);
    }

    /** Save if changed; return new level */
    public static function recalcAndSave(User $u): int
    {
        $new = self::computeLevel($u);
        if ((int)$u->level !== $new) {
            $u->level = $new;
            $u->saveQuietly();
        }
        return $new;
    }

    /**
     * Progress to next level (UI bar ke liye):
     * - Level 1 -> target: 2 directs
     * - Level 2 -> target: 3 directs + (first 3 each having 3) = 12 slots
     */
    public static function progress(User $u): array
    {
        $current = (int)($u->level ?? 1);
        $directs = self::directs($u);

        if ($current < 2) {
            $target = 2;
            $have   = min($directs->count(), $target);
            $toNext = max(0, $target - $have);
            $pct    = (int)round(($have / $target) * 100);
            return [$toNext, $pct, 'Need '.$toNext.' more directs to reach Level 2'];
        }

        if ($current < 3) {
            // 12 = 3 directs + 3 each (9)
            $first3   = $directs->take(3);
            $haveTop  = min(3, $first3->count());

            $kids   = self::childrenOf($first3->pluck('id'));
            $byPar  = $kids->groupBy(fn($c)=> $c->referred_by ?? $c->sponsor_id);

            $grand = 0;
            foreach ($first3 as $p) {
                $grand += min(3, ($byPar->get($p->id)?->count()) ?? 0);
            }

            $have   = $haveTop + $grand; // max 12
            $target = 12;
            $toNext = max(0, $target - $have);
            $pct    = (int)round(($have / $target) * 100);
            return [$toNext, $pct, 'Build 3 directs; each with 3 directs (total 12) for Level 3'];
        }

        // For 3+ (future rules define karo). Abhi 100% dikhado.
        return [0, 100, 'Max per current rules'];
    }
}
