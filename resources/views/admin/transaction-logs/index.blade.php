@extends('layouts.admin')

@section('title', 'Transaction Logs - BSCScan')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="fas fa-list-alt me-2"></i>Transaction Logs - BSCScan
                        </h4>
                        <p class="card-description">
                            Real-time transaction logs from BSCScan for admin wallet
                        </p>

                        <!-- Admin Wallet Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-wallet me-2"></i>Admin Wallet Address
                                        </h6>
                                        <p class="card-text" id="adminWalletAddress">
                                            <code>0x42B289F5cc30A2BAcD86CF57eE03b3FB94884E53</code>
                                        </p>
                                        <button class="btn btn-light btn-sm" onclick="copyAdminAddress()">
                                            <i class="fas fa-copy me-1"></i>Copy Address
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-line me-2"></i>Total Transactions
                                        </h6>
                                        <p class="card-text" id="totalTransactions">
                                            <strong>3</strong> transactions
                                        </p>
                                        <button class="btn btn-light btn-sm" onclick="refreshData()">
                                            <i class="fas fa-sync-alt me-1"></i>Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Transaction Type</label>
                                    <select class="form-control" id="transactionType">
                                        <option value="all">All Transactions</option>
                                        <option value="usdt">USDT BEP20</option>
                                        <option value="bnb">BNB</option>
                                        <option value="other">Other Tokens</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="transactionStatus">
                                        <option value="all">All Status</option>
                                        <option value="success">Success</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" id="fromDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" id="toDate">
                                </div>
                            </div>
                        </div>

                        <!-- Search and Actions -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" class="form-control" id="searchInput" 
                                           placeholder="Search by transaction hash, from address, or to address">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="searchTransactions()">
                                            <i class="fas fa-search me-1"></i>Search
                                        </button>
                                        <button class="btn btn-secondary" onclick="clearFilters()">
                                            <i class="fas fa-times me-1"></i>Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Refresh Toggle -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="btn btn-success" onclick="refreshTransactions()">
                                            <i class="fas fa-sync-alt me-1"></i>Refresh from BSCScan
                                        </button>
                                        <button class="btn btn-info" onclick="testBSCScanAPI()">
                                            <i class="fas fa-vial me-1"></i>Test BSCScan API
                                        </button>
                                    </div>
                                    <div class="text-muted">
                                        <small>Last updated: <span id="lastUpdated">Never</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Transaction Hash</th>
                                        <th>From Address</th>
                                        <th>To Address</th>
                                        <th>Amount</th>
                                        <th>Token</th>
                                        <th>Status</th>
                                        <th>Block Number</th>
                                        <th>Date/Time</th>
                                        <th>Gas Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="dataTables_info" id="transactionInfo">
                                    Showing 0 to 0 of 0 entries
                                </div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Transaction pagination">
                                    <ul class="pagination justify-content-end" id="transactionPagination">
                                        <!-- Pagination will be generated here -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionDetailsBody">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="viewOnBSCScan()">View on BSCScan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Debug: Check if functions are defined
console.log('Script loading...');
console.log('refreshTransactions defined:', typeof refreshTransactions);
console.log('testBSCScanAPI defined:', typeof testBSCScanAPI);
let currentPage = 1;
let totalPages = 1;
let autoRefreshInterval;
let currentTransactionHash = '';

// Global functions - accessible from onclick handlers
function refreshTransactions() {
    // Show loading state
    const refreshBtn = event.target;
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    $.ajax({
        url: '{{ route("admin.transaction.refresh") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showSuccess('Transactions refreshed successfully! ' + response.total_transactions + ' total transactions.');
                loadTransactions(1); // Reload first page
                updateLastUpdated();
            } else {
                showError('Failed to refresh transactions: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            showError('Error refreshing transactions: ' + error);
        },
        complete: function() {
            // Restore button state
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        }
    });
}

function testBSCScanAPI() {
    const testBtn = event.target;
    const originalText = testBtn.innerHTML;
    testBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Testing...';
    testBtn.disabled = true;
    
    $.ajax({
        url: '{{ route("admin.bscscan.test") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                showSuccess(`BSCScan API Test Successful!<br>
                    API Key: ${response.api_key}<br>
                    Wallet: ${response.wallet_address}<br>
                    Balance: ${response.balance_bnb} BNB<br>
                    Status: ${response.response_status}`);
            } else {
                showError('BSCScan API Test Failed: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            showError('Error testing BSCScan API: ' + error);
        },
        complete: function() {
            testBtn.innerHTML = originalText;
            testBtn.disabled = false;
        }
    });
}

function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = now.toLocaleString();
}

function showSuccess(message) {
    // Create a success alert
    const alert = $(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    // Insert at the top of the content
    $('.content-wrapper').prepend(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}

function showError(message) {
    // Create an error alert
    const alert = $(`
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    // Insert at the top of the content
    $('.content-wrapper').prepend(alert);
    
    // Auto remove after 8 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 8000);
}

$(document).ready(function() {
    console.log('Document ready - starting initialization');
    loadAdminWalletAddress();
    loadTransactions(); // Load from database
    setupEventListeners();
});

function setupEventListeners() {
    // Search on Enter key
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchTransactions();
        }
    });

    // Filter change events
    $('#transactionType, #transactionStatus, #fromDate, #toDate').on('change', function() {
        searchTransactions();
    });

    // Auto refresh toggle
    // Auto refresh removed - using database instead
}

