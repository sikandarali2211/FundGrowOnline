@extends('layouts.admin')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #041a2f, #072d42 60%);
        color: #e0e0e0;
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent !important;
        margin-top: 4rem;
        padding: 2rem;
    }

    /* Modern Card Design */
    .modern-card {
        background: rgba(7, 45, 66, 0.6);
        border: 1px solid rgba(59, 209, 122, 0.2);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .card-header-modern {
        background: rgba(59, 209, 122, 0.1);
        border-bottom: 1px solid rgba(59, 209, 122, 0.2);
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-modern h4 {
        color: #3bd17a;
        font-weight: 700;
        font-size: 24px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: rgba(7, 45, 66, 0.6);
        border: 1px solid rgba(59, 209, 122, 0.2);
        border-radius: 16px;
        padding: 24px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 209, 122, 0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: rgba(59, 209, 122, 0.4);
        box-shadow: 0 8px 32px rgba(59, 209, 122, 0.2);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.warning {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        box-shadow: 0 4px 20px rgba(255, 193, 7, 0.3);
    }

    .stat-icon.success {
        background: linear-gradient(135deg, #28a745, #20c997);
        box-shadow: 0 4px 20px rgba(40, 167, 69, 0.3);
    }

    .stat-icon.danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        box-shadow: 0 4px 20px rgba(220, 53, 69, 0.3);
    }

    .stat-icon.primary {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        box-shadow: 0 4px 20px rgba(74, 144, 226, 0.3);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        color: #a5f2d5;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
    }

    .stat-value {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
    }

    /* Debug Box */
    .debug-box {
        background: rgba(74, 144, 226, 0.1);
        border: 1px solid rgba(74, 144, 226, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .debug-box h6 {
        color: #4a90e2;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .debug-box p {
        color: #a5f2d5;
        margin-bottom: 8px;
        font-size: 14px;
    }

    /* Modern Table */
    .table-container {
        background: rgba(59, 209, 122, 0.05);
        border: 1px solid rgba(59, 209, 122, 0.2);
        border-radius: 14px;
        overflow: hidden;
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .table-modern thead tr {
        background: rgba(59, 209, 122, 0.1);
    }

    .table-modern th {
        padding: 16px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #3bd17a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(59, 209, 122, 0.2);
    }

    .table-modern td {
        padding: 20px;
        border-bottom: 1px solid rgba(59, 209, 122, 0.1);
        color: #e0e0e0;
    }

    .table-modern tbody tr {
        transition: background 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background: rgba(59, 209, 122, 0.05);
    }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3bd17a, #28a745);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .user-info strong {
        color: #fff;
        display: block;
        font-weight: 600;
    }

    .user-info small {
        color: #a5f2d5;
        font-size: 12px;
    }

    /* Badges */
    .badge-modern {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .badge-warning {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .badge-success {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .badge-danger {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .badge-info {
        background: rgba(23, 162, 184, 0.2);
        color: #17a2b8;
        border: 1px solid rgba(23, 162, 184, 0.3);
    }

    /* Action Buttons */
    .btn-group-modern {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-action-view {
        background: rgba(74, 144, 226, 0.2);
        color: #4a90e2;
        border: 1px solid rgba(74, 144, 226, 0.3);
    }

    .btn-action-view:hover {
        background: rgba(74, 144, 226, 0.3);
        transform: scale(1.05);
    }

    .btn-action-approve {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .btn-action-approve:hover {
        background: rgba(40, 167, 69, 0.3);
        transform: scale(1.05);
    }

    .btn-action-reject {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .btn-action-reject:hover {
        background: rgba(220, 53, 69, 0.3);
        transform: scale(1.05);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state i {
        font-size: 64px;
        color: rgba(59, 209, 122, 0.3);
        margin-bottom: 24px;
    }

    .empty-state h5 {
        color: #3bd17a;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .empty-state p {
        color: #a5f2d5;
        font-size: 14px;
    }

    /* Modal Styling */
    .modal-content {
        background: rgba(7, 45, 66, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(59, 209, 122, 0.2);
        border-radius: 16px;
        color: #fff;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        background: rgba(59, 209, 122, 0.1);
        border-bottom: 1px solid rgba(59, 209, 122, 0.2);
        padding: 20px 24px;
    }

    .modal-title {
        color: #3bd17a;
        font-weight: 600;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        border-top: 1px solid rgba(59, 209, 122, 0.2);
        padding: 20px 24px;
    }

    .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.7;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(59, 209, 122, 0.2);
        border-radius: 8px;
        color: #fff;
        padding: 12px 16px;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: #3bd17a;
        box-shadow: 0 0 0 3px rgba(59, 209, 122, 0.1);
        color: #fff;
    }

    .form-label {
        color: #3bd17a;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .alert {
        border-radius: 12px;
        padding: 16px;
        border: none;
        margin-bottom: 20px;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    /* Modal Buttons */
    .btn-secondary {
        background: rgba(108, 117, 125, 0.3);
        border: 1px solid rgba(108, 117, 125, 0.4);
        color: #fff;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: rgba(108, 117, 125, 0.4);
        transform: translateY(-2px);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: #fff;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(40, 167, 69, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        color: #fff;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(220, 53, 69, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(220, 53, 69, 0.4);
    }

    /* Pagination */
    .pagination {
        gap: 8px;
    }

    .pagination .page-link {
        background: rgba(7, 45, 66, 0.6);
        border: 1px solid rgba(59, 209, 122, 0.2);
        color: #3bd17a;
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: rgba(59, 209, 122, 0.2);
        border-color: #3bd17a;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #3bd17a, #28a745);
        border-color: #3bd17a;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 1rem;
        }

        .card-header-modern {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
        }

        .table-modern {
            min-width: 800px;
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h4>
                            <i class="fas fa-chart-line"></i>
                            Plan Selections Management
                        </h4>
                    </div>
                    <div class="card-body" style="padding: 32px;">
                        <!-- Statistics Cards -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-icon warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-label">Pending</span>
                                        <div class="stat-value">{{ $selections->where('status', 'pending')->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-icon success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-label">Approved</span>
                                        <div class="stat-value">{{ $selections->where('status', 'approved')->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-icon danger">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-label">Rejected</span>
                                        <div class="stat-value">{{ $selections->where('status', 'rejected')->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-icon primary">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-label">Total Amount</span>
                                        <div class="stat-value">${{ number_format($selections->sum('plan_amount'), 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Debug Information -->
                        <div class="debug-box">
                            <h6><i class="fas fa-info-circle me-2"></i>Debug Information</h6>
                            <p><strong>Total Selections:</strong> {{ $selections->total() }}</p>
                            <p><strong>Current Page:</strong> {{ $selections->currentPage() }}</p>
                            <p><strong>Per Page:</strong> {{ $selections->perPage() }}</p>
                            <p style="margin-bottom: 0;"><strong>Database Count:</strong> {{ \App\Models\PlanSelection::count() }}</p>
                        </div>

                        @if ($selections->count() > 0)
                            <div class="table-container">
                                <table class="table-modern">
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
                                                <td><strong style="color: #3bd17a;">#{{ $selection->id }}</strong></td>
                                                <td>
                                                    <div class="user-cell">
                                                        <div class="user-avatar">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div class="user-info">
                                                            <strong>{{ $selection->user->name }}</strong>
                                                            <small>{{ $selection->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><strong>{{ $selection->plan_name }}</strong></td>
                                                <td><strong style="color: #3bd17a;">${{ number_format($selection->plan_amount, 2) }}</strong></td>
                                                <td><span class="badge-modern badge-info">{{ $selection->return_percentage }}%</span></td>
                                                <td>{{ $selection->duration_days }} days</td>
                                                <td><strong style="color: #28a745;">${{ number_format($selection->expected_return, 2) }}</strong></td>
                                                <td>
                                                    <span class="badge-modern badge-{{ $selection->status_badge }}">
                                                        {{ $selection->status_text }}
                                                    </span>
                                                    @if ($selection->processed_at)
                                                        <br><small style="color: #a5f2d5; font-size: 11px;">
                                                            {{ $selection->processed_at instanceof \Carbon\Carbon ? $selection->processed_at->format('M d, Y') : \Carbon\Carbon::parse($selection->processed_at)->format('M d, Y') }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ $selection->created_at->format('M d, Y') }}</div>
                                                    <small style="color: #a5f2d5; font-size: 12px;">{{ $selection->created_at->format('g:i A') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group-modern">
                                                        <a href="{{ route('admin.plan-selections.show', $selection->id) }}" class="btn-action btn-action-view">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if ($selection->status === 'pending')
                                                            <button type="button" class="btn-action btn-action-approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $selection->id }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn-action btn-action-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $selection->id }}">
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
                            <!-- <div class="d-flex justify-content-center mt-4">
                                {{ $selections->links() }}
                            </div> -->
                        @else
                            <div class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <h5>No Plan Selections Found</h5>
                                <p>No plan selections match your current filter criteria.</p>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        Approve Plan Selection #{{ $selection->id }}
                    </h5>
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
                            <label for="admin_notes{{ $selection->id }}" class="form-label">
                                Admin Notes (Optional)
                            </label>
                            <textarea class="form-control" id="admin_notes{{ $selection->id }}" name="admin_notes" rows="3" placeholder="Add any notes about this plan approval..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-success">
                            <i class="fas fa-check me-2"></i>Approve Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal{{ $selection->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>
                        Reject Plan Selection #{{ $selection->id }}
                    </h5>
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
                            <label for="reject_notes{{ $selection->id }}" class="form-label">
                                Rejection Reason <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reject_notes{{ $selection->id }}" name="admin_notes" rows="3" placeholder="Please provide a reason for rejecting this plan selection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-danger">
                            <i class="fas fa-times me-2"></i>Reject Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection