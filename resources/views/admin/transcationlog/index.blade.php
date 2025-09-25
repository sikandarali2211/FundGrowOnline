@extends('layouts.admin')

@section('content')
<style>
    .tx-wrap {
        background: radial-gradient(1200px circle at 10% 10%, rgba(253,187,45,0.05), transparent 50%),
                    radial-gradient(800px circle at 90% 20%, rgba(253,187,45,0.06), transparent 50%),
                    #0b0f14;
        min-height: 100vh;
        color: #eaeaea;
        padding: 24px 16px;
    }
    .tx-card {
        background: rgba(12, 18, 26, 0.8);
        border: 1px solid rgba(253,187,45,0.25);
        box-shadow: 0 0 24px rgba(253,187,45,0.08);
        border-radius: 16px;
        padding: 18px;
    }
    .tx-heading {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom: 12px;
    }
    .badge-gold {
        background: linear-gradient(135deg, #f6d365, #fda085);
        color: #1a1a1a;
        font-weight: 700;
        border: none;
    }
    .copy-btn {
        background: #131a23;
        color: #fdbb2d;
        border: 1px solid rgba(253,187,45,0.35);
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
    }
    .copy-btn:hover { background: #0f141b; }
    .filter-row select {
        background: #0f141b; color: #f1f1f1; border: 1px solid #253040; border-radius: 10px; padding: 8px 10px;
    }
    .table-dark-gold {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    .table-dark-gold thead th {
        color: #c9c9c9;
        font-weight: 600;
        border-bottom: 1px solid rgba(253,187,45,0.2);
        padding: 10px 12px;
    }
    .table-dark-gold tbody tr {
        background: #0f141b;
        border: 1px solid rgba(253,187,45,0.12);
    }
    .table-dark-gold tbody td {
        padding: 12px;
        vertical-align: middle;
    }
    .tx-pill {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .tx-sent   { background: rgba(255, 99, 99, .12); color: #ff8b8b; border: 1px solid rgba(255, 99, 99, .25); }
    .tx-recv   { background: rgba(100, 220, 170, .12); color: #8af5cc; border: 1px solid rgba(100, 220, 170, .25); }
    .tx-bnb    { color: #fdbb2d; }
    .tx-token  { color: #8dc6ff; }
    .tx-hash   { color: #9bc2ff; }
    .page-link { background:#0f141b; color:#f1f1f1; border:1px solid #253040; }
    .page-item.active .page-link { background:#fdbb2d; color:#121212; border-color:#fdbb2d; }
</style>

<div class="tx-wrap">
    <div class="tx-card">
        <div class="tx-heading">
            <div>
                <h2 style="margin:0; font-weight:700; color:#fdbb2d;">Admin Transactions (BSC)</h2>
                <div style="opacity:.9; font-size:14px;">Wallet: 
                    <span id="adminAddr">{{ $address }}</span>
                    <button class="copy-btn" onclick="copyAddress()">Copy</button>
                    &nbsp; | &nbsp; Balance (BNB): <span class="badge badge-gold">{{ $balanceBNB }}</span>
                </div>
            </div>
            <div>
                <a class="copy-btn" target="_blank" href="https://bscscan.com/address/{{ $address }}">Open in BscScan</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.transactionlog.index') }}" class="filter-row" style="display:flex; gap:10px; flex-wrap:wrap; margin:14px 0;">
            <input type="hidden" name="address" value="{{ $address }}">
            <div>
                <label style="font-size:12px; opacity:.8;">Type</label><br>
                <select name="type" onchange="this.form.submit()">
                    <option value="all"   {{ $type==='all'?'selected':'' }}>All</option>
                    <option value="bnb"   {{ $type==='bnb'?'selected':'' }}>BNB</option>
                    <option value="token" {{ $type==='token'?'selected':'' }}>Tokens (BEP-20)</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px; opacity:.8;">Direction</label><br>
                <select name="direction" onchange="this.form.submit()">
                    <option value="all"      {{ $direction==='all'?'selected':'' }}>All</option>
                    <option value="sent"     {{ $direction==='sent'?'selected':'' }}>Sent</option>
                    <option value="received" {{ $direction==='received'?'selected':'' }}>Received</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px; opacity:.8;">Per Page</label><br>
                <select name="perPage" onchange="this.form.submit()">
                    @foreach([10,25,50,100] as $pp)
                        <option value="{{ $pp }}" {{ $perPage==$pp?'selected':'' }}>{{ $pp }}</option>
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
                            <td><span class="tx-hash">{{ substr($tx['hash'],0,10) }}…</span></td>
                            <td>
                                @if($tx['user_id'])
                                    <span class="badge badge-primary">{{ $tx['user_id'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tx['user_name'] && $tx['user_name'] !== 'Unknown')
                                    <div>
                                        <strong>{{ $tx['user_name'] }}</strong>
                                        @if($tx['user_email'] && $tx['user_email'] !== 'N/A')
                                            <br><small class="text-muted">{{ $tx['user_email'] }}</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Unknown User</span>
                                @endif
                            </td>
                            <td>
                                @if ($tx['type']==='bnb')
                                    <span class="tx-bnb">BNB</span>
                                @else
                                    <span class="tx-token">{{ $tx['tokenSymbol'] }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($tx['direction']==='sent')
                                    <span class="tx-pill tx-sent">Sent</span>
                                @else
                                    <span class="tx-pill tx-recv">Received</span>
                                @endif
                            </td>
                            <td title="{{ $tx['from'] }}">{{ substr($tx['from'],0,8) }}…</td>
                            <td title="{{ $tx['to'] }}">{{ substr($tx['to'],0,8) }}…</td>
                            <td>
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <span style="font-weight: bold; color: #3bd17a;">{{ $tx['amount'] }}</span>
                                    <small style="opacity:.75;">{{ $tx['type']==='bnb' ? 'BNB' : $tx['tokenSymbol'] }}</small>
                                    @if($tx['direction'] === 'received')
                                        <small style="color: #28a745; font-size: 0.7rem;">Received</small>
                                    @else
                                        <small style="color: #dc3545; font-size: 0.7rem;">Sent</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $tx['status'] }}</td>
                            <td>
                                <a class="copy-btn" target="_blank" href="https://bscscan.com/tx/{{ $tx['hash'] }}">View</a>
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

        <div style="margin-top: 12px;">
            {{ $transactions->appends([
                'address'   => $address,
                'type'      => $type,
                'direction' => $direction,
                'perPage'   => $perPage,
            ])->links() }}
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
@endsection