// Auto refresh removed - using database instead

function loadAdminWalletAddress() {
    $.ajax({
        url: '{{ route("admin.wallet.address") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#adminWalletAddress').html(`
                    <code>${response.admin_wallet_address}</code>
                    <br><small class="text-light">${response.is_live ? 'Live Address' : 'Fallback Address'}</small>
                `);
            }
        },
        error: function() {
            $('#adminWalletAddress').html('<span class="text-warning">Failed to load</span>');
        }
    });
}

function loadTransactions(page = 1) {
    currentPage = page;
    
    console.log('Loading transactions, page:', page);
    
    // Show loading indicator
    $('#transactionTableBody').html(`
        <tr>
            <td colspan="11" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <br><small class="text-muted">Loading transactions...</small>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: '{{ route("admin.transaction.data") }}',
        method: 'GET',
        data: {
            page: page,
            type: $('#transactionType').val(),
            status: $('#transactionStatus').val(),
            from_date: $('#fromDate').val(),
            to_date: $('#toDate').val(),
            search: $('#searchInput').val()
        },
        success: function(response) {
            console.log('API Response:', response);
            if (response.success && response.transactions) {
                displayTransactions(response.transactions);
                updatePagination(response.pagination);
                updateTransactionInfo(response.pagination);
            } else {
                console.log('No transactions found or API error');
                displayTransactions([]);
                updatePagination({current_page: 1, last_page: 1, per_page: 10, total: 0});
                updateTransactionInfo({total: 0});
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr, status, error);
            console.error('Response Text:', xhr.responseText);
            showError('Error loading transactions: ' + error);
            displayTransactions([]);
        }
    });
}

function showMockData() {
    console.log('showMockData function called');
    
    const mockTransactions = [
        {
            hash: '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef',
            from: '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            to: '0x55d398326f99059fF775485246999027B3197955',
            amount: '6.250000',
            token_symbol: 'USDT',
            status: 'success',
            blockNumber: 19088743,
            formatted_date: '2025-09-24 22:04:44',
            gasUsed: 30000
        },
        {
            hash: '0xabcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890',
            from: '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            to: '0x55d398326f99059fF775485246999027B3197955',
            amount: '2.000000',
            token_symbol: 'USDT',
            status: 'success',
            blockNumber: 19088744,
            formatted_date: '2025-09-24 21:04:44',
            gasUsed: 30000
        },
        {
            hash: '0x9876543210fedcba9876543210fedcba9876543210fedcba9876543210fedcba',
            from: '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
            to: '0x0000000000000000000000000000000000000000',
            amount: '0.001000',
            token_symbol: 'BNB',
            status: 'success',
            blockNumber: 19088745,
            formatted_date: '2025-09-24 20:04:44',
            gasUsed: 21000
        }
    ];
    
    console.log('Mock transactions:', mockTransactions);
    
    displayTransactions(mockTransactions);
    updatePagination({
        current_page: 1,
        per_page: 20,
        total: 3,
        last_page: 1
    });
    updateTransactionInfo({
        current_page: 1,
        per_page: 20,
        total: 3,
        last_page: 1
    });
    
    console.log('Mock data display completed');
}

function displayTransactions(transactions) {
    const tbody = $('#transactionTableBody');
    
    console.log('Displaying transactions:', transactions);
    
    if (!transactions || transactions.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="11" class="text-center text-muted">
                    <i class="fas fa-inbox me-2"></i>No transactions found
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    transactions.forEach((tx, index) => {
        const statusClass = getStatusClass(tx.status);
        const tokenInfo = getTokenInfo(tx.token_symbol, tx.token_address);
        const dateTime = formatDateTime(tx.formatted_date || tx.timestamp);
        
        html += `
            <tr>
                <td>${(currentPage - 1) * 20 + index + 1}</td>
                <td>
                    <code class="text-primary" style="cursor: pointer;" onclick="viewTransactionDetails('${tx.tx_hash || tx.hash}')">
                        ${(tx.tx_hash || tx.hash).substring(0, 10)}...
                    </code>
                </td>
                <td>
                    <code class="text-info">${(tx.from_address || tx.from).substring(0, 10)}...</code>
                </td>
                <td>
                    <code class="text-success">${(tx.to_address || tx.to).substring(0, 10)}...</code>
                </td>
                <td class="text-right">${tx.amount || formatAmount(tx.value, tx.token_decimals)}</td>
                <td>
                    <span class="badge badge-${tokenInfo.badgeClass}">
                        <i class="${tokenInfo.icon} me-1"></i>${tx.token_symbol || 'Unknown'}
                    </span>
                </td>
                <td>
                    <span class="badge badge-${statusClass}">${tx.status}</span>
                </td>
                <td>${tx.block_number || tx.blockNumber}</td>
                <td>${tx.formatted_date || dateTime}</td>
                <td>${tx.gas_used || tx.gasUsed || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewTransactionDetails('${tx.tx_hash || tx.hash}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="viewOnBSCScan('${tx.tx_hash || tx.hash}')">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
}

function viewTransactionDetails(txHash) {
    currentTransactionHash = txHash;
    
    $.ajax({
        url: '{{ route("admin.transaction.details") }}',
        method: 'GET',
        data: { hash: txHash },
        success: function(response) {
            if (response.success) {
                displayTransactionDetails(response.transaction);
                $('#transactionDetailsModal').modal('show');
            } else {
                showError('Failed to load transaction details');
            }
        },
        error: function() {
            showError('Error loading transaction details');
        }
    });
}

function displayTransactionDetails(transaction) {
    const details = `
        <div class="row">
            <div class="col-md-6">
                <h6>Transaction Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Hash:</strong></td><td><code>${transaction.hash}</code></td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge badge-${getStatusClass(transaction.status)}">${transaction.status}</span></td></tr>
                    <tr><td><strong>Block Number:</strong></td><td>${transaction.blockNumber}</td></tr>
                    <tr><td><strong>Gas Used:</strong></td><td>${transaction.gasUsed || 'N/A'}</td></tr>
                    <tr><td><strong>Gas Price:</strong></td><td>${transaction.gasPrice || 'N/A'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Address Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>From:</strong></td><td><code>${transaction.from}</code></td></tr>
                    <tr><td><strong>To:</strong></td><td><code>${transaction.to}</code></td></tr>
                    <tr><td><strong>Value:</strong></td><td>${formatAmount(transaction.value, transaction.token_decimals)} ${transaction.token_symbol || 'Unknown'}</td></tr>
                    <tr><td><strong>Timestamp:</strong></td><td>${formatDateTime(transaction.timestamp)}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#transactionDetailsBody').html(details);
}

function viewOnBSCScan(txHash = null) {
    const hash = txHash || currentTransactionHash;
    if (hash) {
        window.open(`https://bscscan.com/tx/${hash}`, '_blank');
    }
}

function searchTransactions() {
    currentPage = 1;
    loadTransactions();
}

function clearFilters() {
    $('#transactionType').val('all');
    $('#transactionStatus').val('all');
    $('#fromDate').val('');
    $('#toDate').val('');
    $('#searchInput').val('');
    searchTransactions();
}

function refreshData() {
    loadAdminWalletAddress();
    loadTransactions();
}

function updatePagination(pagination) {
    totalPages = pagination.last_page;
    const paginationHtml = generatePaginationHtml(pagination);
    $('#transactionPagination').html(paginationHtml);
}

function generatePaginationHtml(pagination) {
    let html = '';
    
    // Previous button
    html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadTransactions(${pagination.current_page - 1})">Previous</a>
    </li>`;
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadTransactions(${i})">${i}</a>
        </li>`;
    }
    
    // Next button
    html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadTransactions(${pagination.current_page + 1})">Next</a>
    </li>`;
    
    return html;
}

function updateTransactionInfo(pagination) {
    const start = (pagination.current_page - 1) * pagination.per_page + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    
    $('#transactionInfo').text(`Showing ${start} to ${end} of ${pagination.total} entries`);
}

// Utility functions
function getStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'success': return 'success';
        case 'pending': return 'warning';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
}

function getTokenInfo(symbol, address) {
    if (symbol === 'USDT' || address === '0x55d398326f99059fF775485246999027B3197955') {
        return { badgeClass: 'primary', icon: 'fas fa-dollar-sign' };
    } else if (symbol === 'BNB' || address === '0x0000000000000000000000000000000000000000') {
        return { badgeClass: 'warning', icon: 'fas fa-coins' };
    } else {
        return { badgeClass: 'info', icon: 'fas fa-token' };
    }
}

function showError(message) {
    // You can implement a toast notification here
    console.error(message);
    alert(message);
}

function formatAmount(value, decimals = 18) {
    const amount = parseInt(value, 16) / Math.pow(10, decimals);
    return amount.toFixed(6);
}

function formatDateTime(timestamp) {
    const date = new Date(timestamp * 1000);
    return date.toLocaleString();
}

// Cleanup on page unload
$(window).on('beforeunload', function() {
    clearInterval(autoRefreshInterval);
});

// Debug: Final check
console.log('Script loaded completely');
console.log('refreshTransactions defined at end:', typeof refreshTransactions);
console.log('testBSCScanAPI defined at end:', typeof testBSCScanAPI);

// Make functions globally available as backup
window.refreshTransactions = refreshTransactions;
window.testBSCScanAPI = testBSCScanAPI;
</script>
@endsection
