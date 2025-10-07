@extends('layouts.admin')

@section('content')
    <style>
        /* Background wrapper */
        .tx-wrap {
            background: linear-gradient(145deg, #072d42, #0b0f14);
            min-height: 100vh;
            color: #eaeaea;
            padding: 24px 16px;
        }

        /* Card */
        .tx-card {
            background: rgba(12, 18, 26, 0.9);
            border: 1px solid rgba(59, 209, 122, 0.3);
            box-shadow: 0 0 25px rgba(59, 209, 122, 0.15);
            border-radius: 18px;
            padding: 18px;
            transition: all .3s ease;
        }

        .tx-card:hover {
            box-shadow: 0 0 30px rgba(59, 209, 122, 0.25);
            border-color: rgba(59, 209, 122, 0.5);
        }

        /* Heading */
        .tx-heading h2 {
            margin: 0;
            font-weight: 700;
            color: #3bd17a;
        }

        .badge-gold {
            background: linear-gradient(135deg, #3bd17a, #3bd17a);
            color: #121212;
            font-weight: 700;
            border-radius: 12px;
            padding: 4px 10px;
        }

        /* Pagination Styling */
        .pagination-container {
            background: rgba(12, 18, 26, 0.9);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .pagination {
            gap: 8px;
            margin: 0;
        }

        .pagination .page-link {
            background: rgba(7, 45, 66, 0.6);
            border: 1px solid rgba(59, 209, 122, 0.2);
            color: #3bd17a;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .pagination .page-link:hover {
            background: rgba(59, 209, 122, 0.2);
            border-color: #3bd17a;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #3bd17a, #28a745);
            border-color: #3bd17a;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 209, 122, 0.4);
        }

        .pagination .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.02);
            color: #666;
            border-color: rgba(255, 255, 255, 0.1);
            cursor: not-allowed;
        }

        .pagination-info {
            color: #aaa;
            font-size: 0.9rem;
        }

        .pagination-links {
            display: flex;
            align-items: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pagination-container .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .pagination-info {
                text-align: center;
            }

            .pagination .page-link {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
        }

        /* Buttons */
        .copy-btn {
            background: #072d42;
            color: #3bd17a;
            border: 1px solid rgba(59, 209, 122, 0.4);
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all .3s ease;
        }

        .copy-btn:hover {
            background: #0f141b;
            color: #fdbb2d;
            border-color: #fdbb2d;
        }

        /* Filter dropdowns */
        .filter-row select {
            background: #0f141b;
            color: #eaeaea;
            border: 1px solid #3bd17a;
            border-radius: 10px;
            padding: 8px 10px;
        }

        .filter-row select:hover {
            border-color: #fdbb2d;
        }

        /* Table */
        .table-dark-gold {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-dark-gold thead th {
            color: #3bd17a;
            font-weight: 600;
            border-bottom: 1px solid rgba(59, 209, 122, 0.3);
            padding: 10px 12px;
            font-size: 14px;
        }

        .table-dark-gold tbody tr {
            background: #0f141b;
            border: 1px solid rgba(59, 209, 122, 0.15);
            transition: all .25s ease;
        }

        .table-dark-gold tbody tr:hover {
            background: #131a23;
            border-color: #3bd17a;
            box-shadow: 0 0 12px rgba(59, 209, 122, 0.25);
        }

        .table-dark-gold tbody td {
            padding: 12px;
            vertical-align: middle;
            font-size: 13px;
        }

        /* Pills */
        .tx-pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .tx-sent {
            background: rgba(255, 99, 99, .12);
            color: #ff8b8b;
            border: 1px solid rgba(255, 99, 99, .25);
        }

        .tx-recv {
            background: rgba(59, 209, 122, .12);
            color: #3bd17a;
            border: 1px solid rgba(59, 209, 122, .25);
        }

        .tx-bnb {
            color: #fdbb2d;
            font-weight: 600;
        }

        .tx-token {
            color: #8dc6ff;
            font-weight: 600;
        }

        .tx-hash {
            color: #9bc2ff;
        }

        /* Pagination */
        .page-link {
            background: #0f141b;
            color: #eaeaea;
            border: 1px solid #3bd17a;
            border-radius: 8px;
        }

        .page-link:hover {
            background: #072d42;
            color: #fdbb2d;
        }

        .page-item.active .page-link {
            background: #3bd17a;
            color: #121212;
            border-color: #3bd17a;
        }


        .tx-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .wallet-info {
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .wallet-line {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .wallet-label {
            color: #8dc6ff;
            font-weight: 600;
        }

        .wallet-addr {
            max-width: 160px;
            /* prevent overflow on mobile */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: middle;
        }

        .bscscan-btn {
            margin-top: 10px;
        }

        @media(max-width: 576px) {
            .wallet-addr {
                max-width: 120px;
                font-size: 13px;
            }

            .wallet-info {
                font-size: 13px;
            }
        }

        .copy-btn.btn-xs {
            background: transparent;
            border: none;
            color: #3bd17a;
            cursor: pointer;
            font-size: 12px;
            margin-left: 4px;
            transition: color .2s;
        }

        .copy-btn.btn-xs:hover {
            color: #fdbb2d;
        }
    </style>
    <div class="main-panel" style="margin-top:5rem;">
        <div class="tx-wrap">
            <div class="tx-card">
                <div class="tx-heading">
                    <div style="flex:1;">
                        <h2 style="margin:0; font-weight:700; color:#3bd17a;">Admin Transactions (BSC)</h2>

                        <div class="wallet-info">
                            <div class="wallet-line">
                                <span class="wallet-label">Wallet:</span>
                                <span id="adminAddr" class="wallet-addr">{{ $address }}</span>
                                <button class="copy-btn" onclick="copyAddress()">Copy</button>
                            </div>

                            <div class="wallet-line">
                                <span class="wallet-label">Balance (BNB):</span>
                                <span class="badge badge-gold">{{ $balanceBNB }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bscscan-btn">
                        <a class="copy-btn" target="_blank" href="https://bscscan.com/address/{{ $address }}">Open in
                            BscScan</a>
                    </div>
                </div>


                <form method="GET" action="{{ route('admin.transactionlog.index') }}" class="filter-row"
                    style="display:flex; gap:10px; flex-wrap:wrap; margin:14px 0;">
                    <input type="hidden" name="address" value="{{ $address }}">
                    <div>
                        <label style="font-size:12px; opacity:.8;">Type</label><br>
                        <select name="type" onchange="this.form.submit()">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                            <option value="bnb" {{ $type === 'bnb' ? 'selected' : '' }}>BNB</option>
                            <option value="token" {{ $type === 'token' ? 'selected' : '' }}>Tokens (BEP-20)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; opacity:.8;">Direction</label><br>
                        <select name="direction" onchange="this.form.submit()">
                            <option value="all" {{ $direction === 'all' ? 'selected' : '' }}>All</option>
                            <option value="sent" {{ $direction === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="received" {{ $direction === 'received' ? 'selected' : '' }}>Received</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px; opacity:.8;">Per Page</label><br>
                        <select name="perPage" onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>
                                    {{ $pp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table-dark-gold">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Tx Hash</th>
                                <th>User ID</th>
                                <th>User Name</th>
                                <th>Type</th>
                                <th>Direction</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>BscScan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td>{{ $tx['date'] }}</td>

                                    {{-- Tx Hash --}}
                                    <td>
                                        <span class="tx-hash">{{ substr($tx['hash'], 0, 10) }}…</span>
                                        <button class="copy-btn btn-xs"
                                            onclick="copyText('{{ $tx['hash'] }}')">📋</button>
                                    </td>

                                    {{-- User ID --}}
                                    <td>
                                        @if ($tx['user_id'])
                                            <span class="badge badge-primary">{{ $tx['user_id'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- User Name --}}
                                    <td>
                                        @if ($tx['user_name'] && $tx['user_name'] !== 'Unknown')
                                            <div>
                                                <strong>{{ $tx['user_name'] }}</strong>
                                                @if ($tx['user_email'] && $tx['user_email'] !== 'N/A')
                                                    <br><small class="text-muted">{{ $tx['user_email'] }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </td>

                                    {{-- Type --}}
                                    <td>
                                        @if ($tx['type'] === 'bnb')
                                            <span class="tx-bnb">BNB</span>
                                        @else
                                            <span class="tx-token">{{ $tx['tokenSymbol'] }}</span>
                                        @endif
                                    </td>

                                    {{-- Direction --}}
                                    <td>
                                        @if ($tx['direction'] === 'sent')
                                            <span class="tx-pill tx-sent">Sent</span>
                                        @else
                                            <span class="tx-pill tx-recv">Received</span>
                                        @endif
                                    </td>

                                    {{-- From --}}
                                    <td title="{{ $tx['from'] }}">
                                        {{ substr($tx['from'], 0, 8) }}…
                                        <button class="copy-btn btn-xs"
                                            onclick="copyText('{{ $tx['from'] }}')">📋</button>
                                    </td>

                                    {{-- To --}}
                                    <td title="{{ $tx['to'] }}">
                                        {{ substr($tx['to'], 0, 8) }}…
                                        <button class="copy-btn btn-xs"
                                            onclick="copyText('{{ $tx['to'] }}')">📋</button>
                                    </td>

                                    {{-- Amount --}}
                                    <td>
                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                            <span style="font-weight: bold; color: #3bd17a;">{{ $tx['amount'] }}</span>
                                            <small style="opacity:.75;">
                                                {{ $tx['type'] === 'bnb' ? 'BNB' : $tx['tokenSymbol'] }}
                                            </small>
                                            @if ($tx['direction'] === 'received')
                                                <small style="color: #28a745; font-size: 0.7rem;">Received</small>
                                            @else
                                                <small style="color: #dc3545; font-size: 0.7rem;">Sent</small>
                                            @endif
                                        </div>
                                    </td>

                                    <td>{{ $tx['status'] }}</td>

                                    {{-- BscScan --}}
                                    <td>
                                        <a class="copy-btn" target="_blank"
                                            href="https://bscscan.com/tx/{{ $tx['hash'] }}">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align:center; padding:28px;">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="pagination-container mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            <small class="text-muted">
                                Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} 
                                of {{ $transactions->total() }} transactions
                            </small>
                        </div>
                        <div class="pagination-links">
                            {{ $transactions->appends([
                                'address' => $address,
                                'type' => $type,
                                'direction' => $direction,
                                'perPage' => $perPage,
                            ])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function copyAddress() {
            const el = document.createElement('textarea');
            el.value = document.getElementById('adminAddr').innerText.trim();
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Address copied!');
        }
    </script>
    <script>
        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Copied: " + text);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
<!-- 🔄 Auto Reload Script -->
<script>
    setInterval(function () {
        location.reload();
    }, 10000); // 10 sec
</script>
@endsection
