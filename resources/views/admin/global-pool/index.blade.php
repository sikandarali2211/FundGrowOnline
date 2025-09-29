@extends('layouts.admin')
@section('content')
<body style="background: linear-gradient(135deg, #041a2f, #072d42 60%); color: #e0e0e0;">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background: rgba(7, 45, 66, 0.95); border: 1px solid rgba(59, 209, 122, 0.2);">
                    <div class="card-header" style="background: rgba(59, 209, 122, 0.1); border-bottom: 1px solid rgba(59, 209, 122, 0.2);">
                        <h3 class="card-title text-center" style="color: #3bd17a;">
                            <i class="fas fa-globe"></i> Global Pool Management
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Global Pool Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-dollar-sign fa-2x me-3" style="color: #3bd17a;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Total Amount</h6>
                                            <h4 class="mb-0" id="total-amount" style="color: #fff;">$0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chart-line fa-2x me-3" style="color: #17a2b8;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Transactions</h6>
                                            <h4 class="mb-0" id="transaction-count" style="color: #fff;">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x me-3" style="color: #ffc107;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Last Contribution</h6>
                                            <h4 class="mb-0" id="last-contribution" style="color: #fff;">$0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Distribution Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card" style="background: rgba(59, 209, 122, 0.05); border: 1px solid rgba(59, 209, 122, 0.2);">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0" style="color: #3bd17a;">Commission Distribution System</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <i class="fas fa-piggy-bank fa-3x mb-2" style="color: #28a745;"></i>
                                                    <h6 style="color: #3bd17a;">Pool Commission</h6>
                                                    <h4 style="color: #28a745;">60%</h4>
                                                    <small style="color: #a5f2d5;">Goes to user's pool wallet</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <i class="fas fa-chart-pie fa-3x mb-2" style="color: #17a2b8;"></i>
                                                    <h6 style="color: #3bd17a;">Profit Commission</h6>
                                                    <h4 style="color: #17a2b8;">30%</h4>
                                                    <small style="color: #a5f2d5;">Goes to user's profit wallet</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <i class="fas fa-globe fa-3x mb-2" style="color: #dc3545;"></i>
                                                    <h6 style="color: #3bd17a;">Global Pool</h6>
                                                    <h4 style="color: #dc3545;">10%</h4>
                                                    <small style="color: #a5f2d5;">Goes to admin's global pool</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Commission Transactions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card" style="background: rgba(59, 209, 122, 0.05); border: 1px solid rgba(59, 209, 122, 0.2);">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0" style="color: #3bd17a;">Recent Global Pool Contributions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="recent-contributions">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                                <p>Loading recent contributions...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-info btn-lg me-2" onclick="refreshGlobalPoolStats()">
                                    <i class="fas fa-sync"></i> Refresh Stats
                                </button>
                                <button type="button" class="btn btn-success btn-lg me-2" onclick="viewCommissionHistory()">
                                    <i class="fas fa-history"></i> View History
                                </button>
                                <button type="button" class="btn btn-warning btn-lg" onclick="exportGlobalPoolData()">
                                    <i class="fas fa-download"></i> Export Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global Pool Statistics
        let globalPoolStats = {
            total_amount: 0,
            transaction_count: 0,
            last_contribution: 0,
            last_updated: null
        };

        // Load global pool statistics
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

        // Update global pool display
        function updateGlobalPoolDisplay() {
            document.getElementById('total-amount').textContent = '$' + parseFloat(globalPoolStats.total_amount).toFixed(2);
            document.getElementById('transaction-count').textContent = globalPoolStats.transaction_count;
            document.getElementById('last-contribution').textContent = '$' + parseFloat(globalPoolStats.last_contribution).toFixed(2);
        }

        // Load recent contributions
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
                    '<div class="text-center text-muted"><p>Failed to load recent contributions</p></div>';
            }
        }

        // Display recent contributions
        function displayRecentContributions(contributions) {
            const container = document.getElementById('recent-contributions');
            
            if (contributions.length === 0) {
                container.innerHTML = '<div class="text-center text-muted"><p>No contributions yet</p></div>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-dark table-striped">';
            html += '<thead><tr><th>Date</th><th>Amount</th><th>User</th><th>Type</th></tr></thead><tbody>';
            
            contributions.forEach(contribution => {
                html += `<tr>
                    <td>${new Date(contribution.created_at).toLocaleString()}</td>
                    <td class="text-success">$${parseFloat(contribution.global_pool_commission).toFixed(2)}</td>
                    <td>${contribution.user.name}</td>
                    <td><span class="badge bg-${contribution.commission_type === 'second_plan' ? 'success' : 'info'}">${contribution.commission_type_text}</span></td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        // Refresh global pool stats
        function refreshGlobalPoolStats() {
            loadGlobalPoolStats();
            loadRecentContributions();
            showNotification('Global pool statistics refreshed', 'success');
        }

        // View commission history
        function viewCommissionHistory() {
            showNotification('Commission history feature coming soon', 'info');
        }

        // Export global pool data
        function exportGlobalPoolData() {
            showNotification('Export feature coming soon', 'info');
        }

        // Show notification
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadGlobalPoolStats();
            loadRecentContributions();
        });
    </script>

    <style>
        .info-box {
            font-family: 'Poppins', sans-serif;
        }

        .info-box h6 {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-box h4 {
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .card {
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }

        .table-dark {
            background-color: rgba(7, 45, 66, 0.8);
        }

        .table-dark th {
            background-color: rgba(59, 209, 122, 0.2);
            color: #3bd17a;
            border-color: rgba(59, 209, 122, 0.3);
        }

        .table-dark td {
            border-color: rgba(59, 209, 122, 0.1);
        }
    </style>
@endsection