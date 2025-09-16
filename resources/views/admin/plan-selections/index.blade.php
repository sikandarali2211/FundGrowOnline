@extends('layouts.admin')
@section('content')

    <style>
        body {
            background: linear-gradient(145deg, #072d42, #22384e);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        /* Card Wrapper */
        .card {
            background: rgba(25, 40, 60, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
        }

        .card-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-bottom: none;
            border-radius: 18px 18px 0 0 !important;
            padding: 1.2rem 1.5rem;
            font-weight: 700;
            font-size: 1.2rem;
            color: #fff;
        }

        /* Statistics Small Cards */
        .card.bg-warning,
        .card.bg-success,
        .card.bg-danger,
        .card.bg-primary {
            border-radius: 15px;
            border: none;
            color: #fff;
            transition: all 0.3s ease;
        }

        .card.bg-warning:hover,
        .card.bg-success:hover,
        .card.bg-danger:hover,
        .card.bg-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        }

        /* Debug Box */
        .alert-info {
            background: rgba(74, 144, 226, 0.1);
            color: #fff;
            border: 1px solid rgba(74, 144, 226, 0.3);
            border-radius: 12px;
        }

        /* Table */
        .table {
            color: #ddd;
        }

        .table thead th {
            background: rgba(255, 255, 255, 0.08);
            color: #f1f1f1;
            border: none;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .table tbody td {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Status Badges */
        .badge.bg-warning {
            background: #ffc10733;
            color: #ffffffff;
        }

        .badge.bg-success {
            background: #28a74533;
            color: #28a745;
        }

        .badge.bg-danger {
            background: #dc354533;
            color: #dc3545;
        }

        .badge.bg-info {
            background: #4a90e233;
            color: #ffffffff;
        }

        /* Buttons */
        .btn-outline-primary,
        .btn-outline-success,
        .btn-outline-danger {
            border-width: 2px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: #4a90e2;
            color: #fff;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }

        .btn-outline-success:hover {
            background: #28a745;
            color: #fff;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: #fff;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        /* Modals */
        .modal-content {
            background: rgba(25, 40, 60, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            color: #fff;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-close {
            filter: invert(1);
        }
    </style>

    <div class="main-panel">
        <div class="content-wrapper" style=" margin-top:4rem; background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>  Plan Selections Management
                            </h4>
                            <div class="btn-group">
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All
                                            Selections</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Approved</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body" style=" background: linear-gradient(145deg, #072d42, #22384e);">
                            <!-- Statistics -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                            <h5>Pending</h5>
                                            <h3>{{ $selections->where('status', 'pending')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <h5>Approved</h5>
                                            <h3>{{ $selections->where('status', 'approved')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-times-circle fa-2x mb-2"></i>
                                            <h5>Rejected</h5>
                                            <h3>{{ $selections->where('status', 'rejected')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                            <h5>Total Amount</h5>
                                            <h3>${{ number_format($selections->sum('plan_amount'), 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Debug Information -->
                            <div class="alert alert-info mb-3">
                                <h6>Debug Information:</h6>
                                <p><strong>Total Selections:</strong> {{ $selections->total() }}</p>
                                <p><strong>Current Page:</strong> {{ $selections->currentPage() }}</p>
                                <p><strong>Per Page:</strong> {{ $selections->perPage() }}</p>
                                <p><strong>Database Count:</strong> {{ \App\Models\PlanSelection::count() }}</p>
                            </div>

                            @if ($selections->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Plan Name</th>
                                                <th>Amount</th>
                                                <th>Return %</th>
                                                <th>Duration</th>
                                                <th>Expected Return</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selections as $selection)
                                                <tr>
                                                    <td>
                                                        <strong>#{{ $selection->id }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                                            <div>
                                                                <strong>{{ $selection->user->name }}</strong>
                                                                <br><small
                                                                    class="text-muted">{{ $selection->user->email }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
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
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.plan-selections.show', $selection->id) }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if ($selection->status === 'pending')
                                                                <button type="button"
                                                                    class="btn btn-outline-success btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approveModal{{ $selection->id }}">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#rejectModal{{ $selection->id }}">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
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
                                    <p class="text-muted">No plan selections match your current filter criteria.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Plan Selection Modals -->
    @foreach ($selections->where('status', 'pending') as $selection)
        <div class="modal fade" id="approveModal{{ $selection->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Plan Selection #{{ $selection->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.plan-selections.update-status', $selection->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <div class="modal-body">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Are you sure you want to approve this plan selection?
                            </div>
                            <div class="mb-3">
                                <label for="admin_notes{{ $selection->id }}" class="form-label">Admin Notes
                                    (Optional)
                                </label>
                                <textarea class="form-control" id="admin_notes{{ $selection->id }}" name="admin_notes" rows="3"
                                    placeholder="Add any notes about this plan approval..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Approve Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rejectModal{{ $selection->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Plan Selection #{{ $selection->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.plan-selections.update-status', $selection->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Are you sure you want to reject this plan selection?
                            </div>
                            <div class="mb-3">
                                <label for="reject_notes{{ $selection->id }}" class="form-label">Rejection Reason <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="reject_notes{{ $selection->id }}" name="admin_notes" rows="3"
                                    placeholder="Please provide a reason for rejecting this plan selection..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i>Reject Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
