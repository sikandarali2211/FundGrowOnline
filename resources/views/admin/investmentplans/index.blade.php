@extends('layouts.admin')

@section('content')
    <style>
        :root {
            --primary-color: #4a90e2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --white: #fff;
        }

        /* Body Background */
        body {
            background: linear-gradient(145deg, #072d42, #22384e);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--white);
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            margin-top: 4rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .page-header h1 {
            color: var(--white);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        /* Plan Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .plan-card {
            background: rgba(25, 40, 60, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            transition: all 0.3s ease;
            text-align: center;
        }

        .plan-card:hover {
            transform: translateY(-6px);
            background: rgba(25, 40, 60, 0.95);
        }

        .plan-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.2rem;
        }

        .plan-detail {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem;
            color: var(--white);
        }

        .plan-detail-label {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-bottom: 0.3rem;
        }

        .plan-detail-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .plan-users-count {
            margin-top: 1.2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }

        .plan-users-count-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .plan-users-count-value {
            font-size: 1.6rem;
            font-weight: 800;
        }

        /* Users Table */
        .users-table {
            margin-top: 3rem;
            background: linear-gradient(145deg, #072d42, #22384e);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        }

        .users-table h3 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: var(--white);
        }

        .table thead th {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: none;
            font-weight: 600;
        }

        .table tbody td {
            color: #ddd;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #ffc10733;
            color: #ffc107;
        }

        .status-active {
            background: #28a74533;
            color: #28a745;
        }

        .status-completed {
            background: #4a90e233;
            color: #4a90e2;
        }

        .status-cancelled {
            background: #dc354533;
            color: #dc3545;
        }

        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--white);
        }

        /* Buttons */
        .btn-update {
            background-color: var(--primary-color);
            border: none;
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            background-color: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
        }
    </style>


    <div class="main-panel">
        <div class="content-wrapper" style=" background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="page-header" >
                <h1 style="color: #3bd17a;">Investment Dashboard</h1>
            </div>

            <!-- Investment Plans Overview -->
            <div class="plans-grid">
                @foreach ($plans as $plan)
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
                <h3>User Investment Details</h3>
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
                                        <span
                                            class="text-success fw-bold">${{ number_format($investment->amount, 2) }}</span>
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
                                                <option value="pending"
                                                    {{ $investment->status === 'pending' ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="active"
                                                    {{ $investment->status === 'active' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="completed"
                                                    {{ $investment->status === 'completed' ? 'selected' : '' }}>Completed
                                                </option>
                                                <option value="cancelled"
                                                    {{ $investment->status === 'cancelled' ? 'selected' : '' }}>Cancelled
                                                </option>
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
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)'
                        }).showToast();

                        // Update status badge
                        const statusBadge = document.querySelector(
                            `tr:has(select[data-investment-id="${investmentId}"]) .status-badge`);
                        statusBadge.className = `status-badge status-${status}`;
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    }
                })
                .catch(error => {
                    Toastify({
                        text: "Error updating status",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)'
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)'
                        }).showToast();

                        // Update plan badge
                        const planBadge = document.querySelector(
                            `tr:has(select[data-investment-id="${investmentId}"]) .badge`);
                        planBadge.textContent = data.plan_name;
                    }
                })
                .catch(error => {
                    Toastify({
                        text: "Error updating plan",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)'
                    }).showToast();
                });
        }
    </script>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

@endsection
