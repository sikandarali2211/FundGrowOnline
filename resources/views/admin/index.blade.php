@extends('layouts.admin')

@section('content')
    <style>
        /* Page background */
        .content-wrapper {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            padding-top: 80px;
            /* space for fixed navbar */
            min-height: calc(100vh - 80px);
            color: #fff;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #3bd17a;
        }

        /* Card style */
        .card.card-statistics {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        /* Table Styling */
        .table-container {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            margin-bottom: 1.5rem;
        }

        /* Responsive Table */
        .modern-table {
            color: #fff;
        }

        .modern-table th,
        .modern-table td {
            vertical-align: middle;
        }

        .badge {
            border-radius: 12px;
            padding: 4px 8px;
        }

        /* Statistics Items */
        .statistics-item {
            flex: 1 1 200px;
            margin-bottom: 1rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 991px) {
            .page-header {
                padding: 1rem;
                font-size: 1rem;
            }

            .statistics-item h2 {
                font-size: 1.2rem;
            }

            .statistics-item p {
                font-size: 0.9rem;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>

    <!-- MAIN PANEL -->
    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="page-header">
                <h3 class="page-title" style="color: #3bd17a;">Dashboard</h3>
            </div>

            <!-- Statistics -->
            <div class="row grid-margin">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body d-flex flex-wrap justify-content-between">
                            <div class="statistics-item">
                                <p><i class="fa fa-user me-2"></i>  Total Users</p>
                                <h2>{{ number_format($totalUsers ?? 0) }}</h2>
                                <span class="badge badge-outline-success">
                                    +{{ number_format($newUsersToday ?? 0) }} today ·
                                    {{ number_format($newUsers7Days ?? 0) }} last 7d
                                </span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fa fa-clipboard-list me-2"></i>  Plan Selections</p>
                                <h2>{{ number_format(\App\Models\PlanSelection::count()) }}</h2>
                                <span class="badge badge-outline-warning">
                                    {{ \App\Models\PlanSelection::where('status', 'pending')->count() }} pending
                                </span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-hourglass-half me-2"></i>  Avg Time</p>
                                <h2>123.50</h2>
                                <span class="badge badge-outline-danger">30% decrease</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-cloud-download-alt me-2"></i>  Downloads</p>
                                <h2>3500</h2>
                                <span class="badge badge-outline-success">12% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-check-circle me-2"></i>  Update</p>
                                <h2>7500</h2>
                                <span class="badge badge-outline-success">57% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-chart-line me-2"></i>  Sales</p>
                                <h2>9000</h2>
                                <span class="badge badge-outline-success">10% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-circle-notch me-2"></i>  Pending</p>
                                <h2>7500</h2>
                                <span class="badge badge-outline-danger">16% decrease</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <h4 class="card-title text-white"><i class="fa fa-user me-2"></i>  Total Users</h4>
                            <canvas id="orders-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <h4 class="card-title text-white"><i class="fas fa-chart-line me-2"></i>  Sales</h4>
                            <h2 class="mb-4">56000</h2>
                            <canvas id="sales-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Plan Selections -->
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="table-container">
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <h4 class="table-title"> <i class="fas fa-clipboard-list me-2"></i>  Recent Plan Selections</h4>
                            <a href="{{ route('admin.plan-selections.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table modern-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $recentSelections = \App\Models\PlanSelection::with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    @forelse($recentSelections as $selection)
                                        <tr>
                                            <td><strong>#{{ $selection->id }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $selection->user->name ?? 'N/A' }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $selection->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong>{{ $selection->plan_name }}</strong></td>
                                            <td><strong>${{ number_format($selection->plan_amount, 2) }}</strong></td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $selection->status_badge }}">{{ $selection->status_text }}</span>
                                            </td>
                                            <td>{{ $selection->created_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <a href="{{ route('admin.plan-selections.show', $selection->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i>
                                                    View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-clipboard-list fa-3x mb-3"></i><br>
                                                No plan selections found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
