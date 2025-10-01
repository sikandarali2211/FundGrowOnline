@extends('layouts.user')
@section('content')
<style>
    /* Global dark gradient background */
    body,
    .content-wrapper,
    .main-panel,
    .page-header,
    footer.footer {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #fff;
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

    .clickable-card {
        background: transparent;
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        color: #fff;
    }

    .clickable-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 0 15px rgba(0, 255, 200, 0.4),
            0 0 30px rgba(0, 255, 200, 0.2);
        cursor: pointer;
    }

    .clickable-card i {
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .clickable-card:hover i {
        transform: scale(1.2) rotate(5deg);
        color: #00ffd0 !important;
        /* neon glow icon */
    }

    .clickable-card h6,
    .clickable-card span {
        transition: color 0.3s ease;
    }

    .clickable-card:hover h6,
    .clickable-card:hover span {
        color: #00ffd0;
    }


    /* Cards */
    .card {
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 15px;
        border: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        color: #fff;
    }

    .card.card-statistics {
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 15px;
        border: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        color: #fff;
    }

    /* Inner glass effect */
    .card-body {
        background: linear-gradient(145deg, #072d42, #22384e);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        color: #e1e1e1;
    }

    /* Headings */
    h3.page-title,
    .card-title {
        color: #3bd17a;
        /* green accent */
        font-weight: 600;
    }

    /* Statistics items */
    .statistics-item p {
        color: #9ec3d8;
        font-size: 0.9rem;
    }

    .statistics-item h2 {
        font-size: 1.8rem;
        font-weight: bold;
    }

    /* Tables */
    .table {
        color: #ddd;
        background: transparent;
    }

    .table thead th {
        background: rgba(255, 255, 255, 0.1);
        color: #f1f1f1;
    }

    .table tbody tr {
        transition: background 0.3s ease;
    }

    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    /* Badges */
    .badge {
        border-radius: 30px;
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }

    /* Buttons */
    .btn-primary {
        background: #3bd17a;
        border: none;
    }

    .btn-outline-dark {
        border-color: #3bd17a;
        color: #3bd17a;
    }

    .btn-outline-dark:hover {
        background: #3bd17a;
        color: #072d42;
    }

    /* Footer */
    .footer {
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        padding: 1rem 0;
        text-align: center;
        font-size: 0.85rem;
        color: #ddd;
    }

    /* Scrollbar styling (optional) */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-thumb {
        background: #3bd17a;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #072d42;
    }

    .main-card {
        min-height: 20%;
        height: 30px;
    }

    /* Ensure the modal stays centered in the viewport */
    .modal-dialog {
        margin: 0 auto;
        top: -90px;
        /* Center the modal vertically */
        transform: translateY(-50%);
        /* Adjust the modal to be centered */
    }

    /* Modal body: Add scrolling if content is too long */
    .modal-body {
        max-height: calc(100vh - 150px);
        /* Ensure modal is scrollable but doesn't exceed viewport */
        overflow-y: auto;
    }

    /* Remove the margin and padding when modal is open to avoid extra space */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0px !important;
        margin-right: 0px !important;
    }

    .modal {
        overflow-y: auto !important;
        /* Make the modal content scrollable if it's too long */
    }

    .modal-open .modal {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        /* Ensure vertical scrolling when content exceeds screen height */
    }


    .toast-message {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #3bd17a;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease-in-out;
        z-index: 9999;
    }

    .toast-message.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Trust Wallet Connection Styles */
    #trustWalletStatus .alert {
        border-radius: 10px;
        margin-bottom: 0;
    }

    #adminWalletAddress {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid #3bd17a;
        color: #fff;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }

    #userWalletAddress {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        word-break: break-all;
    }

    .wallet-connection-btn {
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .wallet-connection-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 209, 122, 0.3);
    }

    #transactionStatus .alert {
        border-radius: 10px;
        margin-bottom: 0;
    }
</style>



