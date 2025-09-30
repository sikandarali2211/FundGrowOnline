@extends('layouts.admin')
@section('content')
<body style="background: linear-gradient(135deg, #041a2f, #072d42 60%); color: #e0e0e0; min-height: 100vh;">
    <div class="py-5 main-panel" style="margin-top: 60px;" >
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="mb-1">Global Pool Management</h2>
                            <p class="text-muted mb-0">Monitor and manage global commission distribution</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Total Amount</span>
                        <h3 class="stat-value" id="total-amount">$0.00</h3>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>From all contributions</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-blue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Transactions</span>
                        <h3 class="stat-value" id="transaction-count">0</h3>
                        <div class="stat-trend">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Total contributions</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-yellow">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Last Contribution</span>
                        <h3 class="stat-value" id="last-contribution">$0.00</h3>
                        <div class="stat-trend">
                            <i class="fas fa-history"></i>
                            <span>Most recent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Distribution -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Commission Distribution System
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4" hidden>
                                <div class="distribution-card pool-card">
                                    <div class="distribution-icon">
                                        <i class="fas fa-piggy-bank"></i>
                                    </div>
                                    <h6>Pool Commission</h6>
                                    <div class="percentage">60%</div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 60%; background: linear-gradient(90deg, #28a745, #20c997);"></div>
                                    </div>
                                    <p class="distribution-desc">Goes to user's pool wallet</p>
                                </div>
                            </div>
                            <div class="col-md-4" hidden>
                                <div class="distribution-card profit-card">
                                    <div class="distribution-icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <h6>Profit Commission</h6>
                                    <div class="percentage">30%</div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 30%; background: linear-gradient(90deg, #17a2b8, #138496);"></div>
                                    </div>
                                    <p class="distribution-desc">Goes to user's profit wallet</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="distribution-card global-card">
                                    <div class="distribution-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <h6>Global Pool</h6>
                                    <div class="percentage">10%</div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 10%; background: linear-gradient(90deg, #dc3545, #c82333);"></div>
                                    </div>
                                    <p class="distribution-desc">Goes to admin's global pool</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Contributions -->
        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Recent Global Pool Contributions
                        </h5>
                        <button class="btn-refresh" onclick="refreshGlobalPoolStats()">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div id="recent-contributions">
                            <div class="loading-state">
                                <div class="spinner"></div>
                                <p>Loading recent contributions...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="action-buttons">
                    <button class="btn-action btn-action-primary" onclick="refreshGlobalPoolStats()">
                        <i class="fas fa-sync"></i>
                        <span>Refresh Stats</span>
                    </button>
                    <button class="btn-action btn-action-success" onclick="viewCommissionHistory()">
                        <i class="fas fa-history"></i>
                        <span>View History</span>
                    </button>
                    <!-- <button class="btn-action btn-action-warning" onclick="exportGlobalPoolData()">
                        <i class="fas fa-download"></i>
                        <span>Export Data</span>
                    </button> -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let globalPoolStats = {
            total_amount: 0,
            transaction_count: 0,
            last_contribution: 0,
            last_updated: null
        };

        async function loadGlobalPoolStats() {
            try {
                const response = await fetch('/admin/global-pool/statistics', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        globalPoolStats = data.data;
                        updateGlobalPoolDisplay();
                    }
                }
            } catch (error) {
                console.error('Failed to load global pool stats:', error);
                showNotification('Failed to load global pool statistics', 'error');
            }
        }

        function updateGlobalPoolDisplay() {
            animateValue('total-amount', 0, parseFloat(globalPoolStats.total_amount), 1000, true);
            animateValue('transaction-count', 0, globalPoolStats.transaction_count, 1000, false);
            animateValue('last-contribution', 0, parseFloat(globalPoolStats.last_contribution), 1000, true);
        }

        function animateValue(id, start, end, duration, isCurrency) {
            const element = document.getElementById(id);
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = isCurrency ? '$' + current.toFixed(2) : Math.floor(current);
            }, 16);
        }

        async function loadRecentContributions() {
            try {
                const response = await fetch('/admin/global-pool/recent-contributions', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        displayRecentContributions(data.data);
                    }
                }
            } catch (error) {
                console.error('Failed to load recent contributions:', error);
                document.getElementById('recent-contributions').innerHTML = 
                    '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Failed to load contributions</p></div>';
            }
        }

        function displayRecentContributions(contributions) {
            const container = document.getElementById('recent-contributions');
            
            if (contributions.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No contributions yet</p></div>';
                return;
            }

            let html = '<div class="table-modern-wrapper"><table class="table-modern">';
            html += '<thead><tr><th>Date & Time</th><th>Amount</th><th>User</th><th>Type</th></tr></thead><tbody>';
            
            contributions.forEach(contribution => {
                html += `<tr>
                    <td>
                        <div class="date-cell">
                            <i class="fas fa-calendar-alt"></i>
                            <span>${new Date(contribution.created_at).toLocaleString()}</span>
                        </div>
                    </td>
                    <td>
                        <span class="amount-badge">$${parseFloat(contribution.global_pool_commission).toFixed(2)}</span>
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">${contribution.user.name.charAt(0)}</div>
                            <span>${contribution.user.name}</span>
                        </div>
                    </td>
                    <td>
                        <span class="type-badge ${contribution.commission_type === 'second_plan' ? 'badge-success' : 'badge-info'}">
                            ${contribution.commission_type_text}
                        </span>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        function refreshGlobalPoolStats() {
            const btn = event.currentTarget;
            btn.classList.add('rotating');
            
            loadGlobalPoolStats();
            loadRecentContributions();
            showNotification('Statistics refreshed successfully', 'success');
            
            setTimeout(() => btn.classList.remove('rotating'), 1000);
        }

        function viewCommissionHistory() {
            showNotification('Commission history feature coming soon', 'info');
        }

        function exportGlobalPoolData() {
            showNotification('Export feature coming soon', 'info');
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('show'), 10);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadGlobalPoolStats();
            loadRecentContributions();
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            background: rgba(7, 45, 66, 0.6);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 16px;
            padding: 24px 32px;
            backdrop-filter: blur(10px);
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3bd17a, #28a745);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            box-shadow: 0 4px 20px rgba(59, 209, 122, 0.3);
        }

        .page-header h2 {
            color: #3bd17a;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
        }

        .page-header p {
            font-size: 14px;
            color: #a5f2d5;
        }

        .stat-card {
            background: rgba(7, 45, 66, 0.6);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
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

        .bg-gradient-green {
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.3);
        }

        .bg-gradient-blue {
            background: linear-gradient(135deg, #17a2b8, #138496);
            box-shadow: 0 4px 20px rgba(23, 162, 184, 0.3);
        }

        .bg-gradient-yellow {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            box-shadow: 0 4px 20px rgba(255, 193, 7, 0.3);
        }

        .stat-content {
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
            margin: 0 0 8px 0;
            line-height: 1;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #3bd17a;
            font-size: 12px;
        }

        .stat-trend i {
            font-size: 10px;
        }

        .modern-card {
            background: rgba(7, 45, 66, 0.6);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .card-header-modern {
            background: rgba(59, 209, 122, 0.1);
            border-bottom: 1px solid rgba(59, 209, 122, 0.2);
            padding: 20px 24px;
        }

        .card-header-modern h5 {
            color: #3bd17a;
            font-weight: 600;
            font-size: 18px;
        }

        .btn-refresh {
            background: rgba(59, 209, 122, 0.1);
            border: 1px solid rgba(59, 209, 122, 0.3);
            color: #3bd17a;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-refresh:hover {
            background: rgba(59, 209, 122, 0.2);
            transform: scale(1.05);
        }

        .btn-refresh.rotating i {
            animation: rotate 1s linear;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .distribution-card {
            background: rgba(59, 209, 122, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 14px;
            padding: 28px 24px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .distribution-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .distribution-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }

        .pool-card .distribution-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }

        .profit-card .distribution-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
            box-shadow: 0 4px 20px rgba(23, 162, 184, 0.4);
        }

        .global-card .distribution-icon {
            background: linear-gradient(135deg, #dc3545, #c82333);
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.4);
        }

        .distribution-card h6 {
            color: #3bd17a;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .percentage {
            font-size: 36px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
        }

        .progress-bar-custom {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .distribution-desc {
            color: #a5f2d5;
            font-size: 13px;
            margin: 0;
        }

        .loading-state, .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #a5f2d5;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(59, 209, 122, 0.2);
            border-top-color: #3bd17a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state i {
            font-size: 48px;
            color: rgba(59, 209, 122, 0.3);
            margin-bottom: 16px;
        }

        .table-modern-wrapper {
            overflow-x: auto;
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead tr {
            background: rgba(59, 209, 122, 0.1);
        }

        .table-modern th {
            padding: 16px 24px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #3bd17a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(59, 209, 122, 0.2);
        }

        .table-modern td {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(59, 209, 122, 0.1);
            color: #e0e0e0;
        }

        .table-modern tbody tr {
            transition: background 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: rgba(59, 209, 122, 0.05);
        }

        .date-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-cell i {
            color: #3bd17a;
            font-size: 14px;
        }

        .amount-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #3bd17a, #28a745);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .type-badge {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .badge-info {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 14px 32px;
            border-radius: 12px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-action i {
            font-size: 16px;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            box-shadow: 0 4px 16px rgba(23, 162, 184, 0.3);
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(23, 162, 184, 0.4);
        }

        .btn-action-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
        }

        .btn-action-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(40, 167, 69, 0.4);
        }

        .btn-action-warning {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: white;
            box-shadow: 0 4px 16px rgba(255, 193, 7, 0.3);
        }

        .btn-action-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 193, 7, 0.4);
        }

        .notification {
            position: fixed;
            top: 24px;
            right: 24px;
            min-width: 320px;
            background: rgba(7, 45, 66, 0.95);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 9999;
            backdrop-filter: blur(10px);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification i {
            font-size: 20px;
        }

        .notification-success i {
            color: #28a745;
        }

        .notification-error i {
            color: #dc3545;
        }

        .notification-info i {
            color: #17a2b8;
        }

        .notification span {
            color: #e0e0e0;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection