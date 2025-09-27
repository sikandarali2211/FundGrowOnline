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
        $level1 = $level1 ?? collect();

        /** flat nodes for charts - Level 1 only */
        $nodes = [];
        $nodes[] = [
            'id' => (int) $me->id,
            'parentId' => null,
            'name' => (string) $me->name,
            'code' => (string) $me->referral_code,
            'joined' => optional($me->created_at)->format('d M Y'),
            'type' => 'me',
            'g' => 'g-me',
        ];
        $palette = ['g-a', 'g-b', 'g-c', 'g-a', 'g-b'];
        $i = 0;
        foreach ($level1 as $l1) {
            $g = $palette[$i % count($palette)];
            $nodes[] = [
                'id' => (int) $l1->id,
                'parentId' => (int) $me->id,
                'name' => (string) $l1->name,
                'code' => (string) $l1->referral_code,
                'joined' => optional($l1->created_at)->format('d M Y'),
                'type' => 'l1',
                'g' => $g,
            ];
            $i++;
        }
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
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Level Filter Dropdown --}}
                                    <div class="level-filter">
                                        <label for="levelFilter" class="form-label small text-muted mb-1">Filter by
                                            Level:</label>
                                        <select id="levelFilter" class="form-select level-dropdown">
                                            <option value="all">All Levels</option>
                                            <option value="1">Level 1 Only</option>
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
                    <div class="card card-dark mb-4 chart-container" data-level="all">
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



                </div>
            </div>
        </div>
    </div>
    <script type="module">
        (async () => {
            // ====== CDN loader ======
            const D3 = ['https://esm.sh/d3@7', 'https://unpkg.com/d3@7?module'];
            const ORG = ['https://esm.sh/d3-org-chart@3', 'https://unpkg.com/d3-org-chart@3.2.0?module'];
            async function tryImport(list) {
                let e;
                for (const u of list) {
                    try {
                        return await import(u);
                    } catch (err) {
                        e = err;
                    }
                }
                throw e;
            }

            // Chart canvases - Level 1 only
            const boxes = {
                all: document.getElementById('chart-all'),
                l1: document.getElementById('chart-l1'),
            };
            Object.values(boxes).forEach(b => {
                if (!b) return;
                if (!b.querySelector('.status-pill')) {
                    const pill = document.createElement('span');
                    pill.className = 'status-pill';
                    pill.textContent = 'Loading…';
                    b.appendChild(pill);
                }
            });

            let OrgModule;
            try {
                await tryImport(D3);
                OrgModule = await tryImport(ORG);
            } catch (e) {
                Object.values(boxes).forEach(b => b.innerHTML =
                    '<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">CDN blocked</div>');
                return;
            }
            const Chart = OrgModule.OrgChart || OrgModule.default || (OrgModule.default && OrgModule.default
                .OrgChart);
            if (!Chart) {
                Object.values(boxes).forEach(b => b.innerHTML =
                    '<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">OrgChart export missing</div>'
                );
                return;
            }

            // ====== Data from PHP ======
            const RAW = @json($nodes ?? []);
            console.log('Raw data from PHP:', RAW);
            
            const data = (Array.isArray(RAW) ? RAW : []).map(n => ({
                ...n,
                id: n?.id != null ? parseInt(n.id, 10) : null,
                parentId: n?.parentId != null ? parseInt(n.parentId, 10) : null,
                name: n?.name ?? ''
            })).filter(n => n && n.id != null);
            
            console.log('Processed data:', data);
            console.log('Data length:', data.length);

            const esc = s => (s == null) ? '' : (s + '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g,
                '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            const initials = name => {
                const p = (name || '').trim().split(/\s+/);
                return (((p[0] || '?')[0] || '?') + (p.length > 1 ? (p[p.length - 1][0] || '') : ''))
                    .toUpperCase();
            };
            const nodeHTML = d => {
                const n = d.data || d,
                    g = n.g || 'g-a',
                    me = n.type === 'me';
                return `<div class="org-node ${me?'me':''}">
      <div class="avatar ${me?'g-me':g}">${initials(n.name)}</div>
      <div class="info">
        <div class="nm">${esc(n.name)}</div>
        <div class="meta">Code: ${esc(n.code||'-')}</div>
        ${n.joined ? `<div class="meta">Joined: ${esc(n.joined)}</div>` : ``}
        ${me ? `<span class="tag">YOU</span>` : ``}
      </div>
    </div>`;
            };

            if (!data.length) {
                console.log('❌ No data found!');
                console.log('RAW data:', RAW);
                Object.values(boxes).forEach(b => b.innerHTML =
                    '<div style="height:100%;display:grid;place-items:center;color:#9bb3c7">No team data</div>');
                return;
            }
            
            console.log('✅ Data found, length:', data.length);

            // ====== Build ordered children map (data order preserve) ======
            const root = data.find(n => n.parentId == null) || data[0];
            const childrenMap = new Map(); // parentId -> [childNodeObjects in display order]
            for (const n of data) {
                if (n.parentId != null) {
                    const arr = childrenMap.get(n.parentId) || [];
                    arr.push(n);
                    childrenMap.set(n.parentId, arr);
                }
            }
            const L1 = childrenMap.get(root.id) || [];

            // ====== Subset makers ======
            // All levels (no limit)
            const subsetAll = () => data;

            // Level-1 requirement: exactly first 2 directs (if available) + YOU
            const subsetLevel1_Min2 = () => {
                const set = [root];
                set.push(...L1.slice(0, 2));
                return set;
            };

            // Level-2 requirement: 3 directs (A,B,C) + each 3 kids => 12 nodes + YOU on top
            const subsetLevel2_3x3 = () => {
                const set = [root];
                const top3 = L1.slice(0, 3);
                set.push(...top3);
                for (const p of top3) {
                    const kids = (childrenMap.get(p.id) || []).slice(0, 3);
                    set.push(...kids);
                }
                return set;
            };

            // Level 1 only: Show only Level 1 direct referrals + YOU
            const subsetLevel1Only = () => {
                const set = [root];
                set.push(...L1);
                return set;
            };


            // Level progression system - Level 1 only
            const checkLevelProgression = () => {
                const level1Count = L1.length;
                
                // Update level status display
                const level1Status = document.getElementById('level1Status');
                
                if (level1Status) {
                    level1Status.textContent = `Level 1: ${level1Count} users`;
                }
                
                // Always show Level 1
                document.querySelector('[data-level="1"]').style.display = 'block';
                document.querySelector('[data-level="all"]').style.display = 'block';
                
                return true;
            };

            // Show level progression message
            const showLevelProgressionMessage = (message) => {
                // Create or update progress message
                let progressDiv = document.getElementById('levelProgressMessage');
                if (!progressDiv) {
                    progressDiv = document.createElement('div');
                    progressDiv.id = 'levelProgressMessage';
                    progressDiv.className = 'alert alert-success alert-dismissible fade show';
                    progressDiv.style.marginBottom = '1rem';
                    
                    const closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.className = 'btn-close';
                    closeBtn.setAttribute('data-bs-dismiss', 'alert');
                    progressDiv.appendChild(closeBtn);
                    
                    // Insert after header
                    const header = document.querySelector('.card.card-dark.mb-4');
                    header.parentNode.insertBefore(progressDiv, header.nextSibling);
                }
                
                progressDiv.innerHTML = `
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <i class="fa fa-check-circle me-2"></i>${message}
                `;
            };

            // ====== Generic chart initializer ======
            function initChart(suffix, getSubset) {
                const hostId = `chart-${suffix}`;
                const host = document.getElementById(hostId);
                if (!host) return;

                const pill = host.querySelector('.status-pill');

                const chart = new Chart()
                    .container('#' + hostId)
                    .nodeId(d => d.id)
                    .parentNodeId(d => d.parentId)
                    .nodeWidth(() => 280)
                    .nodeHeight(d => (d?.data?.type === 'me' ? 110 : 92))
                    .childrenMargin(() => 40)
                    .compact(false)
                    .nodeContent(nodeHTML);

                const render = () => {
                    const ds = getSubset();
                    console.log(`Rendering chart ${suffix} with data:`, ds);
                    console.log(`Data length for ${suffix}:`, ds.length);
                    chart.data(ds).render().fit();
                    if (pill) pill.textContent = `Users: ${ds.length}`;
                };

                // Initial render
                render();

                // Toolbar binds
                const bind = (id, fn) => document.getElementById(id)?.addEventListener('click', fn);
                bind(`fit-${suffix}`, () => chart.fit());
                bind(`zin-${suffix}`, () => chart.zoomIn());
                bind(`zout-${suffix}`, () => chart.zoomOut());
                bind(`exp-${suffix}`, () => chart.expandAll());
                bind(`col-${suffix}`, () => chart.collapseAll());
                bind(`me-${suffix}`, () => chart.setCentered(root.id).fit());
                window.addEventListener('resize', () => chart.fit(), {
                    passive: true
                });

                return {
                    render
                };
            }

            // ====== Create the charts - Level 1 only ======
            console.log('Initializing charts...');
            const chartInstances = {
                all: initChart('all', subsetAll), // All levels (Level 1 only)
                l1: initChart('l1', subsetLevel1Only), // Level 1 only
            };
            console.log('Charts initialized:', chartInstances);

            // ====== Level Filter Functionality ======
            const levelFilter = document.getElementById('levelFilter');
            const chartContainers = document.querySelectorAll('.chart-container');

            function filterCharts(selectedLevel) {
                console.log('Filtering charts for level:', selectedLevel);

                chartContainers.forEach(container => {
                    const level = container.getAttribute('data-level');
                    console.log('Processing container with level:', level);

                    if (selectedLevel === 'all') {
                        // Show all charts
                        container.classList.remove('hidden');
                        container.style.display = 'block';
                        console.log('Showing all charts');
                    } else if (level === selectedLevel) {
                        // Show only the selected level
                        container.classList.remove('hidden');
                        container.style.display = 'block';
                        console.log('Showing level:', level);
                    } else {
                        // Hide other levels
                        container.classList.add('hidden');
                        container.style.display = 'none';
                        console.log('Hiding level:', level);
                    }
                });
            }

            // Add event listener for level filter
            if (levelFilter) {
                levelFilter.addEventListener('change', function() {
                    console.log('Level filter changed to:', this.value);
                    filterCharts(this.value);
                });
            }

            // Initialize level progression system
            checkLevelProgression();
            
            // Always show all levels by default
            filterCharts('all');
            
            // Debug: Show data in console and add fallback display
            console.log('=== FINAL DEBUG INFO ===');
            console.log('Total data nodes:', data.length);
            console.log('Level 1 users:', L1.length);
            console.log('Children map size:', childrenMap.size);
        })();
    </script>
@endsection
