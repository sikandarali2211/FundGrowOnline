@extends('layouts.user')

@section('content')
    <style>
        /* Body background */
        body {
            background: linear-gradient(135deg, #0d1b2a, #1b263b, #243b55);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            color: #f1f5f9;
        }

        /* Glassy Card */
        .card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.8);
        }

        /* Success Icon */
        .card i.text-success {
            color: #38ef7d !important;
            text-shadow: 0 0 12px rgba(56, 239, 125, 0.6);
        }

        /* Headings */
        h2.text-success {
            color: #38ef7d !important;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(56, 239, 125, 0.6);
        }

        /* Paragraph */
        .card p {
            color: #cbd5e1;
            font-size: 15px;
            line-height: 1.6;
        }

        /* Alert Info */
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 15px;
            padding: 20px;
            color: #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        /* Alert Heading */
        .alert-heading {
            font-weight: 600;
            color: #60a5fa;
            margin-bottom: 10px;
        }

        /* Alert List */
        .alert ul li {
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        /* Alert List Icons */
        .alert ul li i {
            margin-right: 10px;
            font-size: 16px;
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.6);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: #60a5fa;
            border: 1px solid #60a5fa;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: rgba(59, 130, 246, 0.2);
            color: #ffffff;
            border-color: #3b82f6;
            transform: translateY(-2px);
        }
    </style>
    <div class="main-panel">
        <div class="container py-5" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle fa-5x text-success"></i>
                            </div>
                            <h2 class="text-success mb-3">Plan Selection Submitted!</h2>
                            <p class="text-muted mb-4">
                                Your plan selection has been submitted and is pending admin approval.
                                Admin will review your request and contact you if needed.
                            </p>

                            <div class="alert alert-info">
                                <h6 class="alert-heading">What happens next?</h6>
                                <ul class="list-unstyled mb-0 text-start">
                                    <li><i class="fas fa-eye text-primary me-2"></i>Admin will review your plan selection
                                    </li>
                                    <li><i class="fas fa-phone text-success me-2"></i>Admin may contact you for details</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Plan will be approved or rejected</li>
                                    <li><i class="fas fa-bell text-warning me-2"></i>You will receive notification</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="{{ route('user.plan-selections.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>View My Selections
                                </a>
                                <a href="{{ route('user.plans.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Select Another Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
