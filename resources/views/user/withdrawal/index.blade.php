@extends('layouts.user')

@section('content')
<style>
    /* ====== dark teal + neon green theme ====== */
    :root {
        --fg-bg: #061a1f;
        --fg-surface: #0a242b;
        --fg-surface-2: #0d2b33;
        --fg-border: #0f3640;
        --fg-text: #cfe7e3;
        --fg-muted: #7aa5a0;
        --fg-accent: #22e3a0;
        --fg-accent-2: #20c9bb;
        --fg-warning: #f5c84b;
        --fg-danger: #ff6b6b;
        --fg-info: #20c9bb;
        --fg-hover: rgba(34, 227, 160, .08);
        --fg-shadow: 0 10px 30px rgba(0, 0, 0, .35)
    }

    body {
        background: radial-gradient(1200px 600px at 10% -10%, rgba(34, 227, 160, .08), transparent 60%), radial-gradient(900px 500px at 110% 10%, rgba(32, 201, 187, .10), transparent 60%), var(--fg-bg) !important;
        color: var(--fg-text)
    }

    .page-title-box {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin: 1.25rem 0 1.5rem;
        box-shadow: var(--fg-shadow)
    }

    .page-title {
        color: var(--fg-accent);
        font-weight: 800;
        letter-spacing: .4px;
        margin: 0
    }

    .card {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 16px;
        box-shadow: var(--fg-shadow);
        color: var(--fg-text)
    }

    .card-header {
        border-bottom: 1px solid var(--fg-border);
        background: transparent
    }

    .card-title {
        margin: 0;
        font-weight: 800;
        color: var(--fg-text)
    }

    .stat {
        border: 1px solid var(--fg-border);
        background: rgba(255, 255, 255, .03);
        border-radius: 14px;
        padding: 1rem;
        height: 100%
    }

    .stat h6 {
        margin: 0 0 .25rem;
        color: var(--fg-muted);
        font-weight: 700;
        letter-spacing: .3px
    }

    .stat h4 {
        margin: 0;
        font-weight: 900;
        color: var(--fg-accent)
    }

    label {
        font-weight: 700;
        color: var(--fg-text)
    }

    .form-control,
    .form-select {
        background: rgba(255, 255, 255, .04);
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
        border-radius: 10px;
        padding: .6rem .8rem
    }

    .form-control::placeholder {
        color: #89bdb6
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--fg-accent);
        box-shadow: 0 0 0 .2rem rgba(34, 227, 160, .15);
        background: rgba(255, 255, 255, .06);
        color: var(--fg-text)
    }

    .form-text {
        color: var(--fg-muted) !important
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--fg-accent-2), var(--fg-accent));
        border: 0;
        color: #05241d;
        font-weight: 800;
        border-radius: 10px;
        padding: .6rem 1rem;
        transition: transform .15s, box-shadow .15s
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(34, 227, 160, .25)
    }

    .withdrawal-item {
        border: 1px solid var(--fg-border);
        background: rgba(255, 255, 255, .03);
        border-radius: 12px
    }

    .badge {
        border-radius: 999px;
        font-weight: 800;
        padding: .45rem .7rem;
        border: 1px solid transparent
    }

    .badge-pending {
        background: rgba(245, 200, 75, .12);
        color: #f5d26b;
        border-color: rgba(245, 200, 75, .25)
    }

    .badge-approved {
        background: rgba(32, 201, 187, .12);
        color: #66e0d6;
        border-color: rgba(32, 201, 187, .25)
    }

    .badge-completed {
        background: rgba(34, 227, 160, .12);
        color: #88f0cc;
        border-color: rgba(34, 227, 160, .25)
    }

    .badge-rejected {
        background: rgba(255, 107, 107, .12);
        color: #ff9d9d;
        border-color: rgba(255, 107, 107, .25)
    }

    .text-muted {
        color: var(--fg-muted) !important
    }
</style>

