<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Automated Withdrawal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, #041a2f, #072d42 60%); color: #e0e0e0;">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background: rgba(7, 45, 66, 0.95); border: 1px solid rgba(59, 209, 122, 0.2);">
                    <div class="card-header" style="background: rgba(59, 209, 122, 0.1); border-bottom: 1px solid rgba(59, 209, 122, 0.2);">
                        <h3 class="card-title text-center" style="color: #3bd17a;">
                            <i class="fas fa-robot"></i> Automated Withdrawal System
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x me-3" style="color: #ffc107;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Pending</h6>
                                            <h4 class="mb-0" id="pending-count" style="color: #fff;">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x me-3" style="color: #17a2b8;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Approved</h6>
                                            <h4 class="mb-0" id="approved-count" style="color: #fff;">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-double fa-2x me-3" style="color: #28a745;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Completed</h6>
                                            <h4 class="mb-0" id="completed-count" style="color: #fff;">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box p-3" style="background: rgba(59, 209, 122, 0.1); border-radius: 10px; border: 1px solid rgba(59, 209, 122, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-times-circle fa-2x me-3" style="color: #dc3545;"></i>
                                        <div>
                                            <h6 class="mb-0" style="color: #3bd17a;">Rejected</h6>
                                            <h4 class="mb-0" id="rejected-count" style="color: #fff;">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card" style="background: rgba(59, 209, 122, 0.05); border: 1px solid rgba(59, 209, 122, 0.2);">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0" style="color: #3bd17a;">Financial Overview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong style="color: #3bd17a;">Pending Amount:</strong>
                                                <span id="pending-amount" class="text-warning">$0.00</span>
                                            </div>
                                            <div class="col-6">
                                                <strong style="color: #3bd17a;">Completed Amount:</strong>
                                                <span id="completed-amount" class="text-success">$0.00</span>
                                            </div>
                                        </div>
                                        <hr style="border-color: rgba(59, 209, 122, 0.3);">
                                        <div class="row">
                                            <div class="col-12">
                                                <strong style="color: #3bd17a;">Admin Balance:</strong>
                                                <span id="admin-balance" class="text-primary">0.000000 USDT</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card" style="background: rgba(59, 209, 122, 0.05); border: 1px solid rgba(59, 209, 122, 0.2);">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0" style="color: #3bd17a;">System Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong style="color: #3bd17a;">Network Status:</strong>
                                                <span id="network-status" class="badge bg-success">Connected</span>
                                            </div>
                                            <div class="col-6">
                                                <strong style="color: #3bd17a;">Automation:</strong>
                                                <span id="automation-status" class="badge bg-secondary">Disabled</span>
                                            </div>
                                        </div>
                                        <hr style="border-color: rgba(59, 209, 122, 0.3);">
                                        <div class="row">
                                            <div class="col-12">
                                                <strong style="color: #3bd17a;">Last Processed:</strong>
                                                <span id="last-processed">Never</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-success btn-lg me-2" onclick="toggleAutomation()">
                                    <i class="fas fa-play"></i> Enable Automation
                                </button>
                                <button type="button" class="btn btn-primary btn-lg me-2" onclick="processAllWithdrawals()">
                                    <i class="fas fa-cogs"></i> Process All
                                </button>
                                <button type="button" class="btn btn-info btn-lg" onclick="refreshStatistics()">
                                    <i class="fas fa-sync"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card" style="background: rgba(59, 209, 122, 0.05); border: 1px solid rgba(59, 209, 122, 0.2);">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0" style="color: #3bd17a;">Processing Log</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="processing-log" class="log-container" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; color: #000;">
                                            <div class="log-entry">
                                                <span class="text-muted">[System] Automated withdrawal system initialized</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@1.8.0/dist/web3.min.js"></script>

    <script>
        let automationEnabled = false;
        let processingInterval = null;

        function toggleAutomation() {
            automationEnabled = !automationEnabled;
            
            const button = document.querySelector('button[onclick="toggleAutomation()"]');
            const status = document.getElementById('automation-status');
            
            if (automationEnabled) {
                button.innerHTML = '<i class="fas fa-stop"></i> Disable Automation';
                button.className = 'btn btn-danger btn-lg me-2';
                status.textContent = 'Enabled';
                status.className = 'badge bg-success';
                addLogEntry('Automation enabled', 'info');
            } else {
                button.innerHTML = '<i class="fas fa-play"></i> Enable Automation';
                button.className = 'btn btn-success btn-lg me-2';
                status.textContent = 'Disabled';
                status.className = 'badge bg-secondary';
                addLogEntry('Automation disabled', 'warning');
            }
        }

        async function processAllWithdrawals() {
            const button = document.querySelector('button[onclick="processAllWithdrawals()"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            button.disabled = true;
            
            try {
                const response = await fetch('/admin/withdrawals/process-all', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    addLogEntry(`Manual processing completed: ${data.data.processed} processed, ${data.data.successful} successful`, 'success');
                    updateStatistics(data.data);
                } else {
                    addLogEntry(`Manual processing failed: ${data.message}`, 'error');
                }
            } catch (error) {
                addLogEntry(`Manual processing error: ${error.message}`, 'error');
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        async function refreshStatistics() {
            try {
                const response = await fetch('/admin/withdrawals/statistics', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    updateStatistics(data.data);
                    addLogEntry('Statistics refreshed', 'info');
                }
            } catch (error) {
                addLogEntry(`Failed to refresh statistics: ${error.message}`, 'error');
            }
        }

        function updateStatistics(stats) {
            document.getElementById('pending-count').textContent = stats.total_pending;
            document.getElementById('approved-count').textContent = stats.total_approved;
            document.getElementById('completed-count').textContent = stats.total_completed;
            document.getElementById('rejected-count').textContent = stats.total_rejected;
            document.getElementById('pending-amount').textContent = '$' + parseFloat(stats.total_amount_pending).toFixed(2);
            document.getElementById('completed-amount').textContent = '$' + parseFloat(stats.total_amount_completed).toFixed(2);
            document.getElementById('admin-balance').textContent = parseFloat(stats.admin_balance).toFixed(6) + ' USDT';
            
            const networkStatus = document.getElementById('network-status');
            if (stats.network_status.success) {
                networkStatus.textContent = 'Connected';
                networkStatus.className = 'badge bg-success';
            } else {
                networkStatus.textContent = 'Disconnected';
                networkStatus.className = 'badge bg-danger';
            }
            
            document.getElementById('last-processed').textContent = new Date().toLocaleString();
        }

        function addLogEntry(message, type = 'info') {
            const logContainer = document.getElementById('processing-log');
            const logEntry = document.createElement('div');
            logEntry.className = 'log-entry mb-1';
            
            const timestamp = new Date().toLocaleTimeString();
            const typeClass = type === 'error' ? 'text-danger' : type === 'success' ? 'text-success' : type === 'warning' ? 'text-warning' : 'text-muted';
            
            logEntry.innerHTML = `<span class="text-muted">[${timestamp}]</span> <span class="${typeClass}">${message}</span>`;
            
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
            
            const entries = logContainer.querySelectorAll('.log-entry');
            if (entries.length > 50) {
                entries[0].remove();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            refreshStatistics();
            addLogEntry('Automated withdrawal system loaded', 'info');
        });
    </script>
</body>
</html>