<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h3 class="page-title mb-0">
                    Dashboard
                </h3>
                <div class="d-flex align-items-center" style="gap: 15px;">
                    <div class="text-end">
                        <div class="text-white font-weight-bold">{{ auth()->user()->name }}</div>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <img src="{{ auth()->user()->profile_picture 
                        ? asset('storage/' . auth()->user()->profile_picture) . '?t=' . time() 
                        : asset('assets/images/default-avatar.png') }}" 
                        alt="Profile" 
                        class="rounded-circle" 
                        style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #3bd17a;"
                        onerror="this.src='{{ asset('assets/images/default-avatar.png') }}'">
                </div>
            </div>
        </div>

        <!-- Referral Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $totalReferrals ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Total Referrals</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $activePlanUsers ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Active Plan Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $pendingPlanUsers ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Pending Plan Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #f0c24b; margin-bottom: 5px;">{{ auth()->user()->level ?? 1 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Your Level</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Wallet Connection Status (Full Width) -->
            <div class="col-lg-12" hidden>
                @if (auth()->user()->wallet_address)
                <div class="card mb-3"
                    style="background: linear-gradient(145deg, #072d42, #22384e); border: 1px solid #3bd17a;">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-check-circle text-success fa-2x me-2"></i>
                            <h5 class="mb-0 text-success">Wallet Connected</h5>
                        </div>
                        <div class="wallet-address-display">
                            <small class="text-muted">Connected Address:</small><br>
                            <code class="wallet-address-text text-info"
                                style="font-size: 0.8rem; word-break: break-all;">
                                {{ auth()->user()->wallet_address }}
                            </code>
                            <button class="btn btn-sm btn-outline-info ms-2"
                                onclick="copyWalletAddressToClipboard('{{ auth()->user()->wallet_address }}')"
                                title="Copy Address">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-shield-alt me-1"></i>Your wallet is securely connected
                            </small>
                        </div>
                    </div>
                </div>
                @else
                <div class="card mb-3"
                    style="background: linear-gradient(145deg, #072d42, #22384e); border: 2px solid #3bd17a;">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-exclamation-triangle fa-2x me-2" style="color: #3bd17a"></i>
                            <h5 class="mb-0 " style="color: #3bd17a">Wallet Not Connected</h5>
                        </div>
                        <p class="text-muted mb-3">Connect your crypto wallet to manage your funds</p>
                        <a href="{{ route('user.wallet.index') }}" class="btn btn-warning">
                            <i class="fas fa-link me-2"></i> Connect Wallet
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Left Side Main Balance Card -->
            <div class="col-md-6 col-lg-6">
                <div class="main-card p-4 text-white position-relative d-flex justify-content-between align-items-center"
                    style="background: url('{{ asset('assets/images/bg-balance.png') }}') no-repeat center/cover; border-radius: 15px; min-height: 220px;">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="Logo"
                        style="position: absolute; top: 5px; left: 15px; height: 60px;">
                    <div style="margin-top:70px;">
                        <h5 style="color: #3bd17a;">Total Balance</h5>
                        <h2 style="color: #3bd17a" class="font-weight-bold mb-0">${{ number_format((float)($balanceBreakdown['balance_wallet'] ?? 0), 2) }}</h2>
                        @if(isset($balanceBreakdown))
                        <div style="margin-top: 10px; font-size: 0.8rem;">
                            <div style="color: #28a745;">Sent: ${{ $balanceBreakdown['total_sent'] }}</div>
                            <div style="color: #17a2b8;">Investment: ${{ $balanceBreakdown['total_investment'] }}</div>
                            <div style="color: #ffc107;">Returns: ${{ $balanceBreakdown['total_returns'] }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                            data-toggle="modal" data-target="#topUpModal">
                            <i class="fas fa-arrow-up mr-1"></i> Top Up
                        </button>
                        <!-- <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                                data-toggle="modal" data-target="#sendModal">
                                <i class="fas fa-paper-plane mr-1"></i> Send
                            </button> -->
                        <!-- <button class="btn btn-light btn-sm"  style="border-radius: 20px; min-width: 120px;">
                                <i class="fas fa-arrow-up mr-1"></i> Cash Out
                            </button> -->
                    </div>
                </div>
            </div>

            <!-- Right Side Wallet Cards -->
            <div class="col-md-6 col-lg-6" hidden>
                <!-- Recent Transactions Card -->
                @if(isset($balanceBreakdown) && $balanceBreakdown['recent_transactions']->count() > 0)
                <div class="card mb-3" style="background: linear-gradient(145deg, #072d42, #22384e); border: 1px solid #3bd17a;">
                    <div class="card-header" style="background: transparent; border-bottom: 1px solid #3bd17a;">
                        <h6 class="mb-0 text-success">
                            <i class="fas fa-history me-2"></i>Recent Transactions
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                        @foreach($balanceBreakdown['recent_transactions'] as $tx)
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.85rem;">
                            <div>
                                <span class="text-white">{{ $tx->token_symbol ?? 'USDT' }}</span>
                                <small class="text-muted d-block">{{ $tx->created_at->format('M d, H:i') }}</small>
                            </div>
                            <div class="text-right">
                                <span class="text-success font-weight-bold">${{ number_format($tx->amount, 2) }}</span>
                                <small class="text-muted d-block">Sent</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="text-decoration-none">
                            <div class="card text-center p-3  h-100 clickable-card">
                                <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                                <h6 class="mb-1">Balance Wallet</h6>
                                <span class="font-weight-bold">${{ $balanceBreakdown['balance_wallet'] ?? '0.00' }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="text-decoration-none">
                            <div class="card text-center p-3  h-100 clickable-card">
                                <i class="fas fa-box fa-2x text-success mb-2"></i>
                                <h6 class="mb-1">Pool Wallet</h6>
                                <span class="font-weight-bold" id="mainPoolWallet">${{ $balanceBreakdown['pool_wallet'] ?? '0.00' }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="text-decoration-none">
                            <div class="card text-center p-3  h-100 clickable-card">
                                <i class="fas fa-percentage fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">Pool Commission</h6>
                                <span class="font-weight-bold">${{ $balanceBreakdown['pool_commission'] ?? '0.00' }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="{{ url('exchange') }}" class="text-decoration-none" data-toggle="modal"
                            data-target="#exchangemodal">
                            <div class="card text-center p-3  h-100 clickable-card">
                                <i class="fas fa-exchange-alt fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">Exchange</h6>
                            </div>
                        </a>
                    </div>
                    <!-- <div class="col-sm-6 mb-3" >
                            <a href="{{ url('pools') }}" class="text-decoration-none">
                                <div class="card text-center p-3  h-100 clickable-card">
                                    <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1">Pools</h6>
                                </div>
                            </a>
                        </div> -->
                </div>
            </div>
        </div>
        <!-- Top Up Modal -->
        <div class="modal fade" id="topUpModal" tabindex="-1" role="dialog" aria-labelledby="topUpModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="background: #072d42; color: #fff; border-radius: 15px;">

                    <!-- Header -->
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="topUpModalLabel" style="color:#3bd17a;">Top Up Wallet</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body text-center">
                        <!-- Trust Wallet Connection Status -->
                        <div id="trustWalletStatus" class="mb-4">
                            <div id="walletConnectedStatus" class="d-none">
                                <div class="alert alert-success" style="background: rgba(59, 209, 122, 0.1); border: 1px solid #3bd17a; color: #3bd17a;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Trust Wallet Connected</strong>
                                    <div class="mt-2">
                                        <small>Your wallet: <span id="userWalletAddress" class="text-info"></span></small>
                                    </div>
                                </div>
                            </div>
                            <div id="walletNotConnectedStatus">
                                <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107; color: #ffc107;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Trust Wallet Not Connected</strong>
                                    <div class="mt-2">
                                        <button class="btn btn-warning btn-sm wallet-connection-btn" onclick="connectTrustWallet()">
                                            <i class="fas fa-link me-1"></i> Connect Trust Wallet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Wallet Address Section -->
                        <div class="mb-4">
                            <h6 style="color: #3bd17a;">Send USDT to Admin Wallet</h6>
                            <div class="input-group mb-2">
                                <input type="text" id="adminWalletAddress" class="form-control text-center"
                                    value="{{ $adminWalletAddress ?? '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6' }}" readonly>
                                <div class="input-group-append">
                                    <button id="refreshAdminBtn" class="btn btn-info btn-sm" type="button"
                                        onclick="refreshAdminWalletAddress()" title="Refresh Admin Wallet">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button id="copyAdminBtn" class="btn btn-success" type="button"
                                        onclick="copyAdminWalletAddress()">Copy</button>
                                </div>
                            </div>
                            <small class="text-white">Admin Wallet Address for USDT deposits</small>
                            @if($adminWalletAddress)
                            <div class="mt-1 admin-wallet-status">
                                <small class="text-success" hidden>
                                    <i class="fas fa-check-circle me-1"></i>Live admin wallet address
                                </small>
                            </div>
                            @else
                            <div class="mt-1 admin-wallet-status">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Using fallback address
                                </small>
                            </div>
                            @endif
                        </div>

                        <!-- QR Code -->
                        <!-- <div class="mb-3">
                                <img src="{{ asset('assets/images/qr-sample.png') }}" alt="QR Code" class="img-fluid"
                                    style="max-width: 180px;">
                            </div> -->

                        <!-- Simple Instructions -->
                        <div class="mb-4" hidden>
                            <h6 style="color: #3bd17a;">How to Top Up - Simple Steps</h6>
                            <div class="card" style="background: rgba(255,255,255,0.05); border: 1px solid #3bd17a;">
                                <div class="card-body">
                                    <div class="step-item mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary me-3" style="background: #3bd17a;">1</span>
                                            <div>
                                                <strong>Enter Amount</strong>
                                                <br><small class="text-muted">Enter the USDT amount you want to send in the field below</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-item mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary me-3" style="background: #3bd17a;">2</span>
                                            <div>
                                                <strong>Click "Send USDT BEP20" Button</strong>
                                                <br><small class="text-muted">DApp will automatically approve and send USDT</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary me-3" style="background: #3bd17a;">3</span>
                                            <div>
                                                <strong>Done! Balance Updated</strong>
                                                <br><small class="text-muted">Your balance will be updated automatically after transaction confirmation</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Input Section -->
                        <div class="mb-4">
                            <h6 style="color: #3bd17a;">Transaction Details</h6>
                            <!-- Net desired input (auto adds $1 to Amount to Send) -->
                            <div class="form-group mb-3">
                                <label for="topupNetDesired" class="form-label" style="color: #3bd17a;">I want to receive (Net USD)</label>
                                <input type="number" id="topupNetDesired" class="form-control"
                                    placeholder="0.00" step="0.01" min="0.01"
                                    style="background: rgba(255,255,255,0.1); border: 1px solid #3bd17a; color: #fff;">
                                <small class="form-text text-muted">Type net amount you want credited. We will add $1 fee to the amount you send.</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="topupAmount" class="form-label" style="color: #3bd17a;">Amount to Send (USDT)</label>
                                <input type="number" id="topupAmount" class="form-control"
                                    placeholder="0.00" step="0.01" min="0.01"
                                    style="background: rgba(255,255,255,0.1); border: 1px solid #3bd17a; color: #fff;">
                                <small class="form-text text-muted">Enter the gross amount. $1 fee will be deducted; rest will be credited.</small>
                            </div>

                            <!-- Fee and Net Receive Preview -->
                            <div class="row mb-2">
                                <div class="col-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label" style="color:#3bd17a;">Deposit Fee (Flat)</label>
                                        <input type="text" id="topupFee" class="form-control" value="$1.00" readonly
                                            style="background: rgba(255,255,255,0.08); border: 1px solid #3bd17a; color: #fff;">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label" style="color:#3bd17a;">You Will Receive</label>
                                        <input type="text" id="topupNetReceive" class="form-control" value="$0.00" readonly
                                            style="background: rgba(255,255,255,0.08); border: 1px solid #3bd17a; color: #fff;">
                                        <small class="form-text text-muted">Net credited = Amount to Send − $1</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <button class="btn btn-success btn-block" hidden onclick="sendUSDTDirect()">
                                        <i class="fas fa-paper-plane me-2"></i>Send USDT (Auto)
                                    </button>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary btn-block" onclick="sendUSDTSimple()">
                                        <i class="fas fa-send me-2"></i>Send USDT
                                    </button>
                                </div>
                            </div>
                            <!-- <small class="text-muted d-block text-center">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Try "Send USDT (Simple)" if approve button doesn't work
                                </small> -->
                        </div>

                        <script>
                            (function() {
                                const FEE = 1.00;
                                const amountEl = document.getElementById('topupAmount');
                                const netReceiveEl = document.getElementById('topupNetReceive');
                                const netDesiredEl = document.getElementById('topupNetDesired');

                                function updateNetFromAmount() {
                                    const amt = parseFloat(amountEl && amountEl.value ? amountEl.value : '0') || 0;
                                    const net = Math.max(0, amt - FEE);
                                    if (netReceiveEl) netReceiveEl.value = `$${net.toFixed(2)}`;
                                    if (netDesiredEl && document.activeElement !== netDesiredEl) {
                                        netDesiredEl.value = net > 0 ? net.toFixed(2) : '';
                                    }
                                }

                                function updateAmountFromNet() {
                                    const desired = parseFloat(netDesiredEl && netDesiredEl.value ? netDesiredEl.value : '0') || 0;
                                    const send = desired + FEE;
                                    if (amountEl) amountEl.value = send > 0 ? send.toFixed(2) : '';
                                    updateNetFromAmount();
                                }

                                if (amountEl) {
                                    amountEl.addEventListener('input', updateNetFromAmount);
                                    updateNetFromAmount();
                                }
                                if (netDesiredEl) {
                                    netDesiredEl.addEventListener('input', updateAmountFromNet);
                                }
                            })();
                        </script>

                        <!-- Auto Detection Status -->
                        <div class="mb-4" hidden>
                            <h6 style="color: #3bd17a;">Transaction Status</h6>
                            <div class="card" style="background: rgba(255,255,255,0.05); border: 1px solid #3bd17a;">
                                <div class="card-body text-center">
                                    <div id="autoDetectionStatus">
                                        <i class="fas fa-search fa-2x text-info mb-3"></i>
                                        <h6>Waiting for Transaction</h6>
                                        <p class="text-muted mb-0">Send USDT to admin wallet and we'll automatically detect it</p>
                                    </div>
                                    <div id="detectionProgress" class="d-none">
                                        <div class="spinner-border text-success mb-3" role="status">
                                            <span class="sr-only">Detecting...</span>
                                        </div>
                                        <h6>Detecting Transaction...</h6>
                                        <p class="text-muted mb-0">Please wait while we verify your transaction</p>
                                    </div>
                                    <div id="detectionSuccess" class="d-none">
                                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                        <h6>Transaction Detected!</h6>
                                        <p class="text-muted mb-0">Your balance has been updated successfully</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="p-3 rounded text-warning mt-3" style="background: rgba(255,255,255,0.1);" hidden>
                            <strong>Simple Process:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Enter USDT amount you want to send</li>
                                <li>Click "Send USDT BEP20" button</li>
                                <li>DApp will automatically approve and send USDT</li>
                                <li>Your balance will be updated automatically</li>
                                <li>Make sure you have BNB for gas fees</li>
                            </ul>
                        </div>

                        <!-- Transaction Status -->
                        <div id="transactionStatus" class="d-none">
                            <div class="alert alert-info" style="background: rgba(0, 123, 255, 0.1); border: 1px solid #007bff; color: #007bff;">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                <span id="statusMessage">Processing transaction...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="refreshBalanceBtn" onclick="refreshWalletBalance()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh Balance
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Send Modal -->
        <div class="modal fade" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="sendModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="background: #072d42; color: #fff; border-radius: 15px;">

                    <!-- Header -->
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="sendModalLabel" style="color:#3bd17a;">Send USDT</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="sendForm">
                            @csrf
                            <!-- Recipient Address -->
                            <div class="form-group mb-3">
                                <label for="recipientAddress" class="form-label" style="color: #3bd17a;">Recipient Address</label>
                                <input type="text" class="form-control" id="recipientAddress"
                                    placeholder="Enter recipient wallet address"
                                    style="background: rgba(255,255,255,0.1); border: 1px solid #3bd17a; color: #fff;"
                                    required>
                                <small class="form-text text-muted">Enter the BEP20 wallet address to send USDT to</small>
                            </div>

                            <!-- Amount -->
                            <div class="form-group mb-3">
                                <label for="sendAmount" class="form-label" style="color: #3bd17a;">Amount (USDT)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sendAmount"
                                        placeholder="0.00" step="0.01" min="0.01"
                                        style="background: rgba(255,255,255,0.1); border: 1px solid #3bd17a; color: #fff;"
                                        required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background: rgba(255,255,255,0.1); border: 1px solid #3bd17a; color: #3bd17a;">USDT</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Available Balance: ${{ $walletBalance ?? '0.00' }}</small>
                            </div>

                            <!-- Network Fee Info -->
                            <div class="alert alert-info" style="background: rgba(59, 209, 122, 0.1); border: 1px solid #3bd17a; color: #3bd17a;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Network Fee:</strong> Estimated fee will be calculated when you confirm the transaction.
                            </div>

                            <!-- Transaction Summary -->
                            <div id="transactionSummary" class="d-none">
                                <div class="card" style="background: rgba(255,255,255,0.05); border: 1px solid #3bd17a;">
                                    <div class="card-body">
                                        <h6 style="color: #3bd17a;">Transaction Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Amount:</span>
                                            <span id="summaryAmount">0 USDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Network Fee:</span>
                                            <span id="summaryFee">~0.001 USDT</span>
                                        </div>
                                        <hr style="border-color: #3bd17a;">
                                        <div class="d-flex justify-content-between font-weight-bold">
                                            <span>Total:</span>
                                            <span id="summaryTotal">0 USDT</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="sendButton" onclick="initiateSend()">
                            <i class="fas fa-paper-plane me-2"></i>Send USDT
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $totalReferrals ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Total Referrals</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $activePlanUsers ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Active Plan Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #1dd1a1; margin-bottom: 5px;">{{ $pendingPlanUsers ?? 0 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Pending Plan Users</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-center p-3 h-100" style="background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1)); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px;">
                    <div style="font-size: 2rem; font-weight: 700; color: #f0c24b; margin-bottom: 5px;">{{ auth()->user()->level ?? 1 }}</div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem; font-weight: 500;">Your Level</div>
                </div>
            </div>
        </div>
       
    </div>

    <!-- Exchange Modal -->
    <div class="modal fade" id="exchangemodal" tabindex="-1" role="dialog" aria-labelledby="exchangeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background:#072d42;color:#fff;border-radius:15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="exchangeLabel" style="color:#3bd17a;">Exchange to Pool Wallet</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <label for="exSource" class="form-label text-warning">Transfer From:</label>
                    <select class="form-control mb-3" id="exSource" style="background:rgba(255,255,255,0.1);border:1px solid #3bd17a;color:#fff;">
                        <option value="balance">Balance Wallet</option>
                        <option value="commission">Pool Commission</option>
                    </select>

                    <div class="alert alert-secondary" style="background:rgba(255,255,255,0.06);border:1px solid #3bd17a;color:#9ec3d8;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div><small id="exSourceLabel">Balance Wallet</small></div>
                                <div class="h5 mb-0" id="exBalance">${{ number_format($walletBalance ?? 0, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <div><small>Pool Wallet</small></div>
                                <div class="h5 mb-0" id="exPool">${{ number_format(auth()->user()->pool_wallet_amount ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <label for="exAmount" class="form-label text-warning">Amount to transfer (USDT)</label>
                    <input type="number" class="form-control mb-2" id="exAmount" min="0.01" step="0.01"
                        placeholder="Enter amount" style="background:rgba(255,255,255,0.1);border:1px solid #3bd17a;color:#fff;">
                    <small class="text-muted">Max: <span id="exMax">{{ number_format($walletBalance ?? 0, 2) }}</span> USDT</small>
                    <input type="hidden" id="exMaxBalance" value="{{ $walletBalance ?? 0 }}">
                    <input type="hidden" id="exMaxCommission" value="{{ auth()->user()->referral_commission_balance ?? 0 }}">
                    <input type="hidden" id="exPoolWalletAmount" value="{{ auth()->user()->pool_wallet_amount ?? 0 }}">

                    <div class="mt-3 card" style="background:rgba(255,255,255,0.05);border:1px solid #3bd17a;">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <span id="exPreviewSourceLabel">Preview Balance Wallet</span>
                                <strong id="exPreviewBalance">${{ number_format($walletBalance ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Preview Pool Wallet</span>
                                <strong id="exPreviewPool">${{ number_format(auth()->user()->pool_wallet_amount ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div id="exMsg" class="mt-3 d-none"></div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="btnExchange" class="btn btn-success">
                        <i class="fas fa-lock mr-1"></i> Verify PIN & Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- {{-- Exchange modal end --}} -->


    <div class="modal fade" id="pinModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#072d42;color:#fff;border-radius:15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" style="color:#3bd17a;">Verify Security PIN</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="password" id="pinInput" class="form-control" placeholder="Enter 6-digit PIN"
                        maxlength="6" style="background:rgba(255,255,255,0.1);border:1px solid #3bd17a;color:#fff;">
                    <small class="text-muted d-block mt-2">This secures your transfer.</small>
                    <div id="pinMsg" class="mt-2 d-none"></div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="btnVerifyPin"><i class="fas fa-check mr-1"></i> Verify</button>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- main-panel ends -->

<script>
    function copyWalletAddress() {
        var copyText = document.getElementById("walletAddress");

        // Copy to clipboard
        navigator.clipboard.writeText(copyText.value).then(function() {

            // Change button text
            let btn = document.getElementById("copyBtn");
            btn.innerText = "Copied!";
            btn.classList.remove("btn-success");
            btn.classList.add("btn-info");

            // Reset back after 2s
            setTimeout(() => {
                btn.innerText = "Copy";
                btn.classList.remove("btn-info");
                btn.classList.add("btn-success");
            }, 2000);

            // Show toast
            showToast("Wallet address copied!");
        });
    }

    // Copy wallet address from dashboard
    function copyWalletAddressToClipboard(walletAddress) {
        navigator.clipboard.writeText(walletAddress).then(function() {
            showToast("Wallet address copied to clipboard!");
        }).catch(function(err) {
            console.error('Failed to copy wallet address: ', err);
            showToast("Failed to copy wallet address", "error");
        });
    }

    // Auto-restore wallet connection on dashboard load
    function autoRestoreWalletConnection() {
        // Check if user has a saved wallet address
        @if(auth() -> user() -> wallet_address)
        console.log('✅ User has saved wallet address:', '{{ auth()->user()->wallet_address }}');

        // Save to localStorage for consistency
        localStorage.setItem('walletAccount', '{{ auth()->user()->wallet_address }}');
        localStorage.setItem('isWalletConnected', 'true');
        localStorage.setItem('walletType', 'trust'); // Assume Trust Wallet

        console.log('✅ Wallet connection state restored from database');
        @else
        console.log('ℹ️ No wallet address saved for user');
        @endif
    }

    // Trust Wallet connection functions
    async function connectTrustWallet() {
        try {
            // Check if Trust Wallet is available
            if (typeof window.ethereum !== 'undefined') {
                // Check if it's Trust Wallet
                if (window.ethereum.isTrust) {
                    console.log('✅ Trust Wallet detected');

                    // Request account access
                    const accounts = await window.ethereum.request({
                        method: 'eth_requestAccounts'
                    });

                    if (accounts.length > 0) {
                        const walletAddress = accounts[0];

                        // Save wallet info
                        localStorage.setItem('walletAccount', walletAddress);
                        localStorage.setItem('isWalletConnected', 'true');
                        localStorage.setItem('walletType', 'trust');

                        // Update UI
                        updateWalletConnectionStatus(walletAddress);

                        // Save to backend
                        await saveWalletToBackend(walletAddress);

                        showToast('Trust Wallet connected successfully!');
                    }
                } else {
                    // Try to detect Trust Wallet by provider
                    if (window.ethereum.providers) {
                        const trustProvider = window.ethereum.providers.find(provider => provider.isTrust);
                        if (trustProvider) {
                            const accounts = await trustProvider.request({
                                method: 'eth_requestAccounts'
                            });

                            if (accounts.length > 0) {
                                const walletAddress = accounts[0];
                                localStorage.setItem('walletAccount', walletAddress);
                                localStorage.setItem('isWalletConnected', 'true');
                                localStorage.setItem('walletType', 'trust');

                                updateWalletConnectionStatus(walletAddress);
                                await saveWalletToBackend(walletAddress);
                                showToast('Trust Wallet connected successfully!');
                            }
                        }
                    } else {
                        showToast('Trust Wallet not detected. Please install Trust Wallet app.', 'error');
                    }
                }
            } else {
                showToast('No crypto wallet detected. Please install Trust Wallet.', 'error');
            }
        } catch (error) {
            console.error('Trust Wallet connection error:', error);
            showToast('Failed to connect Trust Wallet: ' + error.message, 'error');
        }
    }

    // Update wallet connection status in topup modal
    function updateWalletConnectionStatus(walletAddress) {
        const connectedStatus = document.getElementById('walletConnectedStatus');
        const notConnectedStatus = document.getElementById('walletNotConnectedStatus');
        const userWalletAddress = document.getElementById('userWalletAddress');

        if (walletAddress) {
            connectedStatus.classList.remove('d-none');
            notConnectedStatus.classList.add('d-none');
            userWalletAddress.textContent = walletAddress;
        } else {
            connectedStatus.classList.add('d-none');
            notConnectedStatus.classList.remove('d-none');
        }
    }

    // Save wallet address to backend
    async function saveWalletToBackend(walletAddress) {
        try {
            const response = await fetch('{{ route("user.wallet.connect") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    wallet_address: walletAddress,
                    wallet_type: 'trust'
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to save wallet address');
            }

            return result;
        } catch (error) {
            console.error('Save wallet error:', error);
            throw error;
        }
    }

    // Copy admin wallet address
    function copyAdminWalletAddress() {
        const adminAddress = document.getElementById('adminWalletAddress');
        navigator.clipboard.writeText(adminAddress.value).then(function() {
            const btn = document.getElementById('copyAdminBtn');
            btn.innerText = "Copied!";
            btn.classList.remove("btn-success");
            btn.classList.add("btn-info");

            setTimeout(() => {
                btn.innerText = "Copy";
                btn.classList.remove("btn-info");
                btn.classList.add("btn-success");
            }, 2000);

            showToast("Admin wallet address copied!");
        });
    }

    // Refresh wallet balance
    async function refreshWalletBalance() {
        const refreshBtn = document.getElementById('refreshBalanceBtn');
        const originalText = refreshBtn.innerHTML;

        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Refreshing...';
        refreshBtn.disabled = true;

        try {
            // Refresh admin wallet address
            await refreshAdminWalletAddress();

            // Simulate balance refresh (replace with actual API call)
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Reload page to get updated balance
            location.reload();
        } catch (error) {
            showToast('Failed to refresh balance', 'error');
        } finally {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        }
    }

    // Refresh admin wallet address
    async function refreshAdminWalletAddress() {
        const refreshBtn = document.getElementById('refreshAdminBtn');
        const originalContent = refreshBtn.innerHTML;

        // Show loading state
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        refreshBtn.disabled = true;

        try {
            const response = await fetch('{{ route("user.admin.wallet.address") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                const adminAddressInput = document.getElementById('adminWalletAddress');
                const statusIndicator = document.querySelector('.admin-wallet-status');

                // Update admin wallet address
                adminAddressInput.value = result.admin_wallet_address;

                // Update status indicator
                if (statusIndicator) {
                    if (result.is_live) {
                        statusIndicator.innerHTML = '<i class="fas fa-check-circle me-1"></i>Live admin wallet address';
                        statusIndicator.className = 'mt-1 admin-wallet-status text-success';
                    } else {
                        statusIndicator.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Using fallback address';
                        statusIndicator.className = 'mt-1 admin-wallet-status text-warning';
                    }
                }

                showToast('Admin wallet address refreshed successfully!');
                console.log('✅ Admin wallet address refreshed:', result.admin_wallet_address);
            } else {
                showToast('Failed to refresh admin wallet address', 'error');
            }
        } catch (error) {
            console.error('Failed to refresh admin wallet address:', error);
            showToast('Failed to refresh admin wallet address', 'error');
        } finally {
            // Restore button state
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }
    }

    // Auto transaction detection
    let detectionInterval;
    let isDetecting = false;

    // Start automatic transaction detection
    function startTransactionDetection() {
        if (isDetecting) return;

        const userWallet = localStorage.getItem('walletAccount');
        const amount = document.getElementById('topupAmount').value;

        if (!userWallet) {
            showToast('Please connect your wallet first', 'error');
            return;
        }

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        isDetecting = true;

        // Show detection progress
        document.getElementById('autoDetectionStatus').classList.add('d-none');
        document.getElementById('detectionProgress').classList.remove('d-none');

        // Start checking every 10 seconds
        detectionInterval = setInterval(checkForNewTransactions, 10000);

        // Initial check
        checkForNewTransactions();
    }

    // Stop transaction detection
    function stopTransactionDetection() {
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }
        isDetecting = false;
    }

    // Check for new transactions
    async function checkForNewTransactions() {
        try {
            const response = await fetch('{{ route("user.wallet.check.transactions") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success && result.new_transactions.length > 0) {
                const userWallet = localStorage.getItem('walletAccount');

                // Find transaction from current user
                const userTransaction = result.new_transactions.find(tx =>
                    tx.from.toLowerCase() === userWallet.toLowerCase()
                );

                if (userTransaction) {
                    // Process the detected transaction
                    await processDetectedTransaction(userTransaction);
                }
            }
        } catch (error) {
            console.error('Transaction detection error:', error);
        }
    }

    // Process detected transaction
    async function processDetectedTransaction(transaction) {
        try {
            // Extract amount from transaction data (simplified)
            const amount = parseFloat(transaction.value) / Math.pow(10, 18); // Convert from wei

            const response = await fetch('{{ route("user.wallet.process.detected") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tx_hash: transaction.hash,
                    from_address: transaction.from,
                    amount: amount,
                    block_number: transaction.blockNumber
                })
            });

            const result = await response.json();

            if (result.success) {
                // Show success status
                document.getElementById('detectionProgress').classList.add('d-none');
                document.getElementById('detectionSuccess').classList.remove('d-none');

                showToast('Transaction detected and processed! Your balance has been updated.');

                // Stop detection
                stopTransactionDetection();

                // Refresh page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                console.error('Transaction processing failed:', result.message);
            }
        } catch (error) {
            console.error('Error processing detected transaction:', error);
        }
    }

    // Open Trust Wallet for sending USDT
    function openTrustWallet() {
        const amount = document.getElementById('topupAmount').value;
        const adminAddress = document.getElementById('adminWalletAddress').value;

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        if (!adminAddress) {
            showToast('Admin wallet address not found', 'error');
            return;
        }

        // Show confirmation dialog
        const confirmMessage = `Confirm USDT BEP20 Transfer:
            
Amount: ${amount} USDT
To: ${adminAddress}
Network: BNB Smart Chain

Do you want to proceed?`;

        if (confirm(confirmMessage)) {
            // Check if Trust Wallet is available
            if (typeof window.ethereum !== 'undefined' && window.ethereum.isTrust) {
                // Trust Wallet is available, try to send transaction
                sendUSDTViaTrustWallet(adminAddress, amount);
            } else {
                // Trust Wallet not available, show instructions
                showTrustWalletInstructions(adminAddress, amount);
            }
        }
    }

    // Alternative method: Open Trust Wallet with deep link
    function openTrustWalletDeepLink() {
        const amount = document.getElementById('topupAmount').value;
        const adminAddress = document.getElementById('adminWalletAddress').value;

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        if (!adminAddress) {
            showToast('Admin wallet address not found', 'error');
            return;
        }

        // Create deep link for Trust Wallet
        const deepLink = `trust://send?asset=c60_t0x55d398326f99059fF775485246999027B3197955&address=${adminAddress}&amount=${amount}`;

        // Try to open Trust Wallet
        window.location.href = deepLink;

        // Show instructions as backup
        setTimeout(() => {
            showTrustWalletInstructions(adminAddress, amount);
        }, 2000);
    }

    // Send USDT via Trust Wallet (BNB Smart Chain)
    async function sendUSDTViaTrustWallet(toAddress, amount) {
        try {
            // Check if user is connected
            const accounts = await window.ethereum.request({
                method: 'eth_accounts'
            });
            if (accounts.length === 0) {
                // Request connection
                await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
            }

            // Switch to BNB Smart Chain (BSC) network
            await switchToBNBSmartChain();

            // USDT contract address on BSC (BEP20)
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';

            // Convert amount to wei (USDT has 18 decimals)
            const web3 = new Web3(window.ethereum);
            const amountInWei = web3.utils.toWei(amount.toString(), 'ether');

            // USDT transfer function signature
            const transferData = web3.eth.abi.encodeFunctionCall({
                name: 'transfer',
                type: 'function',
                inputs: [{
                        type: 'address',
                        name: 'to'
                    },
                    {
                        type: 'uint256',
                        name: 'amount'
                    }
                ]
            }, [toAddress, amountInWei]);

            // Get gas price for BSC
            const gasPrice = await web3.eth.getGasPrice();
            const gasLimit = '0x7530'; // 30000 gas limit

            // Send transaction on BSC
            const txHash = await window.ethereum.request({
                method: 'eth_sendTransaction',
                params: [{
                    from: accounts[0],
                    to: usdtContractAddress,
                    data: transferData,
                    gas: gasLimit,
                    gasPrice: gasPrice
                }]
            });

            showToast('USDT BEP20 transaction sent! Hash: ' + txHash.substring(0, 10) + '...', 'success');

            // Start detection after sending
            setTimeout(() => {
                startTransactionDetection();
            }, 2000);

        } catch (error) {
            console.error('Trust Wallet transaction error:', error);
            if (error.code === 4001) {
                showToast('Transaction rejected by user', 'error');
            } else if (error.code === -32603) {
                showToast('Insufficient USDT balance or gas fee', 'error');
            } else {
                showToast('Transaction failed: ' + error.message, 'error');
            }
        }
    }

    // Switch to BNB Smart Chain network
    async function switchToBNBSmartChain() {
        try {
            // BNB Smart Chain network details
            const bscNetwork = {
                chainId: '0x38', // 56 in decimal
                chainName: 'BNB Smart Chain',
                nativeCurrency: {
                    name: 'BNB',
                    symbol: 'BNB',
                    decimals: 18
                },
                rpcUrls: ['https://bsc-dataseed.binance.org/'],
                blockExplorerUrls: ['https://bscscan.com/']
            };

            // Try to switch to BSC network
            await window.ethereum.request({
                method: 'wallet_switchEthereumChain',
                params: [{
                    chainId: bscNetwork.chainId
                }]
            });
        } catch (switchError) {
            // If network doesn't exist, add it
            if (switchError.code === 4902) {
                try {
                    await window.ethereum.request({
                        method: 'wallet_addEthereumChain',
                        params: [bscNetwork]
                    });
                } catch (addError) {
                    console.error('Failed to add BSC network:', addError);
                    throw new Error('Please add BNB Smart Chain network to your wallet');
                }
            } else {
                throw switchError;
            }
        }
    }

    // Show Trust Wallet instructions
    function showTrustWalletInstructions(toAddress, amount) {
        const message = `Trust Wallet Instructions (USDT BEP20):
            
1. Open Trust Wallet app
2. Switch to BNB Smart Chain network
3. Go to USDT (BEP20) - NOT BNB
4. Tap "Send"
5. Paste this address: ${toAddress}
6. Enter amount: ${amount} USDT
7. Make sure you have BNB for gas fees
8. Send the transaction

Important:
- Send USDT BEP20, not BNB
- You need BNB for gas fees
- After sending, click "Start Detection" below`;

        alert(message);
    }

    // Alternative simple send function (without approval)
    async function sendUSDTSimple() {
        const amount = document.getElementById('topupAmount').value;
        const adminAddress = document.getElementById('adminWalletAddress').value;

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        if (!adminAddress) {
            showToast('Admin wallet address not found', 'error');
            return;
        }

        try {
            // Check if wallet is connected
            if (typeof window.ethereum === 'undefined') {
                showToast('Please install Trust Wallet or MetaMask', 'error');
                return;
            }

            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            if (accounts.length === 0) {
                showToast('Please connect your wallet', 'error');
                return;
            }

            // Switch to BNB Smart Chain
            await switchToBNBSmartChain();

            // USDT contract address on BSC
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';

            // Convert amount to wei
            const web3 = new Web3(window.ethereum);
            const amountInWei = web3.utils.toWei(amount.toString(), 'ether');

            // Direct transfer (user needs to approve manually first)
            showToast('Sending USDT...', 'info');

            const transferData = web3.eth.abi.encodeFunctionCall({
                name: 'transfer',
                type: 'function',
                inputs: [{
                        type: 'address',
                        name: 'to'
                    },
                    {
                        type: 'uint256',
                        name: 'amount'
                    }
                ]
            }, [adminAddress, amountInWei]);

            // Get gas price and estimate gas
            const gasPrice = await web3.eth.getGasPrice();
            const gasEstimate = await web3.eth.estimateGas({
                from: accounts[0],
                to: usdtContractAddress,
                data: transferData
            });

            const txHash = await window.ethereum.request({
                method: 'eth_sendTransaction',
                params: [{
                    from: accounts[0],
                    to: usdtContractAddress,
                    data: transferData,
                    gas: '0x' + gasEstimate.toString(16),
                    gasPrice: gasPrice
                }]
            });

            showToast('USDT sent successfully! Transaction: ' + txHash.substring(0, 10) + '...', 'success');

            // Wait for transaction confirmation and then process
            try {
                await waitForTransaction(txHash);
                showToast('Transaction confirmed! Processing...', 'info');

                // Process the transaction immediately
                await processDetectedTransaction({
                    hash: txHash,
                    from: accounts[0],
                    value: amountInWei,
                    blockNumber: '0x' + (await web3.eth.getBlockNumber()).toString(16)
                });

                showToast('Balance updated successfully!', 'success');

                // Refresh the page to show updated balance
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } catch (error) {
                console.error('Transaction processing error:', error);
                showToast('Transaction sent but processing failed. Please refresh manually.', 'warning');
            }

        } catch (error) {
            console.error('Send USDT error:', error);
            if (error.code === 4001) {
                showToast('Transaction rejected by user', 'error');
            } else if (error.message.includes('insufficient allowance')) {
                showToast('Please approve USDT first. Click "Send USDT BEP20" again.', 'error');
            } else {
                showToast('Transaction failed: ' + error.message, 'error');
            }
        }
    }

    // Simple direct send function
    async function sendUSDTDirect() {
        const amount = document.getElementById('topupAmount').value;
        const adminAddress = document.getElementById('adminWalletAddress').value;

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        if (!adminAddress) {
            showToast('Admin wallet address not found', 'error');
            return;
        }

        try {
            // Check if wallet is connected
            if (typeof window.ethereum === 'undefined') {
                showToast('Please install Trust Wallet or MetaMask', 'error');
                return;
            }

            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            if (accounts.length === 0) {
                showToast('Please connect your wallet', 'error');
                return;
            }

            // Switch to BNB Smart Chain
            await switchToBNBSmartChain();

            // USDT contract address on BSC
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';

            // Convert amount to wei
            const web3 = new Web3(window.ethereum);
            const amountInWei = web3.utils.toWei(amount.toString(), 'ether');

            // Check USDT balance first
            const balance = await web3.eth.call({
                to: usdtContractAddress,
                data: web3.eth.abi.encodeFunctionCall({
                    name: 'balanceOf',
                    type: 'function',
                    inputs: [{
                        type: 'address',
                        name: 'account'
                    }]
                }, [accounts[0]])
            });

            const userBalance = web3.utils.fromWei(balance, 'ether');
            if (parseFloat(userBalance) < parseFloat(amount)) {
                showToast('Insufficient USDT balance', 'error');
                return;
            }

            // Check allowance
            const allowance = await web3.eth.call({
                to: usdtContractAddress,
                data: web3.eth.abi.encodeFunctionCall({
                    name: 'allowance',
                    type: 'function',
                    inputs: [{
                            type: 'address',
                            name: 'owner'
                        },
                        {
                            type: 'address',
                            name: 'spender'
                        }
                    ]
                }, [accounts[0], adminAddress])
            });

            const currentAllowance = web3.utils.fromWei(allowance, 'ether');

            // If allowance is not enough, approve first
            if (parseFloat(currentAllowance) < parseFloat(amount)) {
                showToast('Approving USDT...', 'info');

                // Use maximum allowance for better UX
                const maxAllowance = web3.utils.toWei('1000000', 'ether'); // 1M USDT max allowance

                const approveData = web3.eth.abi.encodeFunctionCall({
                    name: 'approve',
                    type: 'function',
                    inputs: [{
                            type: 'address',
                            name: 'spender'
                        },
                        {
                            type: 'uint256',
                            name: 'amount'
                        }
                    ]
                }, [adminAddress, maxAllowance]);

                // Get gas price and estimate gas
                const gasPrice = await web3.eth.getGasPrice();
                const gasEstimate = await web3.eth.estimateGas({
                    from: accounts[0],
                    to: usdtContractAddress,
                    data: approveData
                });

                const approveTx = await window.ethereum.request({
                    method: 'eth_sendTransaction',
                    params: [{
                        from: accounts[0],
                        to: usdtContractAddress,
                        data: approveData,
                        gas: '0x' + gasEstimate.toString(16),
                        gasPrice: gasPrice
                    }]
                });

                showToast('Approval transaction sent. Please wait for confirmation...', 'info');

                // Wait for approval confirmation
                await waitForTransaction(approveTx);
                showToast('USDT approved successfully!', 'success');
            }

            // Now send USDT
            showToast('Sending USDT...', 'info');

            const transferData = web3.eth.abi.encodeFunctionCall({
                name: 'transfer',
                type: 'function',
                inputs: [{
                        type: 'address',
                        name: 'to'
                    },
                    {
                        type: 'uint256',
                        name: 'amount'
                    }
                ]
            }, [adminAddress, amountInWei]);

            // Get gas price and estimate gas for transfer
            const gasPrice = await web3.eth.getGasPrice();
            const gasEstimate = await web3.eth.estimateGas({
                from: accounts[0],
                to: usdtContractAddress,
                data: transferData
            });

            const txHash = await window.ethereum.request({
                method: 'eth_sendTransaction',
                params: [{
                    from: accounts[0],
                    to: usdtContractAddress,
                    data: transferData,
                    gas: '0x' + gasEstimate.toString(16),
                    gasPrice: gasPrice
                }]
            });

            showToast('USDT sent successfully! Transaction: ' + txHash.substring(0, 10) + '...', 'success');

            // Wait for transaction confirmation and then process
            try {
                await waitForTransaction(txHash);
                showToast('Transaction confirmed! Processing...', 'info');

                // Process the transaction immediately
                await processDetectedTransaction({
                    hash: txHash,
                    from: accounts[0],
                    value: amountInWei,
                    blockNumber: '0x' + (await web3.eth.getBlockNumber()).toString(16)
                });

                showToast('Balance updated successfully!', 'success');

                // Refresh the page to show updated balance
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } catch (error) {
                console.error('Transaction processing error:', error);
                showToast('Transaction sent but processing failed. Please refresh manually.', 'warning');
            }

        } catch (error) {
            console.error('Send USDT error:', error);
            if (error.code === 4001) {
                showToast('Transaction rejected by user', 'error');
            } else {
                showToast('Transaction failed: ' + error.message, 'error');
            }
        }
    }

    // Process detected transaction immediately
    async function processDetectedTransaction(txData) {
        try {
            // Convert amount from wei to decimal
            const amount = parseFloat(txData.value) / Math.pow(10, 18);

            const response = await fetch('{{ route("user.wallet.process.detected") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tx_hash: txData.hash,
                    from_address: txData.from,
                    amount: amount,
                    block_number: txData.blockNumber
                })
            });

            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Get response text first to debug
            const responseText = await response.text();
            console.log('Raw response:', responseText);

            // Try to parse JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (jsonError) {
                console.error('JSON parsing error:', jsonError);
                console.error('Response text:', responseText);
                throw new Error('Invalid JSON response from server');
            }

            if (result.success) {
                console.log('Transaction processed successfully:', result);
                return result;
            } else {
                throw new Error(result.message || 'Failed to process transaction');
            }
        } catch (error) {
            console.error('Error processing transaction:', error);
            showToast('Transaction failed: ' + error.message, 'error');
            throw error;
        }
    }

    // Wait for transaction confirmation
    async function waitForTransaction(txHash) {
        return new Promise((resolve, reject) => {
            const checkInterval = setInterval(async () => {
                try {
                    const receipt = await window.ethereum.request({
                        method: 'eth_getTransactionReceipt',
                        params: [txHash]
                    });

                    if (receipt) {
                        clearInterval(checkInterval);
                        if (receipt.status === '0x1') {
                            resolve(receipt);
                        } else {
                            reject(new Error('Transaction failed'));
                        }
                    }
                } catch (error) {
                    clearInterval(checkInterval);
                    reject(error);
                }
            }, 2000);

            // Timeout after 60 seconds
            setTimeout(() => {
                clearInterval(checkInterval);
                reject(new Error('Transaction timeout'));
            }, 60000);
        });
    }

    // Direct send without detection
    function directSendUSDT() {
        const amount = document.getElementById('topupAmount').value;
        const adminAddress = document.getElementById('adminWalletAddress').value;

        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount to send', 'error');
            return;
        }

        if (!adminAddress) {
            showToast('Admin wallet address not found', 'error');
            return;
        }

        // Show instructions for manual sending
        const message = `Manual USDT BEP20 Transfer:
            
1. Open Trust Wallet app
2. Switch to BNB Smart Chain
3. Go to USDT (BEP20)
4. Tap "Send"
5. Paste address: ${adminAddress}
6. Enter amount: ${amount} USDT
7. Send transaction

After sending, your balance will be updated automatically.`;

        alert(message);
    }

    // Process topup transaction (legacy method - kept for compatibility)
    async function processTopupTransaction() {
        showToast('Please use the automatic detection system. Send USDT to admin wallet and we will detect it automatically.', 'info');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        autoRestoreWalletConnection();
    });

    // Simple toast function
    function showToast(message) {
        let toast = document.createElement("div");
        toast.className = "toast-message";
        toast.innerText = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("show");
        }, 100);

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }


    $('#topUpModal').on('shown.bs.modal', function() {
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.body.style.marginRight = '0px';

        // Check wallet connection status when modal opens
        checkWalletConnectionStatus();
    });

    $('#topUpModal').on('hidden.bs.modal', function() {
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '0px';
        document.body.style.marginRight = '0px';

        // Stop transaction detection when modal closes
        stopTransactionDetection();

        // Reset detection status
        document.getElementById('autoDetectionStatus').classList.remove('d-none');
        document.getElementById('detectionProgress').classList.add('d-none');
        document.getElementById('detectionSuccess').classList.add('d-none');
    });

    // Check wallet connection status
    function checkWalletConnectionStatus() {
        const savedWallet = localStorage.getItem('walletAccount');
        const isConnected = localStorage.getItem('isWalletConnected') === 'true';
        const walletType = localStorage.getItem('walletType');

        if (savedWallet && isConnected && walletType === 'trust') {
            updateWalletConnectionStatus(savedWallet);
            console.log('✅ Wallet connection status restored:', savedWallet);
        } else {
            updateWalletConnectionStatus(null);
            console.log('ℹ️ No wallet connection found');
        }
    }

    // Send Modal Event Handlers
    $('#sendModal').on('shown.bs.modal', function() {
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.body.style.marginRight = '0px';
    });

    $('#sendModal').on('hidden.bs.modal', function() {
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '0px';
        document.body.style.marginRight = '0px';
        // Reset form
        document.getElementById('sendForm').reset();
        document.getElementById('transactionSummary').classList.add('d-none');
    });

    // Real-time validation and summary update
    document.getElementById('sendAmount').addEventListener('input', function() {
        updateTransactionSummary();
    });

    document.getElementById('recipientAddress').addEventListener('input', function() {
        validateAddress();
    });

    function updateTransactionSummary() {
        const amount = parseFloat(document.getElementById('sendAmount').value) || 0;
        const fee = 0.001; // Estimated BSC fee
        const total = amount + fee;

        if (amount > 0) {
            document.getElementById('summaryAmount').textContent = amount.toFixed(2) + ' USDT';
            document.getElementById('summaryFee').textContent = '~' + fee.toFixed(3) + ' USDT';
            document.getElementById('summaryTotal').textContent = total.toFixed(3) + ' USDT';
            document.getElementById('transactionSummary').classList.remove('d-none');
        } else {
            document.getElementById('transactionSummary').classList.add('d-none');
        }
    }

    function validateAddress() {
        const address = document.getElementById('recipientAddress').value;
        const isValid = /^0x[a-fA-F0-9]{40}$/.test(address);

        if (address.length > 0) {
            if (isValid) {
                document.getElementById('recipientAddress').style.borderColor = '#3bd17a';
            } else {
                document.getElementById('recipientAddress').style.borderColor = '#dc3545';
            }
        } else {
            document.getElementById('recipientAddress').style.borderColor = '#3bd17a';
        }
    }

    function initiateSend() {
        const recipientAddress = document.getElementById('recipientAddress').value;
        const amount = document.getElementById('sendAmount').value;

        // Validate inputs
        if (!recipientAddress || !amount) {
            showToast('Please fill in all fields', 'error');
            return;
        }

        if (!/^0x[a-fA-F0-9]{40}$/.test(recipientAddress)) {
            showToast('Please enter a valid wallet address', 'error');
            return;
        }

        if (parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount', 'error');
            return;
        }

        // Check if user has wallet connected
        if (!window.ethereum) {
            showToast('Please install MetaMask or connect your wallet', 'error');
            return;
        }

        // Check if user has sufficient balance
        const availableBalance = {
            {
                $walletBalance ?? 0
            }
        };
        if (parseFloat(amount) > availableBalance) {
            showToast('Insufficient balance', 'error');
            return;
        }

        // Show loading state
        const sendButton = document.getElementById('sendButton');
        const originalText = sendButton.innerHTML;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        sendButton.disabled = true;

        // Call the send function
        sendUSDT(recipientAddress, amount)
            .then((result) => {
                // Record transaction in backend
                return recordTransaction(recipientAddress, amount, result.txHash);
            })
            .then((backendResult) => {
                showToast('Transaction sent and recorded successfully!', 'success');
                $('#sendModal').modal('hide');
                // Optionally refresh the page or update balance
                setTimeout(() => {
                    location.reload();
                }, 2000);
            })
            .catch((error) => {
                showToast('Transaction failed: ' + error.message, 'error');
            })
            .finally(() => {
                sendButton.innerHTML = originalText;
                sendButton.disabled = false;
            });
    }

    async function sendUSDT(recipientAddress, amount) {
        try {
            // Check if wallet is connected
            const accounts = await window.ethereum.request({
                method: 'eth_accounts'
            });
            if (accounts.length === 0) {
                throw new Error('Please connect your wallet first');
            }

            // USDT contract address on BSC (BEP20)
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';

            // Get the USDT contract ABI (simplified for transfer function)
            const usdtABI = [{
                "constant": false,
                "inputs": [{
                        "name": "_to",
                        "type": "address"
                    },
                    {
                        "name": "_value",
                        "type": "uint256"
                    }
                ],
                "name": "transfer",
                "outputs": [{
                    "name": "",
                    "type": "bool"
                }],
                "type": "function"
            }];

            // Create contract instance
            const web3 = new Web3(window.ethereum);
            const usdtContract = new web3.eth.Contract(usdtABI, usdtContractAddress);

            // Convert amount to wei (USDT has 18 decimals)
            const amountInWei = web3.utils.toWei(amount.toString(), 'ether');

            // Get current account
            const fromAddress = accounts[0];

            // Estimate gas
            const gasEstimate = await usdtContract.methods.transfer(recipientAddress, amountInWei).estimateGas({
                from: fromAddress
            });

            // Send transaction
            const transaction = await usdtContract.methods.transfer(recipientAddress, amountInWei).send({
                from: fromAddress,
                gas: gasEstimate
            });

            return {
                success: true,
                txHash: transaction.transactionHash
            };

        } catch (error) {
            console.error('Send USDT error:', error);
            throw error;
        }
    }

    async function recordTransaction(recipientAddress, amount, txHash) {
        try {
            const response = await fetch('{{ route("user.wallet.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    recipient_address: recipientAddress,
                    amount: parseFloat(amount),
                    tx_hash: txHash
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to record transaction');
            }

            return result;
        } catch (error) {
            console.error('Record transaction error:', error);
            throw error;
        }
    }
</script>
<script>
    (function() {
        const exAmount = document.getElementById('exAmount');
        const exBalanceEl = document.getElementById('exBalance');
        const exPoolEl = document.getElementById('exPool');
        const exPrevBalEl = document.getElementById('exPreviewBalance');
        const exPrevPoolEl = document.getElementById('exPreviewPool');
        const exMsg = document.getElementById('exMsg');
        const exMaxEl = document.getElementById('exMax');
        const exSource = document.getElementById('exSource');
        const exSourceLabel = document.getElementById('exSourceLabel');
        const exPreviewSourceLabel = document.getElementById('exPreviewSourceLabel');
        const exMaxBalance = document.getElementById('exMaxBalance');
        const exMaxCommission = document.getElementById('exMaxCommission');

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            const btnExchange = document.getElementById('btnExchange');
            const pinModal = $('#pinModal');
            const pinInput = document.getElementById('pinInput');
            const pinMsg = document.getElementById('pinMsg');
            const btnVerifyPin = document.getElementById('btnVerifyPin');

            // Helpers
            const num = (v) => parseFloat((v || '0').toString().replace(/[^\d.]/g, '')) || 0;
            const fmt = (v) => Number(v).toFixed(2);

            function currentBalances() {
                const bal = num(exBalanceEl.textContent.replace(/[$,]/g, ''));
                const pool = num(exPoolEl.textContent.replace(/[$,]/g, ''));
                return {
                    bal,
                    pool
                };
            }

            // Update displayed values when source changes
            exSource && exSource.addEventListener('change', () => {
                const source = exSource.value;
                const poolWalletAmount = num(document.getElementById('exPoolWalletAmount').value);

                if (source === 'balance') {
                    const balanceAmount = num(exMaxBalance.value);
                    exSourceLabel.textContent = 'Balance Wallet';
                    exBalanceEl.textContent = '$' + fmt(balanceAmount);
                    exMaxEl.textContent = fmt(balanceAmount);
                    exPreviewSourceLabel.textContent = 'Preview Balance Wallet';
                    exPrevBalEl.textContent = '$' + fmt(balanceAmount);
                    exPrevPoolEl.textContent = '$' + fmt(poolWalletAmount);
                } else if (source === 'commission') {
                    const commissionAmount = num(exMaxCommission.value);
                    exSourceLabel.textContent = 'Pool Commission';
                    exBalanceEl.textContent = '$' + fmt(commissionAmount);
                    exMaxEl.textContent = fmt(commissionAmount);
                    exPreviewSourceLabel.textContent = 'Preview Pool Commission';
                    exPrevBalEl.textContent = '$' + fmt(commissionAmount);
                    exPrevPoolEl.textContent = '$' + fmt(poolWalletAmount);
                }
                exAmount.value = '';
                clearMsg(exMsg);
            });

            function showMsg(el, text, type = 'info') {
                el.className = `alert alert-${type}`;
                el.style.background = 'rgba(255,255,255,0.06)';
                el.style.border = '1px solid #3bd17a';
                el.classList.remove('d-none');
                el.innerText = text;
            }

            function clearMsg(el) {
                el.classList.add('d-none');
                el.innerHTML = '';
            }

            // Live preview
            exAmount && exAmount.addEventListener('input', () => {
                clearMsg(exMsg);
                const {
                    bal,
                    pool
                } = currentBalances();
                const amt = Math.max(0, num(exAmount.value));
                const max = num(exMaxEl.textContent.replace(/[$,]/g, ''));
                const source = exSource.value;
                const sourceName = source === 'balance' ? 'Balance Wallet' : 'Pool Commission';

                if (amt > max) {
                    showMsg(exMsg, `Amount exceeds ${sourceName}. Max available: $${fmt(max)}`, 'warning');
                }
                exPrevBalEl.textContent = '$' + fmt(Math.max(0, bal - amt));
                exPrevPoolEl.textContent = '$' + fmt(pool + amt);
            });

            // Step 1: Open PIN modal
            btnExchange && btnExchange.addEventListener('click', () => {
                console.log('Exchange button clicked!');
                const amt = num(exAmount.value);
                const max = num(exMaxEl.textContent.replace(/[$,]/g, ''));
                console.log('Amount:', amt, 'Max:', max);
                if (!amt || amt <= 0) {
                    showMsg(exMsg, 'Please enter a valid amount (min 0.01).', 'danger');
                    return;
                }
                if (amt > max) {
                    showMsg(exMsg, `Insufficient balance. Available: $${fmt(max)}`, 'danger');
                    return;
                }
                console.log('Opening PIN modal...');
                pinInput.value = '';
                clearMsg(pinMsg);
                pinModal.modal('show');
            });

            // Step 2: Verify PIN → if ok, perform exchange
            btnVerifyPin && btnVerifyPin.addEventListener('click', async () => {
                clearMsg(pinMsg);
                const pin = (pinInput.value || '').trim();

                if (!/^\d{6}$/.test(pin)) {
                    showMsg(pinMsg, 'Enter a valid 6-digit PIN.', 'warning');
                    return;
                }

                // Disable buttons while processing
                btnVerifyPin.disabled = true;
                btnVerifyPin.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Verifying...';

                try {
                    // 2.1 Verify PIN (use your existing route)
                    const csrf = document.querySelector("meta[name='csrf-token']")?.getAttribute('content') || '';
                    const verifyRes = await fetch('{{ route("security.pin.verify.ajax") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin', // session cookie carry
                        body: JSON.stringify({
                            security_pin: pin
                        }) // ✅ correct param name
                    });

                    const verifyJson = await verifyRes.json();
                    if (!verifyRes.ok || !verifyJson.success) {
                        showMsg(pinMsg, verifyJson.message || 'PIN verification failed.', 'danger');
                        btnVerifyPin.disabled = false;
                        btnVerifyPin.innerHTML = '<i class="fas fa-check mr-1"></i> Verify';
                        return;
                    }

                    // 2.2 If PIN OK → call exchange
                    const amt = num(exAmount.value);
                    const source = exSource.value;
                    const exchangeUrl = source === 'balance' ?
                        '{{ route("user.wallet.exchange") }}' :
                        '{{ route("user.wallet.commission-exchange") }}';

                    const exchRes = await fetch(exchangeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute('content')
                        },
                        body: JSON.stringify({
                            amount: amt
                        })
                    });

                    const exchJson = await exchRes.json();
                    if (!exchRes.ok || !exchJson.success) {
                        pinModal.modal('hide');
                        showMsg(exMsg, exchJson.message || 'Exchange failed. Please try again.', 'danger');
                        btnVerifyPin.disabled = false;
                        btnVerifyPin.innerHTML = '<i class="fas fa-check mr-1"></i> Verify';
                        return;
                    }

                    // Success: reload page
                    pinModal.modal('hide');
                    showMsg(exMsg, 'Exchange completed successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                } catch (e) {
                    showMsg(pinMsg, 'Something went wrong. Please try again.', 'danger');
                } finally {
                    btnVerifyPin.disabled = false;
                    btnVerifyPin.innerHTML = '<i class="fas fa-check mr-1"></i> Verify';
                }
            });

            // Reset modal state when opened
            $('#exchangemodal').on('shown.bs.modal', function() {
                clearMsg(exMsg);
                exSource.value = 'balance'; // Reset to Balance Wallet
                const balanceAmount = num(exMaxBalance.value);
                const poolWalletAmount = num(document.getElementById('exPoolWalletAmount').value);

                exSourceLabel.textContent = 'Balance Wallet';
                exBalanceEl.textContent = '$' + fmt(balanceAmount);
                exMaxEl.textContent = fmt(balanceAmount);
                exPreviewSourceLabel.textContent = 'Preview Balance Wallet';
                exPrevBalEl.textContent = '$' + fmt(balanceAmount);
                exPoolEl.textContent = '$' + fmt(poolWalletAmount);
                exPrevPoolEl.textContent = '$' + fmt(poolWalletAmount);
                exAmount.value = '';
            });

        }); // End of DOMContentLoaded listener

    })();
</script>

<!-- Web3.js for Trust Wallet integration -->
<script src="https://cdn.jsdelivr.net/npm/web3@1.10.0/dist/web3.min.js"></script>
@endsection