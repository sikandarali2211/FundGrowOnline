@extends('layouts.user')
@section('content')
    <style>
        /* Background */
        body {
            background: linear-gradient(135deg, #0d1b2a, #1b263b, #2a4d69);
            font-family: 'Poppins', sans-serif;
            color: #e0e6ed;
        }

        /* Card Styling */
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

        /* Card Header */
        .card-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 18px 18px 0 0;
            padding: 1rem 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #3bd17a, #2bbd65);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2bbd65, #249c54);
            box-shadow: 0 6px 16px rgba(59, 209, 122, 0.5);
            transform: translateY(-2px);
        }

        /* Tables */
        .table {
            color: #e0e6ed;
            margin-bottom: 0;
        }

        .table thead {
            background: rgba(255, 255, 255, 0.1);
            color: #3bd17a;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table-hover tbody tr {
            transition: background 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Badges */
        .badge {
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        /* Status Badges */
        .badge.bg-info {
            background: rgba(0, 123, 255, 0.25) !important;
            color: #66b2ff !important;
        }

        .badge.bg-success {
            background: rgba(59, 209, 122, 0.25) !important;
            color: #3bd17a !important;
        }

        .badge.bg-danger {
            background: rgba(220, 53, 69, 0.25) !important;
            color: #ff6b6b !important;
        }

        .badge.bg-warning {
            background: rgba(255, 193, 7, 0.25) !important;
            color: #ffca2c !important;
        }

        /* Empty State */
        .text-center i {
            opacity: 0.5;
        }

        /* Stats Cards */
        .card.text-center .card-body {
            padding: 1.5rem;
        }

        .card.text-center h3 {
            font-weight: 700;
        }

        .card.text-center .fa-2x {
            opacity: 0.9;
        }
    </style>

    <div class="main-panel">
        <div class="container py-5" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Select Investment Plan
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.plan-selections.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="plan_name" class="form-label">Plan Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('plan_name') is-invalid @enderror"
                                            id="plan_name" name="plan_name"
                                            value="{{ old('plan_name', ucfirst(request('plan', ''))) }}" required>
                                        @error('plan_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="plan_amount" class="form-label">Investment Amount <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                class="form-control @error('plan_amount') is-invalid @enderror"
                                                id="plan_amount" name="plan_amount"
                                                value="{{ old('plan_amount', request('amount', '')) }}" step="0.01"
                                                min="0.01" required>
                                        </div>
                                        @error('plan_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="return_percentage" class="form-label">Expected Return % <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control @error('return_percentage') is-invalid @enderror"
                                                id="return_percentage" name="return_percentage"
                                                value="{{ old('return_percentage', request('plan') == 'grower' ? '0' : '360') }}"
                                                step="0.01" min="0" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('return_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="duration_days" class="form-label">Duration (Days) <span
                                                class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('duration_days') is-invalid @enderror"
                                            id="duration_days" name="duration_days" value="{{ old('duration_days', '30') }}"
                                            min="1" required>
                                        @error('duration_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Expected Return Calculation -->
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title">Expected Return Calculation</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Investment Amount:</strong><br>
                                                <span id="investment-amount">$0.00</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Return Rate:</strong><br>
                                                <span id="return-rate">0%</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Total Return:</strong><br>
                                                <span id="total-return">$0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Important Information</h6>
                                    <ul class="mb-0">
                                        <li>Your plan selection will be reviewed by admin</li>
                                        <li>You will be notified once your plan is approved</li>
                                        <li>Admin may contact you for additional details</li>
                                        <li>Plan will become active after approval</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('user.plans.index') }}" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Plans
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Plan Selection
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('plan_amount');
            const returnInput = document.getElementById('return_percentage');
            const investmentAmountSpan = document.getElementById('investment-amount');
            const returnRateSpan = document.getElementById('return-rate');
            const totalReturnSpan = document.getElementById('total-return');

            function calculateReturn() {
                const amount = parseFloat(amountInput.value) || 0;
                const returnRate = parseFloat(returnInput.value) || 0;
                const totalReturn = amount * (1 + returnRate / 100);

                investmentAmountSpan.textContent = '$' + amount.toFixed(2);
                returnRateSpan.textContent = returnRate.toFixed(2) + '%';
                totalReturnSpan.textContent = '$' + totalReturn.toFixed(2);
            }

            amountInput.addEventListener('input', calculateReturn);
            returnInput.addEventListener('input', calculateReturn);

            // Initial calculation
            calculateReturn();

            // Auto-calculate when page loads with pre-filled data
            window.addEventListener('load', function() {
                calculateReturn();
            });
        });
    </script>
@endsection
