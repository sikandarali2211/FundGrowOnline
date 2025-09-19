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

                        <!-- Form -->
                        <form action="{{ route('user.plan-selections.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                            <input type="hidden" name="plan_amount" value="{{ $plan['amount'] }}">
                            <input type="hidden" name="return_percentage" value="{{ $plan['return_percentage'] }}">
                            <input type="hidden" name="duration_days" value="{{ $plan['duration_days'] }}">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-2"></i> Confirm & Submit
                                </button>
                                <a href="{{ route('payment.form', 'temp') }}?plan={{ $plan['name'] }}&amount={{ $plan['amount'] }}&return={{ $plan['return_percentage'] }}&duration={{ $plan['duration_days'] }}" class="btn btn-warning">
                                    <i class="fas fa-credit-card me-2"></i> Pay with Wallet
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection