@extends('layouts.admin')

@section('content')
<div class="main-panel px-4" style="margin-top: 100px; background: transparent;">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Withdrawal Requests</h2>
                    <p class="text-muted mb-0">Manage and process user withdrawal requests</p>
                </div>
                <!-- <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 stat-card stat-card-warning">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pending Requests</p>
                            <h2 class="fw-bold mb-0">{{ $stats['pending'] }}</h2>
                            <small class="text-muted">Awaiting approval</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 stat-card stat-card-info">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Approved</p>
                            <h2 class="fw-bold mb-0">{{ $stats['approved'] }}</h2>
                            <small class="text-muted">Ready to transfer</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="fas fa-check-circle text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 stat-card stat-card-success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Completed</p>
                            <h2 class="fw-bold mb-0">{{ $stats['completed'] }}</h2>
                            <small class="text-muted">Successfully processed</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i class="fas fa-check-double text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 stat-card stat-card-danger">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Rejected</p>
                            <h2 class="fw-bold mb-0">{{ $stats['rejected'] }}</h2>
                            <small class="text-muted">Declined requests</small>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-transparent">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">All Withdrawal Requests</h5>
                        <div class="d-flex gap-2">
                            <!-- <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Search requests...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 bg-transparent">
                    @if($withdrawals->count() > 0)
                    <div class="table-responsive bg-transparent">
                        <table class="table table-hover align-middle mb-0 modern-table" style="background: transparent;">
                            <thead class="bg-transparent">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Amount</th>
                                    <th class="py-3">Wallet Address</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Request Date</th>
                                    <th class="py-3 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                <tr class="withdrawal-row">
                                    <td class="px-4">
                                        <span class="badge text-white fw-semibold">#{{ $withdrawal->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $withdrawal->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $withdrawal->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-white">${{ number_format($withdrawal->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="wallet-address">
                                            <code>{{ substr($withdrawal->wallet_address, 0, 10) }}...{{ substr($withdrawal->wallet_address, -8) }}</code>
                                            <button class="btn btn-sm btn-link p-0 ms-1" onclick="copyToClipboard('{{ $withdrawal->wallet_address }}')" title="Copy address">
                                                <i class="fas fa-copy text-muted"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge  bg-opacity-10 text-secondary" >{{ ucfirst($withdrawal->wallet_type) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge status-{{ $withdrawal->status_badge }}">
                                            {{ $withdrawal->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-white small">{{ $withdrawal->created_at->format('M d, Y') }}</div>
                                        <div class="text-white" style="font-size: 0.75rem;">{{ $withdrawal->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-end pe-4">
                                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}"
                                                class="btn btn-sm btn-light" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($withdrawal->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="approveWithdrawal({{ $withdrawal->id }})" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="rejectWithdrawal({{ $withdrawal->id }})" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @elseif($withdrawal->status === 'approved')
                                            <button type="button" class="btn btn-sm btn-warning text-dark"
                                                onclick="transferFunds({{ $withdrawal->id }}, '{{ $withdrawal->wallet_address }}', {{ $withdrawal->amount }})"
                                                title="Connect wallet to transfer">
                                                <i class="fas fa-exchange-alt"></i> Transfer
                                            </button>
                                            @elseif($withdrawal->status === 'completed')
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i>Completed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-transparent border-0 py-3">
                        <div class="d-flex justify-content-center">
                            {{ $withdrawals->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5 class="text-muted fw-semibold">No withdrawal requests found</h5>
                        <p class="text-muted small">Withdrawal requests will appear here once submitted</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Approve Withdrawal</h5>
                    <p class="text-muted small mb-0">Review and approve this withdrawal request</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="approveForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Admin Notes <span class="text-muted">(Optional)</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3"
                            placeholder="Add any notes for this approval..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitApproval()">
                    <i class="fas fa-check me-2"></i>Approve Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modern Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Reject Withdrawal</h5>
                    <p class="text-muted small mb-0">Provide a reason for rejection</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="rejectForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" required
                            placeholder="Please provide a clear reason for rejection..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-times me-2"></i>Reject Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modern Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">Mark as Completed</h5>
                    <p class="text-muted small mb-0">Enter transaction hash to complete</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="completeForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Hash <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="transaction_hash" required
                            placeholder="0x...">
                        <small class="text-muted">Hash of the blockchain transaction from admin to user wallet</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="submitCompletion()">
                    <i class="fas fa-check-double me-2"></i>Mark Complete
                </button>
            </div>
        </div>
    </div>
</div>

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
        --fg-accent-2: #20c9bb;
        --fg-warning: #f5c84b;
        --fg-danger: #ff6b6b;
        --fg-success: #22e3a0;
        --fg-info: #20c9bb;
        --fg-hover: rgba(34, 227, 160, .08);
        --fg-shadow: 0 10px 30px rgba(0, 0, 0, .35);
    }


    /* Page background & text */
    body {
        background:
            radial-gradient(1200px 600px at 10% -10%, rgba(34, 227, 160, .08), transparent 60%),
            radial-gradient(900px 500px at 110% 10%, rgba(32, 201, 187, .10), transparent 60%),
            var(--fg-bg) !important;
        color: var(--fg-text);
    }

    h1,
    h2,
    h3,
    h4,
    h5 {
        color: var(--fg-text);
    }

    .text-muted {
        color: var(--fg-muted) !important;
    }

    /* Cards */
    .card {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2)) !important;
        border: 1px solid var(--fg-border) !important;
        box-shadow: var(--fg-shadow) !important;
        color: var(--fg-text);
        border-radius: 14px;
    }

    .card-header {
        border-bottom: 1px solid var(--fg-border) !important;
    }

    /* Stat icon chips */
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: linear-gradient(135deg, rgba(34, 227, 160, .12), rgba(32, 201, 187, .12)) !important;
        border: 1px solid rgba(34, 227, 160, .25);
    }

    .stat-card-warning .stat-icon {
        background: rgba(245, 200, 75, .12) !important;
        border-color: rgba(245, 200, 75, .25);
    }

    .stat-card-info .stat-icon {
        background: rgba(32, 201, 187, .12) !important;
        border-color: rgba(32, 201, 187, .25);
    }

    .stat-card-success .stat-icon {
        background: rgba(34, 227, 160, .12) !important;
        border-color: rgba(34, 227, 160, .25);
    }

    .stat-card-danger .stat-icon {
        background: rgba(255, 107, 107, .12) !important;
        border-color: rgba(255, 107, 107, .25);
    }

    /* Inputs & groups */
    .form-control,
    .form-select {
        background: rgba(255, 255, 255, .04);
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
        border-radius: 10px;
    }

    .form-control::placeholder {
        color: #8ab5b0;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--fg-accent);
        box-shadow: 0 0 0 .2rem rgba(34, 227, 160, .15);
        background: rgba(255, 255, 255, .06);
    }

    .input-group .btn {
        border-color: var(--fg-border);
    }

    /* Buttons */
    .btn {
        border-radius: 10px;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-light {
        background: rgba(255, 255, 255, .06);
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
    }

    .btn-light:hover {
        background: rgba(255, 255, 255, .10);
    }

    .btn-outline-secondary {
        color: var(--fg-text);
        border-color: var(--fg-border);
        background: transparent;
    }

    .btn-outline-secondary:hover {
        background: rgba(255, 255, 255, .06);
        border-color: var(--fg-accent);
    }

    .btn-success {
        background: linear-gradient(135deg, #17d89a, #22e3a0);
        border: 0;
        color: #05241d;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ff6b6b, #ff8b8b);
        border: 0;
        color: #2b0b0b;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f5c84b, #ffd76a);
        border: 0;
        color: #3a2a00;
    }

    .btn-info {
        background: linear-gradient(135deg, #20c9bb, #66e0d6);
        border: 0;
        color: #012b28;
    }

    /* Table */
    .table {
        color: var(--fg-text);
    }

    .modern-table thead th {
        font-weight: 600;
        font-size: .85rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid var(--fg-border);
        color: var(--fg-muted);
    }

    .modern-table tbody tr {
        border-bottom: 1px solid var(--fg-border);
    }

    .modern-table tbody tr:hover {
        background: var(--fg-hover) !important;
    }

    .withdrawal-row td {
        padding: 1rem .75rem;
        vertical-align: middle;
    }

    /* Avatar */
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: radial-gradient(70% 70% at 30% 30%, var(--fg-accent), var(--fg-accent-2)) !important;
        color: #04231c;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 12px rgba(0, 0, 0, .25);
    }

    /* Wallet chip */
    .wallet-address code {
        background: rgba(255, 255, 255, .03) !important;
        border: 1px solid var(--fg-border) !important;
        color: var(--fg-text) !important;
        border-radius: 8px;
        padding: 4px 8px;
        font-size: .85rem;
    }

    /* Status badges */
    .status-badge {
        padding: 6px 12px;
        font-weight: 600;
        font-size: .8rem;
        border-radius: 999px;
        border: 1px solid transparent;
    }

    .status-warning {
        background: rgba(245, 200, 75, .12);
        color: #f5d26b;
        border-color: rgba(245, 200, 75, .25);
    }

    .status-info {
        background: rgba(32, 201, 187, .12);
        color: #66e0d6;
        border-color: rgba(32, 201, 187, .25);
    }

    .status-success {
        background: rgba(34, 227, 160, .12);
        color: #88f0cc;
        border-color: rgba(34, 227, 160, .25);
    }

    .status-danger {
        background: rgba(255, 107, 107, .12);
        color: #ff9d9d;
        border-color: rgba(255, 107, 107, .25);
    }

    /* Empty state */
    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        background: rgba(255, 255, 255, .04);
        color: #89bdb6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        border: 1px solid var(--fg-border);
    }

    /* Modals */
    .modal-content {
        background: linear-gradient(180deg, var(--fg-surface), var(--fg-surface-2));
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
        border-radius: 14px;
    }

    .modal-header,
    .modal-footer {
        border-color: var(--fg-border) !important;
    }

    /* Toast (copy address) */
    .toast .toast-body {
        background: linear-gradient(135deg, #17d89a, #22e3a0) !important;
        color: #05241d !important;
        border-radius: 10px;
    }

    /* Pagination (Laravel links) */
    .page-link {
        background: rgba(255, 255, 255, .04);
        color: var(--fg-text);
        border: 1px solid var(--fg-border);
    }

    .page-link:hover {
        background: rgba(255, 255, 255, .08);
        color: var(--fg-accent);
        border-color: var(--fg-accent);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #17d89a, #22e3a0);
        color: #05241d;
        border-color: transparent;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .modern-table {
            font-size: .85rem;
        }

        .btn-group {
            flex-direction: column;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
<script src="{{ asset('js/blockchain-transfer.js') }}"></script>
<script>
    let currentWithdrawalId = null;

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Address copied to clipboard!
                </div>
            </div>
        `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }

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

    async function transferFunds(withdrawalId, toAddress, amount) {
        try {
            if (!window.ethereum) throw new Error('Please install MetaMask or Trust Wallet to continue.');

            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            if (accounts.length === 0) throw new Error('No wallet accounts found. Please connect your wallet.');

            const adminAddress = accounts[0];
            showTransferProgress('Connecting to wallet...');

            const web3 = new Web3(window.ethereum);
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
            const usdtAbi = [{
                "constant": false,
                "inputs": [{
                    "name": "_to",
                    "type": "address"
                }, {
                    "name": "_value",
                    "type": "uint256"
                }],
                "name": "transfer",
                "outputs": [{
                    "name": "",
                    "type": "bool"
                }],
                "type": "function"
            }];

            const usdtContract = new web3.eth.Contract(usdtAbi, usdtContractAddress);
            const amountWei = web3.utils.toWei(amount.toString(), 'ether');

            showTransferProgress('Preparing transfer...');

            const transferResult = await usdtContract.methods.transfer(toAddress, amountWei).send({
                from: adminAddress,
                gas: 100000
            });

            if (transferResult.status) {
                showTransferProgress('Transfer successful! Updating status...');
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
            if (!result.success) throw new Error(result.message);
        } catch (error) {
            console.error('Failed to update withdrawal status:', error);
            throw error;
        }
    }

    function showTransferProgress(message) {
        let progressModal = document.getElementById('transferProgressModal');
        if (!progressModal) {
            progressModal = document.createElement('div');
            progressModal.id = 'transferProgressModal';
            progressModal.className = 'modal fade';
            progressModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="fw-bold mb-2">Transfer in Progress</h5>
                        <p class="text-muted mb-0" id="transferProgressMessage">${message}</p>
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

    function hideTransferProgress() {
        const progressModal = document.getElementById('transferProgressModal');
        if (progressModal) {
            const modal = bootstrap.Modal.getInstance(progressModal);
            if (modal) modal.hide();
        }
    }

    function submitApproval() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page.');
            return;
        }

        const formData = new FormData();
        formData.append('admin_notes', document.querySelector('#approveForm textarea').value);
        formData.append('_token', csrfToken.getAttribute('content'));

        fetch(`/admin/withdrawals/${currentWithdrawalId}/approve`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    alert('Withdrawal approved successfully! Please click "Transfer" to send funds from your wallet.');
                    location.reload();
                } else {
                    alert('Error: ' + d.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                var m = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                if (m) m.hide();
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
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    alert('Withdrawal rejected successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + d.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                var m = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                if (m) m.hide();
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
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    alert('Withdrawal marked as completed successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + d.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                var m = bootstrap.Modal.getInstance(document.getElementById('completeModal'));
                if (m) m.hide();
            });
    }
</script>
@endsection