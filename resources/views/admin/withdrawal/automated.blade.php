@extends('layouts.admin')

@section('title', 'Automated Withdrawal System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-robot"></i> Automated Withdrawal System
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="toggleAutomation()">
                            <i class="fas fa-play"></i> Enable Automation
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="processAllWithdrawals()">
                            <i class="fas fa-cogs"></i> Process All
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="refreshStatistics()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- System Status -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending</span>
                                    <span class="info-box-number" id="pending-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Approved</span>
                                    <span class="info-box-number" id="approved-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-double"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number" id="completed-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Rejected</span>
                                    <span class="info-box-number" id="rejected-count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Overview -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Financial Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Pending Amount:</strong>
                                            <span id="pending-amount" class="text-warning">$0.00</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Completed Amount:</strong>
                                            <span id="completed-amount" class="text-success">$0.00</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Admin Balance:</strong>
                                            <span id="admin-balance" class="text-primary">0.000000 USDT</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">System Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Network Status:</strong>
                                            <span id="network-status" class="badge badge-success">Connected</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Automation:</strong>
                                            <span id="automation-status" class="badge badge-secondary">Disabled</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Last Processed:</strong>
                                            <span id="last-processed">Never</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Log -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Processing Log</h5>
                                </div>
                                <div class="card-body">
                                    <div id="processing-log" class="log-container" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px;">
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

<!-- Include required scripts -->
<script src="https://cdn.jsdelivr.net/npm/web3@1.8.0/dist/web3.min.js"></script>
<script src="{{ asset('js/automated-withdrawal.js') }}"></script>

<script>
// Additional JavaScript for the automated withdrawal system
let automationEnabled = false;
let processingInterval = null;

function toggleAutomation() {
    automationEnabled = !automationEnabled;
    
    const button = document.querySelector('button[onclick="toggleAutomation()"]');
    const status = document.getElementById('automation-status');
    
    if (automationEnabled) {
        button.innerHTML = '<i class="fas fa-stop"></i> Disable Automation';
        button.className = 'btn btn-danger btn-sm';
        status.textContent = 'Enabled';
        status.className = 'badge badge-success';
        
        // Start automated processing
        startAutomatedProcessing();
        addLogEntry('Automation enabled', 'info');
    } else {
        button.innerHTML = '<i class="fas fa-play"></i> Enable Automation';
        button.className = 'btn btn-success btn-sm';
        status.textContent = 'Disabled';
        status.className = 'badge badge-secondary';
        
        // Stop automated processing
        stopAutomatedProcessing();
        addLogEntry('Automation disabled', 'warning');
    }
}

function startAutomatedProcessing() {
    // Process withdrawals every 30 seconds when automation is enabled
    processingInterval = setInterval(async () => {
        if (automationEnabled) {
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
                    addLogEntry(`Processed ${data.data.processed} withdrawals (${data.data.successful} successful)`, 'success');
                    updateStatistics(data.data);
                } else {
                    addLogEntry(`Processing failed: ${data.message}`, 'error');
                }
            } catch (error) {
                addLogEntry(`Processing error: ${error.message}`, 'error');
            }
        }
    }, 30000);
}

function stopAutomatedProcessing() {
    if (processingInterval) {
        clearInterval(processingInterval);
        processingInterval = null;
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
        networkStatus.className = 'badge badge-success';
    } else {
        networkStatus.textContent = 'Disconnected';
        networkStatus.className = 'badge badge-danger';
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
    
    // Keep only last 50 entries
    const entries = logContainer.querySelectorAll('.log-entry');
    if (entries.length > 50) {
        entries[0].remove();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    refreshStatistics();
    addLogEntry('Automated withdrawal system loaded', 'info');
});
</script>

<style>
.log-container {
    font-family: 'Courier New', monospace;
    font-size: 12px;
}

.log-entry {
    border-bottom: 1px solid #e9ecef;
    padding: 2px 0;
}

.info-box {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.info-box-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
}

.info-box-content {
    flex: 1;
}

.info-box-text {
    display: block;
    font-size: 14px;
    color: #6c757d;
}

.info-box-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #495057;
}
</style>
@endsection
