@extends('layouts.user')

@section('content')
<style>
    :root {
        --cA: #44bd32;
        --cB: #f39c12;
        --cC: #8e44ad;
        --bgDark: #0b1f2a;
        --line: #1dd1a1;
    }

    /* ==== FIX 1: wider scroll area + side gutters so ends don't cut ==== */
    .org-wrap {
        overflow-x: auto;
        padding: 20px 0;
    }

    .org-shell {
        min-width: 1100px;
    }

    .org-canvas {
        width: max-content;
        margin: 0 auto;
        padding: 0 160px;
    }

    /* side gutters */

    /* Top YOU node */
    .node-circle {
        width: 108px;
        height: 108px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 700;
        font-size: 28px;
        letter-spacing: 1px;
        border: 6px solid rgba(255, 255, 255, .14);
        box-shadow: 0 12px 28px rgba(0, 0, 0, .25), inset 0 0 0 6px rgba(255, 255, 255, .06);
    }

    .node-you {
        background: radial-gradient(70% 70% at 30% 30%, #ff7e6b, #b71f1f);
        border-color: #ffb3a8;
        position: relative;
    }

    .node-you::before {
        /* ==== FIX 2: CSS crown (no FA needed) ==== */
        content: "👑";
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 20px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, .35));
    }

    .node-a {
        background: radial-gradient(70% 70% at 30% 30%, #58d68d, #1e8449);
        border-color: #aef1c7;
    }

    .node-b {
        background: radial-gradient(70% 70% at 30% 30%, #ffd37a, #d68910);
        border-color: #ffe1a6;
    }

    .node-c {
        background: radial-gradient(70% 70% at 30% 30%, #be9cff, #6c3483);
        border-color: #d9c8ff;
    }

    .name-box {
        background: #fff;
        border-radius: 12px;
        padding: 10px 14px;
        margin-left: 14px;
        min-width: 220px;
        max-width: 300px;
        box-shadow: 0 10px 22px rgba(0, 0, 0, .12);
    }

    .name-box .nm {
        font-weight: 700;
        color: #0c1b2b;
        margin: 0
    }

    .name-box .meta {
        font-size: 12px;
        color: #6b7b8c;
        margin-top: 4px
    }

    .group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 auto;
        width: max-content
    }

    .team-label {
        text-align: center;
        margin-top: 10px;
        font-weight: 600;
        color: #2b3c4d
    }

    .team-label small {
        display: block;
        color: #8aa0b4;
        font-weight: 500
    }

    .pipe-v {
        width: 2px;
        height: 26px;
        background: var(--line);
        margin: 8px auto 0
    }

    .pipe-h {
        height: 2px;
        background: var(--line);
        width: calc(100% - 120px);
        margin: 18px auto 12px;
    }

    /* centered line with margins */

    /* children row */
    .child-row {
        display: flex;
        gap: 18px;
        justify-content: center;
        flex-wrap: nowrap
    }

    .child .node-circle {
        width: 70px;
        height: 70px;
        border-width: 5px;
        font-size: 18px
    }

    .child .cap {
        font-size: 12px;
        color: #637b90;
        margin-top: 6px;
        text-align: center;
        max-width: 90px
    }

    .card-dark {
        border: 0;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .15)
    }

    .head-stat .badge-soft {
        background: rgba(29, 209, 161, .15);
        color: #1dd1a1;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 12px
    }

    .progress {
        background: #0b2030;
        height: 8px;
        border-radius: 999px;
        overflow: hidden
    }

    .progress>span {
        display: block;
        height: 100%;
        background: #1dd1a1
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">

            {{-- ===== Header / Status ===== --}}
            <div class="card card-dark mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 head-stat">
                        <div>
                            <h5 class="mb-1">My Team</h5>
                            <div class="text-muted small">Current Level:
                                <span class="badge-soft">Level {{ $me->level }}</span>
                            </div>
                        </div>
                        <div style="min-width:260px">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Directs: {{ $directCount }}/12</span><span>{{ $progress }}%</span>
                            </div>
                            <div class="progress mb-1"><span style="width:{{ $progress }}%"></span></div>
                            @if($toNext>0)
                            <div class="small text-muted">Need {{ $toNext }} more directs to reach <b>Level 2</b>.</div>
                            @else
                            <div class="small text-success">Congrats! You have unlocked <b>Level 2</b>.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== ORG CHART ===== --}}
            <div class="card card-dark">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Organization (You → Directs → Their Teams)</h6>
                </div>

                <div class="card-body org-wrap">
                    <div class="org-shell">
                        <div class="org-canvas">

                            {{-- YOU (top) --}}
                            <div class="text-center" style="width:max-content; margin:0 auto">
                                <div class="node-circle node-you">{{ strtoupper(mb_substr($me->name,0,1)) }}</div>
                                <div class="text-muted small mt-1">YOU • Code: {{ $me->referral_code }}</div>
                            </div>

                            <div class="pipe-v"></div>
                            <div class="pipe-h"></div>

                            {{-- LEVEL 1 --}}
                            @php
                            $labels=['Team A','Team B','Team C','Team D','Team E'];
                            $colorClass=['node-a','node-b','node-c','node-a','node-b'];
                            $initials=function($nm){
                            $parts=preg_split('/\s+/', trim($nm));
                            $a=mb_substr($parts[0]??'',0,1);
                            $b=count($parts)>1?mb_substr(end($parts),0,1):'';
                            return strtoupper($a.$b);
                            };
                            @endphp

                            <div class="d-flex justify-content-center gap-4 flex-nowrap" style="margin-bottom:8px">
                                @forelse($level1 as $i=>$l1)
                                @php
                                $kids = $level2->get($l1->id) ?? collect();
                                @endphp

                                <div class="text-center" style="min-width:320px">
                                    <div class="group">
                                        <div class="node-circle {{ $colorClass[$i % count($colorClass)] }}">
                                            {{ $initials($l1->name) }}
                                        </div>
                                        <div class="name-box">
                                            <p class="nm">{{ $l1->name }}</p>
                                            <div class="meta">Code: {{ $l1->referral_code }}</div>
                                            <div class="meta">Joined: {{ $l1->created_at?->format('d M Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="pipe-v"></div>

                                    @if($kids->isNotEmpty())
                                    <div class="child-row mt-1">
                                        @foreach($kids as $l2)
                                        <div class="child">
                                            <div class="node-circle {{ $colorClass[$i % count($colorClass)] }}">{{ $initials($l2->name) }}</div>
                                            <div class="cap">{{ $l2->name }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-muted small mt-1">No level-2 yet</div>
                                    @endif

                                    <div class="team-label mt-2">
                                        {{ $labels[$i % count($labels)] }}
                                        <small>{{ $kids->count() }} member(s)</small>
                                    </div>
                                </div>
                                @empty
                                <div class="text-muted">No direct referrals yet.</div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection