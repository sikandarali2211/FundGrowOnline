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
            border-radius: 20px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
        }

        /* Success Icon */
        .fa-check-circle {
            color: #3bd17a;
            text-shadow: 0 0 18px rgba(59, 209, 122, 0.8);
        }

        /* Heading */
        h2 {
            color: #3bd17a;
            font-weight: 700;
            text-shadow: 0 0 12px rgba(59, 209, 122, 0.6);
        }

        p {
            color: #cbd5e1;
            font-size: 1rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #3bd17a, #2bbd65);
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2bbd65, #249c54);
            box-shadow: 0 6px 16px rgba(59, 209, 122, 0.5);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border: 2px solid #3bd17a;
            color: #3bd17a;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: #3bd17a;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>

    <div class="main-panel" style=" background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
        <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="card p-5 text-center">
                <i class="fas fa-check-circle fa-5x mb-4"></i>
                <h2>Plan Submitted!</h2>
                <p class="mb-4">Your plan is pending admin approval. You will be notified once reviewed.</p>

                <div class="d-flex justify-content-center" style="gap:0.75rem;">
                    <a href="{{ route('user.plan-selections.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i> View My Selections
                    </a>
                    <a href="{{ route('user.plans.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Back to Plans
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
