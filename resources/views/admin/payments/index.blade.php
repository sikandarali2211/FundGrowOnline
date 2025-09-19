@extends('layouts.admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); padding-top: 80px; min-height: calc(100vh - 80px); color: #fff;">
        <div class="page-header" style="background: linear-gradient(145deg, #072d42, #22384e); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6); color: #3bd17a;">
            <h3 class="page-title" style="color: #3bd17a;">
                <i class="fa fa-credit-card me-2"></i>Payment Management
            </h3>
            <p class="mb-0">Manage and verify user payments for investment plans</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div style="background: rgba(59, 209, 122, 0.1); border: 1px solid rgba(59, 209, 122, 0.3); border-radius: 12px; padding: 20px; text-align: center;">
                    <div id="pendingCount" style="font-size: 2rem; font-weight: 700; color: #3bd17a; margin: 10px 0;">0</div>
                    <div style="color: #a5f2d5; font-size: 0.9rem;">Pending Payments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: rgba(59, 209, 122, 0.1); border: 1px solid rgba(59, 209, 122, 0.3); border-radius: 12px; padding: 20px; text-align: center;">
                    <div id="confirmedCount" style="font-size: 2rem; font-weight: 700; color: #3bd17a; margin: 10px 0;">0</div>
                    <div style="color: #a5f2d5; font-size: 0.9rem;">Confirmed Today</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: rgba(59, 209, 122, 0.1); border: 1px solid rgba(59, 209, 122, 0.3); border-radius: 12px; padding: 20px; text-align: center;">
                    <div id="totalAmount" style="font-size: 2rem; font-weight: 700; color: #3bd17a; margin: 10px 0;">$0</div>
                    <div style="color: #a5f2d5; font-size: 0.9rem;">Total Amount</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: rgba(59, 209, 122, 0.1); border: 1px solid rgba(59, 209, 122, 0.3); border-radius: 12px; padding: 20px; text-align: center;">
                    <div id="successRate" style="font-size: 2rem; font-weight: 700; color: #3bd17a; margin: 10px 0;">0%</div>
                    <div style="color: #a5f2d5; font-size: 0.9rem;">Success Rate</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div style="background: linear-gradient(145deg, #072d42, #22384e); border-radius: 15px; overflow: hidden; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6); margin-bottom: 1.5rem;">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <h4 style="color: #3bd17a;">
                            <i class="fa fa-list me-2"></i>Payment Transactions
                        </h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" onclick="refreshPayments()">
                                <i class="fa fa-refresh me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" style="color: #fff;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Transaction Hash</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                                        <p>Loading payments...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let payments = [];

document.addEventListener('DOMContentLoaded', function() {
    loadPayments();
});

async function loadPayments() {
    try {
        const response = await fetch('{{ route("admin.payments.index") }}');
        const result = await response.json();
        
        if (result.success) {
            payments = result.transactions.data;
            updateStatistics(result.transactions.data);
            renderPaymentsTable(result.transactions.data);
        } else {
            showError('Failed to load payments');
        }
    } catch (error) {
        showError('Error loading payments: ' + error.message);
    }
}

function updateStatistics(data) {
    const pending = data.filter(p => p.status === 'pending').length;
    const confirmed = data.filter(p => p.status === 'confirmed').length;
    const total = data.reduce((sum, p) => sum + parseFloat(p.amount), 0);
    const successRate = data.length > 0 ? Math.round((confirmed / data.length) * 100) : 0;

    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('confirmedCount').textContent = confirmed;
    document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
    document.getElementById('successRate').textContent = successRate + '%';
}

function renderPaymentsTable(data) {
    const tbody = document.getElementById('paymentsTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-3x mb-3"></i>
                    <p>No payments found</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.map(payment => `
        <tr>
            <td><strong>#${payment.id}</strong></td>
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, #3bd17a, #00d4aa); display: flex; align-items: center; justify-content: center; color: #0d1b2a; font-weight: 700; font-size: 1.1rem;">
                        ${payment.user.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <strong>${payment.user.name}</strong><br>
                        <small class="text-muted">${payment.user.email}</small>
                    </div>
                </div>
            </td>
            <td>
                <strong>${payment.plan ? payment.plan.name : 'N/A'}</strong><br>
                <small class="text-muted">$${payment.plan ? payment.plan.amount : '0'}</small>
            </td>
            <td>
                <div style="font-weight: 700; color: #ffc107;">${payment.amount} ${payment.currency}</div>
            </td>
            <td>
                <div style="font-family: 'Courier New', monospace; font-size: 0.85rem; color: #3bd17a; word-break: break-all;" title="${payment.transaction_hash}">
                    ${payment.transaction_hash.substring(0, 20)}...
                </div>
            </td>
            <td>
                <span class="badge badge-${payment.status}" style="border-radius: 12px; padding: 6px 12px; font-weight: 600; background: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3);">${payment.status.toUpperCase()}</span>
            </td>
            <td>${new Date(payment.created_at).toLocaleDateString()}</td>
            <td>
                ${payment.status === 'pending' ? `
                    <div class="d-flex gap-1">
                        <button class="btn btn-success btn-sm" onclick="confirmPayment(${payment.id})" style="padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                            <i class="fa fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectPayment(${payment.id})" style="padding: 6px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                ` : `
                    <span class="text-muted">No actions</span>
                `}
            </td>
        </tr>
    `).join('');
}

async function confirmPayment(transactionId) {
    if (!confirm('Are you sure you want to confirm this payment?')) return;
    
    try {
        const response = await fetch(`/admin/payments/${transactionId}/confirm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                admin_notes: 'Payment confirmed by admin'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Payment confirmed successfully!');
            loadPayments();
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Error confirming payment: ' + error.message);
    }
}

async function rejectPayment(transactionId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (!reason) return;
    
    try {
        const response = await fetch(`/admin/payments/${transactionId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                admin_notes: reason
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Payment rejected successfully!');
            loadPayments();
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Error rejecting payment: ' + error.message);
    }
}

function refreshPayments() {
    loadPayments();
}

function showSuccess(message) {
    showAlert('success', message);
}

function showError(message) {
    showAlert('danger', message);
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>
@endsection
