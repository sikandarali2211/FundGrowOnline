@extends('layouts.user')

@section('content')
    @php
        $user = $user ?? auth()->user();
        $referralUrl = $referralUrl ?? url('/register') . '?ref=' . ($user->referral_code ?? '');
    @endphp

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0a1d3a, #0f3460);
        }

        .invite-wrap {
            padding-block: 24px;
        }

        /* Glassy card base */
        .glassy-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            padding: 24px;
            color: #eaeaea;
        }

        /* QR box */
        .qr-box {
            display: grid;
            place-items: center;
            min-height: 360px;
        }

        #qrCanvas canvas,
        #qrCanvas img {
            width: 300px;
            height: 300px;
        }

        .qr-link {
            font-size: .95rem;
            color: #cfcfcf;
            word-break: break-all;
        }

        /* Buttons */
        .btn-soft {
            border-radius: 10px;
            padding: 10px 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            transition: .2s ease;
        }

        .btn-soft:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .btn-primary-ghost {
            background: #00c2a8;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            transition: .2s ease;
        }

        .btn-primary-ghost:hover {
            filter: brightness(.9);
            transform: translateY(-2px);
        }

        /* Rewards */
        .rewards-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .rewards-title .ico {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, .1);
            border-radius: 10px;
            color: #fff;
        }

        .rewards-body p {
            color: #bdbdbd;
            margin-bottom: 10px;
        }

        .rewards-list {
            margin: 14px 0 18px;
            padding-left: 0;
            list-style: none;
        }

        .rewards-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
            color: #eaeaea;
        }

        .tick {
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            background: #00c2a8;
            color: #0d0d0d;
            font-weight: 900;
            border-radius: 6px;
            display: grid;
            place-items: center;
            font-size: 14px;
        }

        .note {
            color: #cfcfcf;
            font-size: .95rem;
        }

        .muted {
            color: #a8a8a8;
            font-size: .92rem;
        }

        /* Actions row */
        .actions-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Tables */
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #fff;
            font-weight: 600;
        }

        .table td,
        .table th {
            vertical-align: middle;
            color: #eaeaea;
        }

        .table thead th {
            color: #bdbdbd;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
    <div class="main-panel">
        <div class="container invite-wrap">
            <div class="row justify-content-center">
                <div class="col-12 mb-3" style="margin-top:4rem; "> 
                    <h4 class="fw-bold text-white">Invite Friends</h4>
                </div>

                <!-- LEFT: QR Card -->
                <div class="col-lg-6 mb-4">
                    <div class="glassy-card">
                        <div class="qr-box">
                            <div id="qrCanvas" aria-label="Referral QR"></div>
                        </div>

                        <div class="mt-3">
                            <div class="qr-link" id="refLinkText">{{ $referralUrl }}</div>
                        </div>

                        <div class="d-flex gap-2 mt-3 actions-row">
                            <button id="btnShare" type="button" class="btn btn-soft">
                                <i class="fa fa-share-alt me-2"></i> Share
                            </button>
                            <button id="btnCopy" type="button" class="btn btn-primary-ghost">
                                <i class="fa fa-copy me-2"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Rewards Card -->
                <div class="col-lg-6 mb-4">
                    <div class="glassy-card">
                        <div class="rewards-title">
                            <div class="ico"><i class="fa fa-gift"></i></div>
                            <span>Referral Rewards</span>
                        </div>

                        <div class="rewards-body">
                            <p>Invite your friends to join and earn instant bonuses every time they join a pool.</p>

                            <ul class="rewards-list">
                                <li>
                                    <span class="tick">✓</span>
                                    <span><strong>100% Bonus</strong> when they join Pool 1</span>
                                </li>
                                <li>
                                    <span class="tick">💰</span>
                                    <span><strong>30% Bonus</strong> from every pool they join from Pool 2 to Pool 15</span>
                                </li>
                            </ul>

                            <p class="note"><strong>It’s simple:</strong> The more they grow, the more you earn —
                                instantly!
                            </p>
                            <p class="muted"><i class="fa fa-mobile me-2"></i>Share your referral link now and start
                                earning!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BELOW: Referral Details -->
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">My Referral Details</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 220px;">Name</th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Referral Code</th>
                                            <td>
                                                <span id="refCodeText" class="me-2">{{ $user->referral_code }}</span>
                                                <button class="btn btn-sm btn-outline-light" type="button"
                                                    onclick="copyFrom('refCodeText')">
                                                    Copy
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Your Referral Link</th>
                                            <td class="text-wrap">
                                                <span id="refLinkTextTable" class="me-2 d-inline-block"
                                                    style="max-width:100%; word-break:break-all;">
                                                    {{ $referralUrl }}
                                                </span>
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    onclick="copyFrom('refLinkTextTable')">
                                                    Copy Link
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @isset($directs)
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">My Direct Referrals</h5>
                                <small class="opacity-75">Total: {{ $directs->total() }}</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Joined</th>
                                                <th>Used Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($directs as $idx => $u)
                                                <tr>
                                                    <td>{{ $directs->firstItem() + $idx }}</td>
                                                    <td>{{ $u->name }}</td>
                                                    <td>{{ $u->email }}</td>
                                                    <td>{{ $u->phone }}</td>
                                                    <td>{{ $u->created_at?->format('d M Y, h:i A') }}</td>
                                                    <td>{{ $u->referral }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">No direct referrals
                                                        yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-3">
                                    {{ $directs->links() }}
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- QR Generate ---
        (function makeQR() {
            const url = @json($referralUrl);
            const el = document.getElementById('qrCanvas');
            if (!el) return;
            new QRCode(el, {
                text: url,
                width: 300,
                height: 300,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        })();

        // --- Share / Copy buttons ---
        const linkText = @json($referralUrl);

        function showToast(msg, color = "#00c2a8") {
            if (window.Toastify) {
                Toastify({
                    text: msg,
                    duration: 2000,
                    gravity: "top",
                    position: "center",
                    close: false,
                    backgroundColor: color
                }).showToast();
            } else {
                alert(msg);
            }
        }

        document.getElementById('btnCopy')?.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(linkText);
                showToast('Referral link copied!');
            } catch (e) {
                // Fallback
                copyFallback(linkText);
            }
        });

        document.getElementById('btnShare')?.addEventListener('click', async () => {
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: 'Join me on FundGrow Online',
                        text: 'Sign up with my link:',
                        url: linkText
                    });
                    showToast('Shared!');
                } catch (e) {
                    /* user cancelled */
                }
            } else {
                // If Web Share not supported, copy instead
                try {
                    await navigator.clipboard.writeText(linkText);
                    showToast('Link copied (share not supported)!');
                } catch (e) {
                    copyFallback(linkText);
                }
            }
        });

        function copyFallback(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast('Referral link copied!');
        }

        // Reusable copy for table fields
        function copyFrom(id) {
            const txt = document.getElementById(id)?.innerText?.trim() ?? '';
            if (!txt) {
                showToast('Nothing to copy', "#f44336");
                return;
            }
            navigator.clipboard.writeText(txt)
                .then(() => showToast('Copied!'))
                .catch(() => copyFallback(txt));
        }
    </script>
@endsection
