@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Withdrawal Requests</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                            <p class="mb-0">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                            <p class="mb-0">Approved</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                            <p class="mb-0">Completed</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['rejected'] }}</h4>
                            <p class="mb-0">Rejected</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Withdrawal Requests</h5>
                </div>
                <div class="card-body">
                    @if($withdrawals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Wallet Address</th>
                                        <th>Wallet Type</th>
                                        <th>Status</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr>
                                            <td><strong>#{{ $withdrawal->id }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $withdrawal->user->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $withdrawal->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong>${{ number_format($withdrawal->amount, 2) }}</strong></td>
                                            <td>
                                                <code class="text-muted">{{ substr($withdrawal->wallet_address, 0, 10) }}...{{ substr($withdrawal->wallet_address, -8) }}</code>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ ucfirst($withdrawal->wallet_type) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $withdrawal->status_badge }}">
                                                    {{ $withdrawal->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $withdrawal->created_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    
                                                    @if($withdrawal->status === 'pending')
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="approveWithdrawal({{ $withdrawal->id }})">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectWithdrawal({{ $withdrawal->id }})">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    @elseif($withdrawal->status === 'approved')
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="transferFunds({{ $withdrawal->id }}, '{{ $withdrawal->wallet_address }}', {{ $withdrawal->amount }})"
                                                                title="Connect your MetaMask/Trust Wallet to transfer USDT">
                                                            <i class="fas fa-exchange-alt"></i> Transfer USDT
                                                        </button>
                                                    @elseif($withdrawal->status === 'completed')
                                                        <span class="badge badge-success">Transfer Completed</span>
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
                            {{ $withdrawals->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No withdrawal requests found</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="approveForm">
                    <div class="form-group">
                        <label>Admin Notes (Optional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" 
                                  placeholder="Add any notes for this approval..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitApproval()">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="form-group">
                        <label>Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" required
                                  placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="completeForm">
                    <div class="form-group">
                        <label>Transaction Hash <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="transaction_hash" required
                               placeholder="Enter the blockchain transaction hash...">
                        <small class="text-muted">This should be the hash of the transaction from admin wallet to user wallet.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="submitCompletion()">Mark Complete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
<script src="{{ asset('js/blockchain-transfer.js') }}"></script>
<script>
let currentWithdrawalId = null;

function approveWithdrawal(id) {
    currentWithdrawalId = id;
    var modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectWithdrawal(id) {
    currentWithdrawalId = id;
    var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function completeWithdrawal(id) {
    currentWithdrawalId = id;
    var modal = new bootstrap.Modal(document.getElementById('completeModal'));
    modal.show();
}

/**
 * Transfer funds from admin wallet to user wallet
 */
async function transferFunds(withdrawalId, toAddress, amount) {
    try {
        // Check if Web3 is available
        if (!window.ethereum) {
            throw new Error('Please install MetaMask or Trust Wallet to continue.');
        }

        // Request account access
        const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
        if (accounts.length === 0) {
            throw new Error('No wallet accounts found. Please connect your wallet.');
        }

        const adminAddress = accounts[0];
        
        // Show transfer progress
        showTransferProgress('Connecting to wallet...');

        // Initialize Web3
        const web3 = new Web3(window.ethereum);
        
        // USDT contract address and ABI
        const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
        const usdtAbi = [
            {
                "constant": false,
                "inputs": [
                    {"name": "_to", "type": "address"},
                    {"name": "_value", "type": "uint256"}
                ],
                "name": "transfer",
                "outputs": [{"name": "", "type": "bool"}],
                "type": "function"
            }
        ];

        const usdtContract = new web3.eth.Contract(usdtAbi, usdtContractAddress);
        
        // Convert amount to wei
        const amountWei = web3.utils.toWei(amount.toString(), 'ether');
        
        showTransferProgress('Preparing transfer...');

        // Perform the transfer
        const transferResult = await usdtContract.methods.transfer(toAddress, amountWei).send({
            from: adminAddress,
            gas: 100000
        });

        if (transferResult.status) {
            showTransferProgress('Transfer successful! Updating status...');
            
            // Update withdrawal status to completed
            await updateWithdrawalStatus(withdrawalId, transferResult.transactionHash);
            
            alert(`Transfer completed successfully!\nTransaction Hash: ${transferResult.transactionHash}`);
            location.reload();
        } else {
            throw new Error('Transaction failed');
        }

    } catch (error) {
        console.error('Transfer failed:', error);
        alert('Transfer failed: ' + error.message);
        hideTransferProgress();
    }
}

/**
 * Update withdrawal status after successful transfer
 */
async function updateWithdrawalStatus(withdrawalId, transactionHash) {
    try {
        const response = await fetch(`/admin/withdrawals/${withdrawalId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                transaction_hash: transactionHash
            })
        });

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Failed to update withdrawal status:', error);
        throw error;
    }
}

/**
 * Show transfer progress
 */
function showTransferProgress(message) {
    // Create or update progress modal
    let progressModal = document.getElementById('transferProgressModal');
    if (!progressModal) {
        progressModal = document.createElement('div');
        progressModal.id = 'transferProgressModal';
        progressModal.className = 'modal fade';
        progressModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer in Progress</h5>
                    </div>
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3" id="transferProgressMessage">${message}</p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(progressModal);
    }
    
    document.getElementById('transferProgressMessage').textContent = message;
    const modal = new bootstrap.Modal(progressModal);
    modal.show();
}

/**
 * Hide transfer progress
 */
function hideTransferProgress() {
    const progressModal = document.getElementById('transferProgressModal');
    if (progressModal) {
        const modal = bootstrap.Modal.getInstance(progressModal);
        if (modal) {
            modal.hide();
        }
    }
}

function submitApproval() {
    console.log('Submitting approval for withdrawal ID:', currentWithdrawalId);
    
    // Get CSRF token safely
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }
    
    const formData = new FormData();
    formData.append('admin_notes', document.querySelector('#approveForm textarea').value);
    formData.append('_token', csrfToken.getAttribute('content'));
    
    console.log('Form data:', formData);
    
    fetch(`/admin/withdrawals/${currentWithdrawalId}/approve`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Withdrawal approved successfully! Please click "Transfer USDT" to send funds from your wallet.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
        if (modal) modal.hide();
    });
}

function submitRejection() {
    const formData = new FormData();
    formData.append('admin_notes', document.querySelector('#rejectForm textarea').value);
    
    fetch(`/admin/withdrawals/${currentWithdrawalId}/reject`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Withdrawal rejected successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
        if (modal) modal.hide();
    });
}

function submitCompletion() {
    const formData = new FormData();
    formData.append('transaction_hash', document.querySelector('#completeForm input').value);
    
    fetch(`/admin/withdrawals/${currentWithdrawalId}/complete`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Withdrawal marked as completed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('completeModal'));
        if (modal) modal.hide();
    });
}
</script>
@endsection
