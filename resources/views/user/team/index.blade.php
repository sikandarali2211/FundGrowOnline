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
        return [root,...L1withL2,...L2];
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
