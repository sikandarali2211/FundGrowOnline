@extends('layouts.admin')

@section('content')
<style>
    /* ======================== Dark Teal + Neon Green Theme ======================== */
    :root {
        --fg-bg: #061a1f;
        --fg-surface: #0a242b;
        --fg-surface-2: #0d2b33;
        --fg-border: #0f3640;
        --fg-text: #cfe7e3;
        --fg-muted: #7aa5a0;
        --fg-accent: #22e3a0;
        /* neon green */
        --fg-accent-2: #20c9bb;
        /* cyan/teal */
        --fg-primary: #4a90e2;
        /* existing primary - kept for minor accents if needed */
        --fg-warning: #f5c84b;
        --fg-danger: #ff6b6b;
        --fg-success: #22e3a0;
        --fg-info: #20c9bb;
        --fg-hover: rgba(34, 227, 160, .08);
        --fg-shadow: 0 10px 30px rgba(0, 0, 0, .35);
    }

    /* Body Background */
    body {
        background:
            radial-gradient(1200px 600px at 10% -10%, rgba(34, 227, 160, .08), transparent 60%),
            radial-gradient(900px 500px at 110% 10%, rgba(32, 201, 187, .10), transparent 60%),
            var(--fg-bg) !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--fg-text);
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        margin-top: 4rem;
        box-shadow: var(--fg-shadow);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .page-header::after {
        content: "";
        position: absolute;
        inset: -2px;
        background: radial-gradient(600px 200px at 20% -40%, rgba(34, 227, 160, .12), transparent 60%);
        pointer-events: none;
    }

    .page-header h1 {
        color: var(--fg-accent);
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0;
        letter-spacing: .5px;
    }

    /* Outer wrapper bg */
    .content-wrapper {
        background: transparent !important;
    }

    /* Plan Grid */
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .plan-card {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: var(--fg-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .plan-card:hover {
        transform: translateY(-4px);
        border-color: rgba(34, 227, 160, .35);
        box-shadow: 0 14px 40px rgba(0, 0, 0, .45);
    }

    .plan-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--fg-text);
        margin-bottom: 1rem;
        letter-spacing: .3px;
    }

    .plan-name::after {
        content: "";
        display: block;
        width: 42px;
        height: 3px;
        border-radius: 2px;
        margin: .45rem auto 0;
        background: linear-gradient(90deg, var(--fg-accent), var(--fg-accent-2));
        opacity: .8;
    }

    .plan-details {
        display: grid;
        gap: .75rem;
        grid-template-columns: 1fr 1fr;
    }

    .plan-detail {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--fg-border);
        border-radius: 12px;
        padding: .85rem;
        color: var(--fg-text);
    }

    .plan-detail-label {
        font-size: 0.8rem;
        color: var(--fg-muted);
        margin-bottom: 0.2rem;
    }

    .plan-detail-value {
        font-size: 1.1rem;
        font-weight: 800;
        background: linear-gradient(90deg, var(--fg-accent), #b1ffe5);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .plan-users-count {
        margin-top: .9rem;
        padding: .85rem;
        background: rgba(34, 227, 160, .06);
        border: 1px solid rgba(34, 227, 160, .25);
        border-radius: 12px;
    }

    .plan-users-count-label {
        font-size: 0.85rem;
        color: var(--fg-muted);
    }

    .plan-users-count-value {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--fg-accent);
    }

    /* Users Table Card */
    .users-table {
        margin-top: 2rem;
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: var(--fg-shadow);
    }

    .users-table h3 {
        text-align: center;
        margin-bottom: 1rem;
        font-weight: 800;
        color: var(--fg-text);
    }

    /* Table */
    .table {
        color: var(--fg-text);
        margin-bottom: 0;
    }

    .table thead th {
        background: rgba(255, 255, 255, 0.03);
        color: var(--fg-muted);
        border: 0;
        border-bottom: 1px solid var(--fg-border);
        font-weight: 700;
        text-transform: uppercase;
        font-size: .8rem;
        letter-spacing: .6px;
    }

    .table tbody td {
        color: var(--fg-text);
        border-top: 1px solid var(--fg-border);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--fg-hover);
    }

    /* Bootstrap primary badge override for plan name in table */
    .badge.bg-primary {
        background: linear-gradient(90deg, var(--fg-accent-2), var(--fg-accent)) !important;
        color: #05241d;
        border: 0;
        font-weight: 700;
    }

    .badge.bg-secondary {
        background: linear-gradient(90deg, var(--fg-surface-2), var(--fg-border)) !important;
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
        font-weight: 600;
    }

    .investment-summary {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination-info {
        color: var(--fg-muted);
        font-size: 0.9rem;
    }

    .pagination-links {
        display: flex;
        align-items: center;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        border: 1px solid transparent;
        letter-spacing: .3px;
    }

    .status-pending {
        background: rgba(245, 200, 75, .12);
        color: #f5d26b;
        border-color: rgba(245, 200, 75, .25);
    }

    .status-active {
        background: rgba(34, 227, 160, .12);
        color: #88f0cc;
        border-color: rgba(34, 227, 160, .25);
    }

    .status-completed {
        background: rgba(32, 201, 187, .12);
        color: #66e0d6;
        border-color: rgba(32, 201, 187, .25);
    }

    .status-cancelled {
        background: rgba(255, 107, 107, .12);
        color: #ff9d9d;
        border-color: rgba(255, 107, 107, .25);
    }

    /* User Avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: radial-gradient(70% 70% at 30% 30%, var(--fg-accent), var(--fg-accent-2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        color: #04231c;
        box-shadow: inset 0 0 12px rgba(0, 0, 0, .25);
        border: 1px solid rgba(34, 227, 160, .35);
    }

    /* Selects + Buttons */
    .form-select {
        background: rgba(255, 255, 255, .04);
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
        border-radius: 10px;
        padding: .45rem .75rem;
    }

    .form-select:focus {
        border-color: var(--fg-accent);
        box-shadow: 0 0 0 .2rem rgba(34, 227, 160, .15);
        background: rgba(255, 255, 255, .06);
        color: var(--fg-text);
    }

    .btn-update {
        background: linear-gradient(135deg, var(--fg-accent-2), var(--fg-accent));
        border: none;
        color: #05241d;
        padding: 0.5rem 0.9rem;
        border-radius: 10px;
        font-weight: 800;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(34, 227, 160, .3);
    }

    /* Muted texts inside table (email etc.) */
    .text-muted {
        color: var(--fg-muted) !important;
    }

    /* Pagination Styling */
    .pagination-container {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        border: 1px solid var(--fg-border);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--fg-shadow);
    }

    .pagination .page-link {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--fg-border);
        color: var(--fg-text);
        border-radius: 10px;
        padding: 0.6rem 1rem;
        margin: 0 0.3rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: var(--fg-hover);
        color: var(--fg-accent);
        border-color: var(--fg-accent);
        transform: translateY(-2px);
    }

    .pagination .active .page-link {
        background: linear-gradient(135deg, var(--fg-accent), var(--fg-accent-2));
        color: #05241d;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(34, 227, 160, 0.4);
    }

    .pagination .disabled .page-link {
        background: rgba(255, 255, 255, 0.02);
        color: var(--fg-muted);
        border-color: var(--fg-border);
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .plans-grid {
            grid-template-columns: 1fr;
        }

        .plan-details {
            grid-template-columns: 1fr 1fr;
        }
        
        .pagination .page-link {
            padding: 0.4rem 0.8rem;
            margin: 0 0.2rem;
            font-size: 0.9rem;
        }
        
        .pagination-container .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .pagination-info {
            text-align: center;
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h1>Investment Dashboard</h1>
        </div>

        <!-- Investment Plans Overview -->
        <div class="plans-grid">
            @php $excludePlans = ['demo plan','pool wallet plan','grower plan']; @endphp
            @foreach ($plans as $plan)
            @if(in_array(strtolower($plan->name ?? ''), $excludePlans))
            @continue
            @endif
            <div class="plan-card">
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-details">
                    <div class="plan-detail">
                        <div class="plan-detail-label">Entry Amount</div>
                        <div class="plan-detail-value">${{ number_format($plan->entry_amount, 2) }}</div>
                    </div>
                    <div class="plan-detail">
                        <div class="plan-detail-label">Return %</div>
                        <div class="plan-detail-value">{{ $plan->return_percentage }}%</div>
                    </div>
                </div>
                <div class="plan-users-count">
                    <div class="plan-users-count-label">Users Count</div>
                    <div class="plan-users-count-value">{{ $plan->user_investments_count }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Users Investment Details -->
        <div class="users-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>User Investment Details</h3>
                <div class="investment-summary">
                    <span class="badge bg-primary">
                        Total: {{ $userInvestments->total() }} investments
                    </span>
                    @if($userInvestments->hasPages())
                    <span class="badge bg-secondary ms-2">
                        Page {{ $userInvestments->currentPage() }} of {{ $userInvestments->lastPage() }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Invested Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userInvestments as $investment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        {{ strtoupper(substr($investment->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $investment->user->name }}</div>
                                        <small class="text-muted">{{ $investment->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $investment->investmentPlan->name }}</span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">${{ number_format($investment->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $investment->status }}">
                                    {{ ucfirst($investment->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $investment->invested_at ? $investment->invested_at->format('M d, Y') : 'Not set' }}
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Status Update -->
                                    <select class="form-select status-select"
                                        data-investment-id="{{ $investment->id }}" style="width: 120px;">
                                        <option value="pending" {{ $investment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ $investment->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $investment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $investment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>

                                    <!-- Plan Update -->
                                    <select class="form-select plan-select"
                                        data-investment-id="{{ $investment->id }}" style="width: 150px;">
                                        @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}"
                                            {{ $investment->investment_plan_id === $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <button class="btn btn-update btn-sm"
                                        onclick="updateInvestment({{ $investment->id }})">
                                        <i class="fas fa-sync-alt"></i> Update
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No user investments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Showing {{ $userInvestments->firstItem() ?? 0 }} to {{ $userInvestments->lastItem() ?? 0 }} 
                            of {{ $userInvestments->total() }} investments
                        </small>
                        <!-- Debug info -->
                        <br><small class="text-info">
                            Debug: Page {{ $userInvestments->currentPage() }} of {{ $userInvestments->lastPage() }} | 
                            Has Pages: {{ $userInvestments->hasPages() ? 'Yes' : 'No' }} | 
                            Per Page: {{ $userInvestments->perPage() }}
                        </small>
                    </div>
                    <div class="pagination-links">
                        @if ($userInvestments->hasPages())
                        <nav aria-label="Investment pagination">
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if ($userInvestments->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo;</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $userInvestments->previousPageUrl() }}" rel="prev">&laquo;</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($userInvestments->getUrlRange(1, $userInvestments->lastPage()) as $page => $url)
                                    @if ($page == $userInvestments->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($userInvestments->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $userInvestments->nextPageUrl() }}" rel="next">&raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">&raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                        @else
                        <small class="text-muted">All {{ $userInvestments->total() }} investments on this page</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    function updateInvestment(investmentId) {
        const statusSelect = document.querySelector(`select[data-investment-id="${investmentId}"].status-select`);
        const planSelect = document.querySelector(`select[data-investment-id="${investmentId}"].plan-select`);

        const status = statusSelect.value;
        const planId = planSelect.value;

        // Update status
        fetch(`/admin/investment-plans/${investmentId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: 'linear-gradient(to right, #17d89a, #22e3a0)'
                    }).showToast();

                    const statusBadge = document.querySelector(
                        `tr:has(select[data-investment-id="${investmentId}"]) .status-badge`);
                    statusBadge.className = `status-badge status-${status}`;
                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                } else {
                    throw new Error();
                }
            })
            .catch(() => {
                Toastify({
                    text: "Error updating status",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: 'linear-gradient(to right, #ff6b6b, #ff9f7f)'
                }).showToast();
            });

        // Update plan
        fetch(`/admin/investment-plans/${investmentId}/plan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    investment_plan_id: planId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Toastify({
                        text: data.message,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: 'linear-gradient(to right, #17d89a, #22e3a0)'
                    }).showToast();

                    const planBadge = document.querySelector(
                        `tr:has(select[data-investment-id="${investmentId}"]) .badge`);
                    if (planBadge) planBadge.textContent = data.plan_name;
                } else {
                    throw new Error();
                }
            })
            .catch(() => {
                Toastify({
                    text: "Error updating plan",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: 'linear-gradient(to right, #ff6b6b, #ff9f7f)'
                }).showToast();
            });
    }
</script>

<!-- Font Awesome for icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
@endsection