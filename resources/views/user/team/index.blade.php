@extends('layouts.user')

@section('content')
    <style>
        :root {
            --gold: #f0c24b;
            --bgDark: #0b1f2a;
            --line: #1dd1a1;
            --cardGlass: linear-gradient(145deg, rgba(255, 215, 0, .03), rgba(0, 0, 0, .95));
        }

        .card-dark {
            border: 0;
            box-shadow: 0 8px 28px rgba(0, 0, 0, .15);
        }

        .head-stat .badge-soft {
            background: rgba(29, 209, 161, .15);
            color: #1dd1a1;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
        }

        .progress {
            background: #0b2030;
            height: 8px;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress>span {
            display: block;
            height: 100%;
            background: #1dd1a1;
        }

        .org-toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .org-toolbar .btn {
            background: #0e1f2d;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 13px;
        }

        .org-toolbar .btn:hover {
            background: #123246;
        }

        .org-toolbar .hint {
            font-size: 12px;
            color: #9bb3c7;
        }

        .orgbox {
            height: 520px;
            border-radius: 18px;
            background: var(--cardGlass);
            border: 1px solid rgba(255, 255, 255, .12);
            position: relative;
            overflow: hidden;
        }

        .org-node {
            width: 280px;
            height: 92px;
            background: #071724;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 16px;
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 10px;
            padding: 10px;
            color: #e8f2ff;
            box-shadow: 0 10px 22px rgba(0, 0, 0, .25), inset 0 1px 0 rgba(255, 255, 255, .04);
        }

        .org-node.me {
            height: 110px;
            border-color: rgba(240, 194, 75, .45);
            box-shadow: 0 14px 28px rgba(0, 0, 0, .35), 0 0 0 1px rgba(240, 194, 75, .15) inset;
        }

        .org-node .avatar {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 20px;
            color: #fff;
            border: 4px solid rgba(255, 255, 255, .10);
        }

        .org-node.me .avatar {
            width: 74px;
            height: 74px;
            border-radius: 18px;
            font-size: 24px;
            border-width: 5px;
        }

        .g-a {
            background: radial-gradient(70% 70% at 30% 30%, #58d68d, #1e8449);
        }

        .g-b {
            background: radial-gradient(70% 70% at 30% 30%, #ffd37a, #d68910);
        }

        .g-c {
            background: radial-gradient(70% 70% at 30% 30%, #be9cff, #6c3483);
        }

        .g-me {
            background: radial-gradient(70% 70% at 30% 30%, #ff7e6b, #b71f1f);
        }

        .org-node .info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .org-node .nm {
            font-weight: 700;
            font-size: 15px;
            line-height: 1.15;
            margin: 0 0 4px;
            color: #fff;
        }

        .org-node .meta {
            font-size: 12px;
            color: #9bb3c7;
        }

        .org-node .tag {
            display: inline-block;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(29, 209, 161, .15);
            color: #1dd1a1;
            margin-top: 6px;
        }

        .status-pill {
            position: absolute;
            right: 12px;
            top: 12px;
            z-index: 5;
            font: 12px/1.2 system-ui;
            background: #0e1f2d;
            color: #bfe8ff;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 999px;
            padding: 6px 10px;
        }

        .level-filter {
            min-width: 150px;
        }

        .level-dropdown {
            background: #0e1f2d;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .level-dropdown:focus {
            background: #123246;
            border-color: rgba(29, 209, 161, .5);
            box-shadow: 0 0 0 2px rgba(29, 209, 161, .2);
            outline: none;
        }

        .level-dropdown option {
            background: #0e1f2d;
            color: #fff;
            padding: 8px;
        }

        .chart-container {
            transition: all 0.3s ease;
        }

        .chart-container.hidden {
            display: none !important;
            opacity: 0;
            pointer-events: none;
        }

        /* Dark Glassy Card */
        .card-dark {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            overflow: hidden;
        }

        /* Card Heading */
        .head-stat h5 {
            font-weight: 600;
            margin-bottom: 4px;
            color: #fff;
        }

        .head-stat .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Level Badge */
        .badge-soft {
            background: rgba(59, 209, 122, 0.15);
            color: #3bd17a;
            border-radius: 8px;
            padding: 3px 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Level Filter Dropdown */
        .level-dropdown {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #f1f1f1;
            padding: 6px 12px;
            min-width: 150px;
            transition: 0.2s;
        }

        .level-dropdown:focus {
            outline: none;
            border-color: #3bd17a;
            box-shadow: 0 0 6px rgba(59, 209, 122, 0.4);
        }

        /* Progress Bar */
        .progress {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }

        .progress span {
            display: block;
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #3bd17a, #29a35c);
            transition: width 0.4s ease;
        }

        /* Success & Muted Text */
        .text-success {
            color: #3bd17a !important;
        }
    </style>

    @php
        // just enhance, don’t reset
        $nodes = collect($nodes ?? [])->map(function($n, $i) {
            if ($n['type'] === 'me') {
                $n['g'] = 'g-me';
            } elseif ($n['type'] === 'l1') {
                $palette = ['g-a','g-b','g-c'];
                $n['g'] = $palette[$i % count($palette)];
            } else {
                $n['g'] = 'g-b';
            }
            return $n;
        })->all();
    @endphp

    <div class="main-panel">
        <div class="container py-4" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); margin-top:4rem;">
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-10">
                    {{-- header --}}
                    <div class="card card-dark mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 head-stat">
                                    <div>
                                        <h5 class="mb-1">My Team</h5>
                                        <div class="text-muted small">Current Level: <span class="badge-soft">Level
                                                {{ $me->level }}</span></div>
                                        <div id="levelProgressStatus" class="text-muted small mt-1">
                                            <span id="level1Status">Level 1: {{ $directCount ?? 0 }}/3 users</span>
                                        </div>
                                        @php
                                            $isChildOfSomeone = $me->referred_by !== null;
                                            $level2NodeCount = collect($nodes ?? [])->where('type', 'l2')->count();
                                        @endphp
                                        <div class="text-muted small mt-1">
                                            <span class="badge-soft">{{ $isChildOfSomeone ? 'Child Dashboard' : 'Root Dashboard' }}</span>
                                            <span class="badge-soft ms-1">L2 Nodes: {{ $level2NodeCount }}</span>
                                        </div>
                                    </div>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Level Filter Dropdown --}}
                                    <div class="level-filter">
                                        <label for="levelFilter" class="form-label small text-muted mb-1">Filter by
                                            Level:</label>
                                        <select id="levelFilter" class="form-select level-dropdown">
                                            <option value="all">All Levels</option> 
                                            <option value="1">Level 1 Only</option> 
                                            <option value="2">Level 2 Only</option> 
                                            <option value="3">Level 3 Only</option> 
                                            <option value="4">Level 4 Only</option> 
                                            <option value="5">Level 5 Only</option> 
                                            <option value="6">Level 6 Only</option> 
                                            <option value="7">Level 7 Only</option> 
                                            <option value="8">Level 8 Only</option> 
                                            <option value="9">Level 9 Only</option> 
                                            <option value="10">Level 10 Only</option>
                                            <option value="11">Level 11 Only</option> 
                                            <option value="12">Level 12 Only</option>
                                            <option value="13">Level 13 Only</option> 
                                            <option value="14">Level 14 Only</option> 
                                            <option value="15">Level 15 Only</option>
                                        </select>
                                    </div>
                                    <div style="min-width:260px">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Directs:
                                                {{ $directCount ?? 0 }}/12</span><span>{{ $progress ?? 0 }}%</span>
                                        </div>
                                        <div class="progress mb-1"><span style="width:{{ $progress ?? 0 }}%"></span></div>
                                        <div class="small {{ ($toNext ?? 0) > 0 ? 'text-muted' : 'text-success' }}">
                                            {{ $progressText ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========== CHART: ALL LEVELS ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="all" hidden>
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">All Levels</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-all">Fit</button>
                                    <button class="btn" id="zin-all">Zoom In</button>
                                    <button class="btn" id="zout-all">Zoom Out</button>
                                    <button class="btn" id="exp-all">Expand</button>
                                    <button class="btn" id="col-all">Collapse</button>
                                    <button class="btn" id="me-all">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-all" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 1 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="1">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 1 Direct Referrals</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l1">Fit</button>
                                    <button class="btn" id="zin-l1">Zoom In</button>
                                    <button class="btn" id="zout-l1">Zoom Out</button>
                                    <button class="btn" id="exp-l1">Expand</button>
                                    <button class="btn" id="col-l1">Collapse</button>
                                    <button class="btn" id="me-l1">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l1" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>
                    {{-- ========== CHART: LEVEL 2 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="2">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 2 Users (Second Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l2">Fit</button>
                                    <button class="btn" id="zin-l2">Zoom In</button>
                                    <button class="btn" id="zout-l2">Zoom Out</button>
                                    <button class="btn" id="exp-l2">Expand</button>
                                    <button class="btn" id="col-l2">Collapse</button>
                                    <button class="btn" id="me-l2">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l2" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>
                    {{-- ========== CHART: LEVEL 3 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="3">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 3 Users (Third Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l3">Fit</button>
                                    <button class="btn" id="zin-l3">Zoom In</button>
                                    <button class="btn" id="zout-l3">Zoom Out</button>
                                    <button class="btn" id="exp-l3">Expand</button>
                                    <button class="btn" id="col-l3">Collapse</button>
                                    <button class="btn" id="me-l3">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l3" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 4 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="4">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 4 Users (Fourth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l4">Fit</button>
                                    <button class="btn" id="zin-l4">Zoom In</button>
                                    <button class="btn" id="zout-l4">Zoom Out</button>
                                    <button class="btn" id="exp-l4">Expand</button>
                                    <button class="btn" id="col-l4">Collapse</button>
                                    <button class="btn" id="me-l4">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l4" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 5 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="5">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 5 Users (Fifth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l5">Fit</button>
                                    <button class="btn" id="zin-l5">Zoom In</button>
                                    <button class="btn" id="zout-l5">Zoom Out</button>
                                    <button class="btn" id="exp-l5">Expand</button>
                                    <button class="btn" id="col-l5">Collapse</button>
                                    <button class="btn" id="me-l5">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l5" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 6 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="6">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 6 Users (Sixth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l6">Fit</button>
                                    <button class="btn" id="zin-l6">Zoom In</button>
                                    <button class="btn" id="zout-l6">Zoom Out</button>
                                    <button class="btn" id="exp-l6">Expand</button>
                                    <button class="btn" id="col-l6">Collapse</button>
                                    <button class="btn" id="me-l6">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l6" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>
                    {{-- ========== CHART: LEVEL 7 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="7">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 7 Users (Seventh Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l7">Fit</button>
                                    <button class="btn" id="zin-l7">Zoom In</button>
                                    <button class="btn" id="zout-l7">Zoom Out</button>
                                    <button class="btn" id="exp-l7">Expand</button>
                                    <button class="btn" id="col-l7">Collapse</button>
                                    <button class="btn" id="me-l7">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l7" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 8 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="8">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 8 Users (Eighth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l8">Fit</button>
                                    <button class="btn" id="zin-l8">Zoom In</button>
                                    <button class="btn" id="zout-l8">Zoom Out</button>
                                    <button class="btn" id="exp-l8">Expand</button>
                                    <button class="btn" id="col-l8">Collapse</button>
                                    <button class="btn" id="me-l8">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l8" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 9 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="9">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 9 Users (Ninth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l9">Fit</button>
                                    <button class="btn" id="zin-l9">Zoom In</button>
                                    <button class="btn" id="zout-l9">Zoom Out</button>
                                    <button class="btn" id="exp-l9">Expand</button>
                                    <button class="btn" id="col-l9">Collapse</button>
                                    <button class="btn" id="me-l9">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l9" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 10 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="10">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 10 Users (Tenth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l10">Fit</button>
                                    <button class="btn" id="zin-l10">Zoom In</button>
                                    <button class="btn" id="zout-l10">Zoom Out</button>
                                    <button class="btn" id="exp-l10">Expand</button>
                                    <button class="btn" id="col-l10">Collapse</button>
                                    <button class="btn" id="me-l10">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l10" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 11 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="11">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 11 Users (Eleventh Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l11">Fit</button>
                                    <button class="btn" id="zin-l11">Zoom In</button>
                                    <button class="btn" id="zout-l11">Zoom Out</button>
                                    <button class="btn" id="exp-l11">Expand</button>
                                    <button class="btn" id="col-l11">Collapse</button>
                                    <button class="btn" id="me-l11">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l11" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 12 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="12">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 12 Users (Twelfth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l12">Fit</button>
                                    <button class="btn" id="zin-l12">Zoom In</button>
                                    <button class="btn" id="zout-l12">Zoom Out</button>
                                    <button class="btn" id="exp-l12">Expand</button>
                                    <button class="btn" id="col-l12">Collapse</button>
                                    <button class="btn" id="me-l12">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l12" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 13 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="13">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 13 Users (Thirteenth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l13">Fit</button>
                                    <button class="btn" id="zin-l13">Zoom In</button>
                                    <button class="btn" id="zout-l13">Zoom Out</button>
                                    <button class="btn" id="exp-l13">Expand</button>
                                    <button class="btn" id="col-l13">Collapse</button>
                                    <button class="btn" id="me-l13">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l13" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 14 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="14">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 14 Users (Fourteenth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l14">Fit</button>
                                    <button class="btn" id="zin-l14">Zoom In</button>
                                    <button class="btn" id="zout-l14">Zoom Out</button>
                                    <button class="btn" id="exp-l14">Expand</button>
                                    <button class="btn" id="col-l14">Collapse</button>
                                    <button class="btn" id="me-l14">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l14" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>

                    {{-- ========== CHART: LEVEL 15 ONLY ========== --}}
                    <div class="card card-dark mb-4 chart-container" data-level="15">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Level 15 Users (Fifteenth Plan Purchased)</h6>
                            <div class="org-toolbar">
                                <div class="d-flex gap-2">
                                    <button class="btn" id="fit-l15">Fit</button>
                                    <button class="btn" id="zin-l15">Zoom In</button>
                                    <button class="btn" id="zout-l15">Zoom Out</button>
                                    <button class="btn" id="exp-l15">Expand</button>
                                    <button class="btn" id="col-l15">Collapse</button>
                                    <button class="btn" id="me-l15">Center on Me</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-l15" class="orgbox"><span class="status-pill">Loading…</span></div>
                        </div>
                    </div>


                    
                

                </div>
            </div>
        </div>
    </div>
    <script type="module">
(async () => {
    const D3 = ['https://esm.sh/d3@7','https://unpkg.com/d3@7?module'];
    const ORG = ['https://esm.sh/d3-org-chart@3','https://unpkg.com/d3-org-chart@3.2.0?module'];
    async function tryImport(list){
        let e; for(const u of list){try{return await import(u);}catch(err){e=err;}}
        throw e;
    }

    const boxes = {
        all: document.getElementById('chart-all'),
        l1: document.getElementById('chart-l1'),
        l2: document.getElementById('chart-l2'),
        l3: document.getElementById('chart-l3'),
        l4: document.getElementById('chart-l4'),
        l5: document.getElementById('chart-l5'),
        l6: document.getElementById('chart-l6'),
        l7: document.getElementById('chart-l7'),
        l8: document.getElementById('chart-l8'),
        l9: document.getElementById('chart-l9'),
        l10: document.getElementById('chart-l10'),
        l11: document.getElementById('chart-l11'),
        l12: document.getElementById('chart-l12'),
        l13: document.getElementById('chart-l13'),
        l14: document.getElementById('chart-l14'),
        l15: document.getElementById('chart-l15'),
    };

    let OrgModule;
    try {
        await tryImport(D3);
        OrgModule = await tryImport(ORG);
    } catch {
        Object.values(boxes).forEach(b=>b.innerHTML='<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">CDN blocked</div>');
        return;
    }
    const Chart = OrgModule.OrgChart || OrgModule.default;
    if(!Chart){
        Object.values(boxes).forEach(b=>b.innerHTML='<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">OrgChart missing</div>');
        return;
    }

    // ====== Data from PHP ======
    let raw = @json($nodes ?? []);
    let data = (Array.isArray(raw)?raw:[]).map(n=>({
        ...n,
        id: n?.id ?? null,
        parentId: n?.parentId ?? null,
        name: n?.name ?? ''
    })).filter(n=>n && n.id!=null);


    // Ensure single root
    if(!data.some(n => n.parentId === null)){
        if(data.length){
            data[0].parentId = null;
        }
    }

    // Deduplicate IDs
    const seen=new Set();
    data=data.filter(n=>{
        if(seen.has(n.id)) return false;
        seen.add(n.id); return true;
    });

    if(!data.length){
        Object.values(boxes).forEach(b=>b.innerHTML='<div style="height:100%;display:grid;place-items:center;color:#9bb3c7">No team data</div>');
        return;
    }

    const root = data.find(n => n.parentId == null);

    const esc=s=>(s??'').replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;");
    const initials=name=>{
        const p=(name||'').trim().split(/\s+/);
        return (((p[0]||'?')[0]||'?')+(p.length>1?(p[p.length-1][0]||''):'' )).toUpperCase();
    };
    const nodeHTML=d=>{
        const n=d.data||d, g=n.g||'g-a', me=n.type==='me';
        return `<div class="org-node ${me?'me':''}">
          <div class="avatar ${me?'g-me':g}">${initials(n.name)}</div>
          <div class="info">
            <div class="nm">${esc(n.name)}</div>
            <div class="meta">Code: ${esc(n.code||'-')}</div>
            ${n.joined?`<div class="meta">Joined: ${esc(n.joined)}</div>`:``}
            ${me?`<span class="tag">YOU</span>`:``}
          </div>
        </div>`;
    };

    // Subsets
    const subsetAll=()=>data;
    const subsetLevel1Only=()=>[root,...data.filter(n=>n.type==='l1')];
    const subsetLevel2Only=()=>{
        const L2=data.filter(n=>n.type==='l2');
        const L1withL2=data.filter(n=>n.type==='l1' && L2.some(l2=>l2.parentId===n.id));
        const rootWithL2=L2.some(l2=>l2.parentId===root.id);
        // For child dashboards, show root + all L2 nodes (they're children of the child user)
        if(rootWithL2) {
            return [root,...L2];
        }
        return [root,...L1withL2,...L2];
    };
    const subsetLevel3Only=()=> {
        const L3=data.filter(n=>n.type==='l3');
        const L1L2withL3=data.filter(n=>(n.type==='l1'||n.type==='l2') && L3.some(l3=>l3.parentId===n.id));
        return [root,...L1L2withL3,...L3];
    };
    const subsetLevel4Only=()=> {
        const L4=data.filter(n=>n.type==='l4');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3') && L4.some(l4=>l4.parentId===n.id));
        return [root,...parents,...L4];
    };
    const subsetLevel5Only=()=> {
        const L5=data.filter(n=>n.type==='l5');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4') && L5.some(l5=>l5.parentId===n.id));
        return [root,...parents,...L5];
    };
    const subsetLevel6Only=()=> {
        const L6=data.filter(n=>n.type==='l6');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5') && L6.some(l6=>l6.parentId===n.id));
        return [root,...parents,...L6];
    };
    const subsetLevel7Only=()=> {
        const L7=data.filter(n=>n.type==='l7');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6') && L7.some(l7=>l7.parentId===n.id));
        return [root,...parents,...L7];
    };
    const subsetLevel8Only=()=> {
        const L8=data.filter(n=>n.type==='l8');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7') && L8.some(l8=>l8.parentId===n.id));
        return [root,...parents,...L8];
    };
    const subsetLevel9Only=()=> {
        const L9=data.filter(n=>n.type==='l9');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8') && L9.some(l9=>l9.parentId===n.id));
        return [root,...parents,...L9];
    };
    const subsetLevel10Only=()=> {
        const L10=data.filter(n=>n.type==='l10');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9') && L10.some(l10=>l10.parentId===n.id));
        return [root,...parents,...L10];
    };
    const subsetLevel11Only=()=> {
        const L11=data.filter(n=>n.type==='l11');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9'||n.type==='l10') && L11.some(l11=>l11.parentId===n.id));
        return [root,...parents,...L11];
    };
    const subsetLevel12Only=()=> {
        const L12=data.filter(n=>n.type==='l12');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9'||n.type==='l10'||n.type==='l11') && L12.some(l12=>l12.parentId===n.id));
        return [root,...parents,...L12];
    };
    const subsetLevel13Only=()=> {
        const L13=data.filter(n=>n.type==='l13');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9'||n.type==='l10'||n.type==='l11'||n.type==='l12') && L13.some(l13=>l13.parentId===n.id));
        return [root,...parents,...L13];
    };
    const subsetLevel14Only=()=> {
        const L14=data.filter(n=>n.type==='l14');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9'||n.type==='l10'||n.type==='l11'||n.type==='l12'||n.type==='l13') && L14.some(l14=>l14.parentId===n.id));
        return [root,...parents,...L14];
    };
    const subsetLevel15Only=()=> {
        const L15=data.filter(n=>n.type==='l15');
        const parents=data.filter(n=>(n.type==='l1'||n.type==='l2'||n.type==='l3'||n.type==='l4'||n.type==='l5'||n.type==='l6'||n.type==='l7'||n.type==='l8'||n.type==='l9'||n.type==='l10'||n.type==='l11'||n.type==='l12'||n.type==='l13'||n.type==='l14') && L15.some(l15=>l15.parentId===n.id));
        return [root,...parents,...L15];
    };


    // Chart init
    function initChart(suffix,getSubset){
        const hostId=`chart-${suffix}`,host=document.getElementById(hostId);
        if(!host) return;
        const pill=host.querySelector('.status-pill');
        const chart=new Chart()
            .container('#'+hostId)
            .nodeId(d=>d.id)
            .parentNodeId(d=>d.parentId)
            .nodeWidth(()=>280)
            .nodeHeight(d=>d?.data?.type==='me'?110:92)
            .childrenMargin(()=>40)
            .compact(false)
            .nodeContent(nodeHTML);
        const render=()=>{
            const ds=getSubset();
            if(!ds.length){
                host.innerHTML='<div style="height:100%;display:grid;place-items:center;color:#9bb3c7">No nodes</div>';
                return;
            }
            chart.data(ds).render().fit();
            if(pill) pill.textContent=`Users: ${ds.length}`;
        };
        render();
        return {render};
    }

    const charts={
        all:initChart('all',subsetAll),
        l1:initChart('l1',subsetLevel1Only),
        l2:initChart('l2',subsetLevel2Only),
        l3:initChart('l3',subsetLevel3Only),
        l4:initChart('l4',subsetLevel4Only),
        l5:initChart('l5',subsetLevel5Only),
        l6:initChart('l6',subsetLevel6Only),
        l7:initChart('l7',subsetLevel7Only),
        l8:initChart('l8',subsetLevel8Only),
        l9:initChart('l9',subsetLevel9Only),
        l10:initChart('l10',subsetLevel10Only),
        l11:initChart('l11',subsetLevel11Only),
        l12:initChart('l12',subsetLevel12Only),
        l13:initChart('l13',subsetLevel13Only),
        l14:initChart('l14',subsetLevel14Only),
        l15:initChart('l15',subsetLevel15Only),
    };

    const levelFilter=document.getElementById('levelFilter');
    const chartContainers=document.querySelectorAll('.chart-container');
    function filterCharts(level){
        chartContainers.forEach(c=>{
            if(level==='all' || c.getAttribute('data-level')===level){
                c.classList.remove('hidden'); c.style.display='block';
            } else {
                c.classList.add('hidden'); c.style.display='none';
            }
        });
    }
    if(levelFilter){
        levelFilter.addEventListener('change',function(){filterCharts(this.value);});
    }
    filterCharts('all');
})();
</script>


@endsection
