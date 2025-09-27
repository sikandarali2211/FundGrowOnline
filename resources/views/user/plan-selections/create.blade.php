@extends('layouts.user')
@section('content')
<style>
    body {
        background: linear-gradient(135deg, #0d1b2a, #1b263b, #2a4d69);
        font-family: 'Poppins', sans-serif;
        color: #e0e6ed;
    }

    /* Card */
    .card {
        background: rgba(255, 255, 255, 0.08);
        border: none;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(12px);
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
    }

    /* Header */
    .card-header {
        background: linear-gradient(135deg, #3bd17a, #2bbd65);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 18px 18px 0 0;
        padding: 1rem 1.5rem;
        text-align: center;
    }

    /* Plan Info Box */
    .plan-info {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .plan-info h5 {
        color: #3bd17a;
        font-weight: 600;
    }

    /* Confirm Button */
    .btn-success {
        background: linear-gradient(135deg, #3bd17a, #2bbd65);
        border: none;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #2bbd65, #249c54);
        box-shadow: 0 6px 16px rgba(59, 209, 122, 0.5);
        transform: translateY(-2px);
    }

    /* Expected Return Card */
    .expected-return {
        background: rgba(59, 209, 122, 0.1);
        border: 1px solid rgba(59, 209, 122, 0.3);
        border-radius: 15px;
        padding: 1.2rem;
        margin-bottom: 1.5rem;
    }

    .expected-return h6 {
        color: #3bd17a;
        font-weight: 600;
    }
</style>

<div class="main-panel" style="margin-top:4rem;  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-check-circle me-2"></i> Confirm Your Investment Plan
                    </div>
                    <div class="card-body">

                        <!-- Plan Info -->
                        <div class="plan-info text-center">
                            <h5>{{ ucfirst($plan['name']) }} Plan</h5>
                            <p><strong>Amount:</strong> ${{ number_format($plan['amount']) }}</p>
                            <p><strong>Return %:</strong> {{ $plan['return_percentage'] }}%</p>
                            <p><strong>Duration:</strong> {{ $plan['duration_days'] }} days</p>
                        </div>

                        <!-- Expected Return -->
                        <div class="expected-return">
                            <h6>Expected Return Calculation</h6>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <strong>Investment:</strong><br>
                                    <span id="investment-amount">${{ number_format($plan['amount'], 2) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Rate:</strong><br>
                                    <span id="return-rate">{{ $plan['return_percentage'] }}%</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Return:</strong><br>
                                    <span id="total-return">
                                        ${{ number_format($plan['amount'] * (1 + $plan['return_percentage'] / 100), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pool Wallet Balance Display -->
                        <div class="alert alert-info" style="background: rgba(59, 209, 122, 0.1); border: 1px solid #3bd17a; color: #3bd17a;">
                            <i class="fas fa-wallet me-2"></i>
                            <strong>Pool Wallet Balance:</strong> ${{ number_format(auth()->user()->pool_wallet_amount ?? 0, 2) }}
                        </div>

                        <!-- Payment Options -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid #3bd17a;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-success">Pool Wallet Payment</h6>
                                        <p class="card-text small">Use your pool wallet balance to purchase this plan instantly</p>
                                        <button type="button" class="btn btn-success btn-sm" onclick="buyWithPoolWallet()" 
                                                {{ (auth()->user()->pool_wallet_amount ?? 0) < $plan['amount'] ? 'disabled' : '' }}>
                                            <i class="fas fa-credit-card me-2"></i> Buy with Pool Wallet
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid #ffc107;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-warning">Traditional Payment</h6>
                                        <p class="card-text small">Submit plan for admin approval and external payment</p>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="submitForApproval()">
                                            <i class="fas fa-paper-plane me-2"></i> Submit for Approval
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Forms -->
                        <form id="poolWalletForm" action="{{ route('user.plan-selections.buy-with-pool') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                            <input type="hidden" name="plan_amount" value="{{ $plan['amount'] }}">
                            <input type="hidden" name="return_percentage" value="{{ $plan['return_percentage'] }}">
                            <input type="hidden" name="duration_days" value="{{ $plan['duration_days'] }}">
                        </form>

                        <form id="approvalForm" action="{{ route('user.plan-selections.store') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                            <input type="hidden" name="plan_amount" value="{{ $plan['amount'] }}">
                            <input type="hidden" name="return_percentage" value="{{ $plan['return_percentage'] }}">
                            <input type="hidden" name="duration_days" value="{{ $plan['duration_days'] }}">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function buyWithPoolWallet() {
    const planAmount = {{ $plan['amount'] }};
    const poolBalance = {{ auth()->user()->pool_wallet_amount ?? 0 }};
    
    if (poolBalance < planAmount) {
        alert('Insufficient pool wallet balance. Available: $' + poolBalance.toFixed(2));
        return;
    }
    
    if (confirm('Are you sure you want to purchase this plan using your pool wallet? This action cannot be undone.')) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        // Submit the form
        fetch('{{ route("user.plan-selections.buy-with-pool") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                plan_name: '{{ $plan["name"] }}',
                plan_amount: planAmount,
                return_percentage: {{ $plan['return_percentage'] }},
                duration_days: {{ $plan['duration_days'] }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Plan purchased successfully! Your investment is now active.');
                window.location.href = '{{ route("user.plan-selections.success") }}';
            } else {
                alert('Error: ' + data.message);
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
}

function submitForApproval() {
    if (confirm('Submit this plan for admin approval? You will need to make an external payment.')) {
        document.getElementById('approvalForm').submit();
    }
}
</script>
@endsection