<div class="main-panel">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Withdrawal</h4>
            </div>
        </div>
    </div>

    <!-- Row: Form only -->
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title">Request Withdrawal</h5>
                </div>
                <div class="card-body">
                    <!-- Balance tiles -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <div class="stat">
                                <h6><i class="fas fa-coins me-1"></i>Pool Commission</h6>
                                <h4 class="mb-0">${{ number_format($poolCommission, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat">
                                <h6><i class="fas fa-wallet me-1"></i>Balance Wallet</h6>
                                <h4 class="mb-0">${{ number_format($balanceWallet, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat">
                                <h6><i class="fas fa-layer-group me-1"></i>Total Available</h6>
                                <h4 class="mb-0">${{ number_format($totalAvailable, 2) }}</h4>
                            </div>
                        </div>
                    </div>

                    @if($totalAvailable > 0)
                    <form id="withdrawalForm" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount (USDT)</label>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    min="1" max="{{ $totalAvailable }}" step="0.01" required placeholder="Enter amount">
                                <div class="form-text" id="amountHelp">Minimum: $1.00, Maximum: ${{ number_format($totalAvailable, 2) }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="withdrawal_source" class="form-label">Withdrawal Source</label>
                                <select class="form-select" id="withdrawal_source" name="withdrawal_source" required>
                                    <option value="">Select Source</option>
                                    @if($poolCommission > 0)
                                    <option value="pool_commission">Pool Commission Only (${{ number_format($poolCommission, 2) }})</option>
                                    @endif
                                    @if($balanceWallet > 0)
                                    <option value="balance_wallet">Balance Wallet Only (${{ number_format($balanceWallet, 2) }})</option>
                                    @endif
                                    @if($poolCommission > 0 && $balanceWallet > 0)
                                    <option value="both">Both Wallets (${{ number_format($totalAvailable, 2) }})</option>
                                    @endif
                                </select>
                            </div>
                            <!-- Fee summary -->
                            <div class="col-md-6">
                                <label class="form-label">Fee (10%)</label>
                                <div id="feeAmount" class="fw-bold" style="color:#ff6b6b;">$0.00</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">You will receive</label>
                                <div id="netAmount" class="fw-bold" style="color:#3bd17a;">$0.00</div>
                            </div>
                            <input type="hidden" name="fee_amount" id="fee_amount" value="0">
                            <input type="hidden" name="net_amount" id="net_amount" value="0">
                            <div class="col-md-6">
                                <label for="wallet_type" class="form-label">Wallet Type</label>
                                <select class="form-select" id="wallet_type" name="wallet_type" required>
                                    <option value="">Select Wallet Type</option>
                                    <option value="trust">Trust Wallet</option>
                                    <option value="metamask">MetaMask</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="wallet_address" class="form-label">Wallet Address (BEP-20)</label>
                                <input type="text" class="form-control" id="wallet_address" name="wallet_address"
                                    placeholder="0x..." minlength="42" maxlength="42" required>
                                <div class="form-text">Enter your BEP-20 address (starts with 0x, 42 characters).</div>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Submit Withdrawal Request</button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-warning mb-0" style="background: rgba(245,200,75,.12); border:1px solid rgba(245,200,75,.25); color:#f5d26b; border-radius:12px;">
                        <i class="fas fa-exclamation-triangle me-2"></i> You don't have any balance to withdraw.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Withdrawals BELOW -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Withdrawals</h5>
                </div>
                <div class="card-body">
                    @if($withdrawals->count() > 0)
                    @foreach($withdrawals as $withdrawal)
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 withdrawal-item">
                        <div>
                            <h6 class="mb-1">${{ number_format($withdrawal->amount, 2) }}</h6>
                            <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y') }}</small>
                        </div>
                        <span class="badge badge-{{ $withdrawal->status_badge }}">{{ $withdrawal->status_text }}</span>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted mb-0">No withdrawal requests yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 

<script>
    (function() {
        // ✅ Inject backend numbers as real JS numbers (no extra braces)
        const poolCommission = Number(@json($poolCommission));
        const balanceWallet = Number(@json($balanceWallet));
        const totalAvailable = Number(@json($totalAvailable));

        const amountInput = document.getElementById('amount');
        const sourceSel = document.getElementById('withdrawal_source');
        const helpText = document.getElementById('amountHelp');
        const form = document.getElementById('withdrawalForm');
        const addressInput = document.getElementById('wallet_address');
        const feeAmountEl = document.getElementById('feeAmount');
        const netAmountEl = document.getElementById('netAmount');
        const feeHidden = document.getElementById('fee_amount');
        const netHidden = document.getElementById('net_amount');

        function setMaxBySource() {
            let maxAmount = totalAvailable;
            if (sourceSel) {
                switch (sourceSel.value) {
                    case 'pool_commission':
                        maxAmount = poolCommission;
                        break;
                    case 'balance_wallet':
                        maxAmount = balanceWallet;
                        break;
                    case 'both':
                        maxAmount = totalAvailable;
                        break;
                }
            }
            if (amountInput) {
                amountInput.max = maxAmount || 0;
                if (!amountInput.value) amountInput.placeholder = `Max: $${(maxAmount||0).toFixed(2)}`;
            }
            if (helpText) helpText.textContent = `Minimum: $1.00, Maximum: $${(maxAmount||0).toFixed(2)}`;
            recalcFeeNet();
        }
        sourceSel && sourceSel.addEventListener('change', setMaxBySource);
        setMaxBySource();

        function recalcFeeNet(){
            const amt = parseFloat(amountInput.value || '0');
            const fee = Math.max(0, +(amt * 0.10).toFixed(2));
            const net = Math.max(0, +(amt - fee).toFixed(2));
            if (feeAmountEl) feeAmountEl.textContent = `$${fee.toFixed(2)}`;
            if (netAmountEl) netAmountEl.textContent = `$${net.toFixed(2)}`;
            if (feeHidden) feeHidden.value = fee.toFixed(2);
            if (netHidden) netHidden.value = net.toFixed(2);
        }
        amountInput && amountInput.addEventListener('input', recalcFeeNet);
        recalcFeeNet();

        form && form.addEventListener('submit', function(e) {
            e.preventDefault();

            const amt = parseFloat(amountInput.value || '0');
            const max = parseFloat(amountInput.max || '0');
            if (isNaN(amt) || amt < 1) {
                alert('Please enter at least $1.00.');
                amountInput.focus();
                return;
            }
            if (amt > max) {
                alert(`Amount exceeds available limit. Max allowed is $${max.toFixed(2)}.`);
                amountInput.focus();
                return;
            }

            const addr = (addressInput.value || '').trim();
            if (!(addr.startsWith('0x') && addr.length === 42)) {
                alert('Please enter a valid BEP-20 wallet address (starts with 0x and 42 characters long).');
                addressInput.focus();
                return;
            }

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
            submitBtn.disabled = true;

            fetch('{{ route("user.withdrawal.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Withdrawal request submitted successfully! A 10% fee has been applied.');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unable to submit request'));
                    }
                })
                .catch(() => {
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    })();
</script>
@endsection