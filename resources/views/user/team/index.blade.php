@extends('layouts.user')

@section('content')
<style>
    :root {
        --gold: #f0c24b;
        --bgDark: #0b1f2a;
        --line: #1dd1a1;
        --cardGlass: linear-gradient(145deg, rgba(255, 215, 0, 0.03), rgba(0, 0, 0, 0.95));
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

    .org-toolbar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px
    }

    .org-toolbar .btn {
        background: #0e1f2d;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .08);
        padding: 6px 10px;
        border-radius: 10px;
        font-size: 13px
    }

    .org-toolbar .btn:hover {
        background: #123246
    }

    .org-toolbar .hint {
        font-size: 12px;
        color: #9bb3c7
    }

    #orgchart {
        height: 560px;
        border-radius: 18px;
        background: var(--cardGlass);
        border: 1px solid rgba(255, 255, 255, .12);
        position: relative;
        overflow: hidden
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
        box-shadow: 0 10px 22px rgba(0, 0, 0, .25), inset 0 1px 0 rgba(255, 255, 255, .04)
    }

    .org-node.me {
        height: 110px;
        border-color: rgba(240, 194, 75, .45);
        box-shadow: 0 14px 28px rgba(0, 0, 0, .35), 0 0 0 1px rgba(240, 194, 75, .15) inset
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
        border: 4px solid rgba(255, 255, 255, .10)
    }

    .org-node.me .avatar {
        width: 74px;
        height: 74px;
        border-radius: 18px;
        font-size: 24px;
        border-width: 5px
    }

    .g-a {
        background: radial-gradient(70% 70% at 30% 30%, #58d68d, #1e8449)
    }

    .g-b {
        background: radial-gradient(70% 70% at 30% 30%, #ffd37a, #d68910)
    }

    .g-c {
        background: radial-gradient(70% 70% at 30% 30%, #be9cff, #6c3483)
    }

    .g-me {
        background: radial-gradient(70% 70% at 30% 30%, #ff7e6b, #b71f1f)
    }

    .org-node .info {
        display: flex;
        flex-direction: column;
        justify-content: center
    }

    .org-node .nm {
        font-weight: 700;
        font-size: 15px;
        line-height: 1.15;
        margin: 0 0 4px;
        color: #fff
    }

    .org-node .meta {
        font-size: 12px;
        color: #9bb3c7
    }

    .org-node .tag {
        display: inline-block;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        background: rgba(29, 209, 161, .15);
        color: #1dd1a1;
        margin-top: 6px
    }

    .link {
        stroke: var(--line) !important;
        stroke-width: 1.5px !important;
        opacity: .85
    }
</style>

@php
// --- Safe defaults ---
$level1 = $level1 ?? collect();
$level2 = $level2 ?? collect();

// --- Build flat nodes for d3-org-chart ---
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
$palette = ['g-a','g-b','g-c','g-a','g-b']; $i=0;

foreach($level1 as $l1){
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
$kids = $level2->get($l1->id) ?? collect();
foreach($kids as $l2){
$nodes[] = [
'id' => (int) $l2->id,
'parentId' => (int) $l1->id,
'name' => (string) $l2->name,
'code' => (string) $l2->referral_code,
'joined' => optional($l2->created_at)->format('d M Y'),
'type' => 'l2',
'g' => $g,
];
}
$i++;
}
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">
            <div class="card card-dark mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 head-stat">
                        <div>
                            <h5 class="mb-1">My Team</h5>
                            <div class="text-muted small">
                                Current Level: <span class="badge-soft">Level {{ $me->level }}</span>
                            </div>
                        </div>
                        <div style="min-width:260px">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Directs: {{ $directCount ?? 0 }}/12</span>
                                <span>{{ $progress ?? 0 }}%</span>
                            </div>
                            <div class="progress mb-1"><span style="width:{{ $progress ?? 0 }}%"></span></div>
                            @if(($toNext ?? 0)>0)
                            <div class="small text-muted">Need {{ $toNext }} more directs to reach <b>Level 2</b>.</div>
                            @else
                            <div class="small text-success">Congrats! You have unlocked <b>Level 2</b>.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-dark">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Organization (You → Directs → Their Teams)</h6>
                </div>
                <div class="card-body">
                    <div class="org-toolbar">
                        <div class="d-flex gap-2">
                            <button class="btn" id="btnFit">Fit</button>
                            <button class="btn" id="btnZoomIn">Zoom In</button>
                            <button class="btn" id="btnZoomOut">Zoom Out</button>
                            <button class="btn" id="btnExpand">Expand All</button>
                            <button class="btn" id="btnCollapse">Collapse All</button>
                            <button class="btn" id="btnCenterMe">Center on Me</button>
                        </div>
                        <div class="hint">Tip: Scroll/drag to pan, wheel to zoom</div>
                    </div>
                    <div id="orgchart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========= ESM build (no globals needed) ========= --}}
<!-- ===== DYNAMIC ESM LOADER (replaces your previous script tags) ===== -->
<script type="module">
    (async () => {
        const hostSel = '#orgchart';
        const host = document.querySelector(hostSel);

        // Little status pill (optional but handy)
        const pill = document.createElement('div');
        pill.style.cssText = 'position:absolute;right:12px;top:12px;z-index:5;font:12px/1.2 system-ui;background:#0e1f2d;color:#bfe8ff;border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:6px 10px';
        pill.textContent = 'Loading…';
        host.appendChild(pill);

        // Try multiple CDNs for ESM
        const D3_CANDIDATES = [
            'https://esm.sh/d3@7', // fast ESM CDN
            'https://unpkg.com/d3@7?module' // unpkg ESM
        ];
        const ORG_CANDIDATES = [
            'https://esm.sh/d3-org-chart@3', // fast ESM CDN
            'https://unpkg.com/d3-org-chart@3.2.0?module'
        ];

        async function importFirst(urls, label) {
            let lastErr;
            for (const u of urls) {
                try {
                    return await import(u);
                } catch (e) {
                    lastErr = e;
                }
            }
            throw new Error(`Failed to load ${label}: ${lastErr?.message||'unknown'}`);
        }

        let d3mod, orgmod;
        try {
            d3mod = await importFirst(D3_CANDIDATES, 'd3');
            orgmod = await importFirst(ORG_CANDIDATES, 'd3-org-chart');
        } catch (e) {
            console.error(e);
            pill.textContent = 'CDN blocked';
            host.innerHTML = '<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">Libraries could not be loaded (network/CDN blocked).</div>';
            return;
        }

        // Normalize exports
        const d3 = d3mod.default || d3mod;
        const OrgChart = orgmod.OrgChart || orgmod.default || (orgmod.default && orgmod.default.OrgChart);
        if (!OrgChart) {
            pill.textContent = 'Export mismatch';
            host.innerHTML = '<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">OrgChart export not found in module.</div>';
            return;
        }

        // ===== Data from PHP =====
        const RAW = @json($nodes ?? []);

        // Sanitize & cast
        const data = (Array.isArray(RAW) ? RAW : []).map(n => ({
            ...n,
            id: n?.id != null ? parseInt(n.id, 10) : null,
            parentId: n?.parentId != null ? parseInt(n.parentId, 10) : null,
            name: n?.name ?? ''
        })).filter(n => n && n.id != null);

        // Helpers
        const esc = s => (s == null) ? '' : (s + '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        const initials = name => {
            const p = (name || '').trim().split(/\s+/);
            return (((p[0] || '?')[0] || '?') + (p.length > 1 ? (p[p.length - 1][0] || '') : '')).toUpperCase();
        };
        const nodeHTML = d => {
            const n = d.data || d,
                g = n.g || 'g-a',
                me = n.type === 'me';
            return `
      <div class="org-node ${me?'me':''}">
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
            pill.textContent = 'LIB:OK • NODES:0';
            host.innerHTML = '<div style="height:100%;display:grid;place-items:center;color:#9bb3c7">No team data yet</div>';
            return;
        }

        // ===== Render
        try {
            const chart = new OrgChart()
                .container(hostSel)
                .data(data)
                .nodeId(d => d.id)
                .parentNodeId(d => d.parentId)
                .nodeWidth(() => 280)
                .nodeHeight(d => (d?.data?.type === 'me' ? 110 : 92))
                .childrenMargin(() => 40)
                .compact(false)
                .nodeContent(nodeHTML);

            chart.render().fit();
            pill.textContent = `LIB:OK • NODES:${data.length}`;

            // Toolbar
            const $ = id => document.getElementById(id);
            $('#btnFit')?.addEventListener('click', () => chart.fit());
            $('#btnZoomIn')?.addEventListener('click', () => chart.zoomIn());
            $('#btnZoomOut')?.addEventListener('click', () => chart.zoomOut());
            $('#btnExpand')?.addEventListener('click', () => chart.expandAll());
            $('#btnCollapse')?.addEventListener('click', () => chart.collapseAll());
            $('#btnCenterMe')?.addEventListener('click', () => {
                const me = data.find(x => x.type === 'me') || data[0];
                if (me) chart.setCentered(me.id).fit();
            });
            window.addEventListener('resize', () => chart.fit(), {
                passive: true
            });
        } catch (err) {
            console.error('Render failed:', err);
            pill.textContent = 'DATA ERROR';
            host.innerHTML = '<div style="height:100%;display:grid;place-items:center;color:#ffb3b3">Render error (see console).</div>';
        }
    })();
</script>

@endsection