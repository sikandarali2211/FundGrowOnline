@extends('layouts.user')

@section('content')

    <style>
        :root {
            --primary-green: #22c55e;
            --secondary-green: #16a34a;
            --accent-blue: #3b82f6;
            --dark-blue: #0f172a;
            --darker-blue: #0a1120;
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.15);
            --white: #ffffff;
        }

        body {
            background: linear-gradient(135deg, var(--darker-blue), var(--dark-blue));
            color: var(--white);
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.7), 0 0 25px rgba(59, 130, 246, 0.4);
            border-color: var(--accent-blue);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            color: var(--white);
        }

        .card-header h4 {
            font-weight: 700;
            color: var(--white);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), var(--primary-green));
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        /* Table */
        .table {
            color: var(--white);
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .table thead th {
            background: rgba(59, 130, 246, 0.15);
            border: none;
            color: var(--accent-blue);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            padding: 1rem;
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: translateY(-3px);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Statistics Cards */
        .card-body i {
            text-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        .card-title {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .card h3 {
            font-weight: 800;
            font-size: 1.6rem;
        }
    </style>
    <div class="main-panel">
        <div class="container py-5" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>My Plan Selections
                            </h4>
                            <a href="{{ route('user.plans.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Select New Plan
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($selections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Plan Name</th>
                                                <th>Amount</th>
                                                <th>Return %</th>
                                                <th>Duration</th>
                                                <th>Expected Return</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selections as $selection)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $selection->plan_name }}</strong>
                                                    </td>
                                                    <td>
                                                        <strong>${{ number_format($selection->plan_amount, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $selection->return_percentage }}%</span>
                                                    </td>
                                                    <td>
                                                        {{ $selection->duration_days }} days
                                                    </td>
                                                    <td>
                                                        <strong
                                                            class="text-success">${{ number_format($selection->expected_return, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $selection->status_badge }}">
                                                            {{ $selection->status_text }}
                                                        </span>
                                                        @if ($selection->processed_at)
                                                            <br><small class="text-muted">
                                                                {{ $selection->processed_at->format('M d, Y') }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $selection->created_at->format('M d, Y') }}
                                                        <br><small
                                                            class="text-muted">{{ $selection->created_at->format('g:i A') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center">
                                    {{ $selections->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Plan Selections Found</h5>
                                    <p class="text-muted">You haven't selected any plans yet.</p>
                                    <a href="{{ route('user.plans.index') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Select Your First Plan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">Pending</h5>
                            <h3 class="text-warning">{{ $selections->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Approved</h5>
                            <h3 class="text-success">{{ $selections->where('status', 'approved')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">Rejected</h5>
                            <h3 class="text-danger">{{ $selections->where('status', 'rejected')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Total Amount</h5>
                            <h3 class="text-primary">${{ number_format($selections->sum('plan_amount'), 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
