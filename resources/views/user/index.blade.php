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

        body.modal-open {
            overflow: hidden !important;
            padding-right: 0px !important;
            margin-right: 0px !important;
        }

        .modal {
            overflow-y: auto !important;
            /* modal content scrollable ho agar content bada ho */
        }

        .modal-open .modal {
            overflow-x: hidden !important;
            overflow-y: hidden !important;
            /* content chhota ho to scroll bilkul na aaye */
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
    </style>



    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    Dashboard
                </h3>
            </div>
            <div class="row mb-4">
                <!-- Left Side Main Balance Card -->
                <div class="col-md-6 col-lg-5">
                    <div class="main-card p-4 text-white position-relative d-flex justify-content-between align-items-center"
                        style="background: url('{{ asset('assets/images/bg-balance.png') }}') no-repeat center/cover; 
                             border-radius: 15px; 
                             min-height: 220px; ">
                        <!-- Logo Top Right -->
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="Logo"
                            style="position: absolute; top: 5px; left: 15px; height: 60px;">
                        <!-- Left: Balance -->
                        <div style="margin-top:70px;">
                            <h5 style="color: #3bd17a;">Total Balance</h5>
                            <h2 style="color: #3bd17a" class="font-weight-bold mb-0">${{ $walletBalance ?? '0.00' }}</h2>
                        </div>

                        <!-- Right: Buttons -->
                        <div class="d-flex flex-column">
                            <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                                data-toggle="modal" data-target="#topUpModal">
                                <i class="fas fa-arrow-down mr-1"></i> Top Up
                            </button>
                            <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                                data-toggle="modal" data-target="#sendModal">
                                <i class="fas fa-paper-plane mr-1"></i> Send
                            </button>
                            <button class="btn btn-light btn-sm" style="border-radius: 20px; min-width: 120px;">
                                <i class="fas fa-arrow-up mr-1"></i> Cash Out
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side Wallet Cards -->
                <div class="col-md-6 col-lg-7">
                    <!-- Wallet Connection Status -->
                    @if(auth()->user()->wallet_address)
                        <div class="card mb-3" style="background: linear-gradient(145deg, #072d42, #22384e); border: 1px solid #3bd17a;">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="fas fa-check-circle text-success fa-2x me-2"></i>
                                    <h5 class="mb-0 text-success">Wallet Connected</h5>
                                </div>
                                <div class="wallet-address-display">
                                    <small class="text-muted">Connected Address:</small><br>
                                    <code class="wallet-address-text text-info" style="font-size: 0.8rem; word-break: break-all;">
                                        {{ auth()->user()->wallet_address }}
                                    </code>
                                    <button class="btn btn-sm btn-outline-info ms-2" onclick="copyWalletAddressToClipboard('{{ auth()->user()->wallet_address }}')" title="Copy Address">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <small class="text-success">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Your wallet is securely connected
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mb-3" style="background: linear-gradient(145deg, #072d42, #22384e); border: 1px solid #ffc107;">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning fa-2x me-2"></i>
                                    <h5 class="mb-0 text-warning">Wallet Not Connected</h5>
                                </div>
                                <p class="text-muted mb-3">Connect your crypto wallet to manage your funds</p>
                                <a href="{{ route('user.wallet.index') }}" class="btn btn-warning">
                                    <i class="fas fa-link me-2"></i>Connect Wallet
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('balance-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                                    <h6 class="mb-1">Balance Wallet</h6>
                                    <span class="font-weight-bold">${{ $walletBalance ?? '0.00' }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pool-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-box fa-2x text-success mb-2"></i>
                                    <h6 class="mb-1">Pool Wallet</h6>
                                    <span class="font-weight-bold">${{ $walletBalance ?? '0.00' }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('exchange') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-exchange-alt fa-2x text-warning mb-2"></i>
                                    <h6 class="mb-1">Exchange</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pools') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1">Pools</h6>
                                </div>
                            </a>
                        </div>
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

                            <!-- QR Code -->
                            <div class="mb-3">
                                <img src="{{ asset('assets/images/qr-sample.png') }}" alt="QR Code" class="img-fluid"
                                    style="max-width: 180px;">
                            </div>

                            <!-- Wallet Address + Copy -->
                            <div class="input-group mb-3">
                                <input type="text" id="walletAddress" class="form-control text-center"
                                    value="0x1234567890ABCDEF1234567890ABCDEF12345678" readonly>
                                <div class="input-group-append">
                                    <button id="copyBtn" class="btn btn-success" type="button"
                                        onclick="copyWalletAddress()">Copy</button>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="p-3 rounded text-warning mt-3" style="background: rgba(255,255,255,0.1);">
                                <strong>Important:</strong> Please send only <b>USDT (BEP20)</b> to this address.
                                Sending any other token may result in permanent loss of funds.
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

            <div class="row grid-margin">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                                <div class="statistics-item">
                                    <p>
                                        <i class="icon-sm fa fa-users mr-2"></i>
                                        Total Referrals
                                    </p>
                                    <h2>{{ number_format($totalReferrals ?? 0) }}</h2>
                                    <label class="badge badge-outline-success badge-pill">
                                        {{ number_format($directReferrals ?? 0) }} direct ·
                                        {{ number_format($newReferralsToday ?? 0) }} today
                                    </label>
                                </div>
                                <!-- <div class="statistics-item">
                                                                                            <p>
                                                                                                <i class="icon-sm fas fa-hourglass-half mr-2"></i>
                                                                                                Avg Time
                                                                                            </p>
                                                                                            <h2>123.50</h2>
                                                                                            <label class="badge badge-outline-danger badge-pill">30% decrease</label>
                                                                                        </div>
                                                                                        <div class="statistics-item">
                                                                                            <p>
                                                                                                <i class="icon-sm fas fa-chart-line mr-2"></i>
                                                                                                This Week
                                                                                            </p>
                                                                                            <h2>{{ number_format($newReferralsWeek ?? 0) }}</h2>
                                                                                            <label class="badge badge-outline-info badge-pill">New referrals</label>
                                                                                        </div>
                                                                                        <div class="statistics-item">
                                                                                            <p>
                                                                                                <i class="icon-sm fas fa-check-circle mr-2"></i>
                                                                                                Update
                                                                                            </p>
                                                                                            <h2>7500</h2>
                                                                                            <label class="badge badge-outline-success badge-pill">57% increase</label>
                                                                                        </div>
                                                                                        <div class="statistics-item">
                                                                                            <p>
                                                                                                <i class="icon-sm fas fa-chart-line mr-2"></i>
                                                                                                Sales
                                                                                            </p>
                                                                                            <h2>9000</h2>
                                                                                            <label class="badge badge-outline-success badge-pill">10% increase</label>
                                                                                        </div>
                                                                                        <div class="statistics-item">
                                                                                            <p>
                                                                                                <i class="icon-sm fas fa-circle-notch mr-2"></i>
                                                                                                Pending
                                                                                            </p>
                                                                                            <h2>7500</h2>
                                                                                            <label class="badge badge-outline-danger badge-pill">16% decrease</label>
                                                                                        </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                                                                        <div class="col-md-6 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-gift"></i>
                                                                                        Orders
                                                                                    </h4>
                                                                                    <canvas id="orders-chart"></canvas>
                                                                                    <div id="orders-chart-legend" class="orders-chart-legend"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-chart-line"></i>
                                                                                        Sales
                                                                                    </h4>
                                                                                    <h2 class="mb-5">56000 <span class="text-muted h4 font-weight-normal">Sales</span></h2>
                                                                                    <canvas id="sales-chart"></canvas>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->
            <!-- <div class="row">
                                                                        <div class="col-md-4 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body d-flex flex-column">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-chart-pie"></i>
                                                                                        Sales status
                                                                                    </h4>
                                                                                    <div class="flex-grow-1 d-flex flex-column justify-content-between">
                                                                                        <canvas id="sales-status-chart" class="mt-3"></canvas>
                                                                                        <div class="pt-4">
                                                                                            <div id="sales-status-chart-legend" class="sales-status-chart-legend"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                   
                                                                    </div> -->
            <!-- <div class="row">
                                                                        <div class="col-12 grid-margin">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-envelope"></i>
                                                                                        Inbox(31)
                                                                                    </h4>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="form-check">
                                                                                                            <label class="form-check-label">
                                                                                                                <input type="checkbox" class="form-check-input">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face13.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Andrew Bowen
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-light badge-pill">Development</label>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        The required fields are added in the database
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="form-check">
                                                                                                            <label class="form-check-label">
                                                                                                                <input type="checkbox" class="form-check-input">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face2.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Mae Saunders
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-light badge-pill">Development</label>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        The application will be completed by tomorrow
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="form-check">
                                                                                                            <label class="form-check-label">
                                                                                                                <input type="checkbox" class="form-check-input">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="py-1">
                                                                                                        <div class="img-sm rounded-circle bg-warning profile-image-text">M</div>
                                                                                                    </td>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Manuel Yates
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-light badge-pill">Design</label>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        The new design is uploaded in zeplin
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="form-check">
                                                                                                            <label class="form-check-label">
                                                                                                                <input type="checkbox" class="form-check-input">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face11.html') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Marguerite Phillips
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-light badge-pill">Development</label>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        Please send me the latest requirements
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="form-check">
                                                                                                            <label class="form-check-label">
                                                                                                                <input type="checkbox" class="form-check-input">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="py-1">
                                                                                                        <div class="img-sm rounded-circle bg-info profile-image-text">C</div>
                                                                                                    </td>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Clifford Wilson
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-light badge-pill">Testing</label>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        The issues are documented in the shared sheet
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->
            <!-- <div class="row">
                                                                        <div class="col-md-8 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-table"></i>
                                                                                        Sales Data
                                                                                    </h4>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Customer</th>
                                                                                                    <th>Item code</th>
                                                                                                    <th>Orders</th>
                                                                                                    <th>Status</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Clifford Wilson
                                                                                                    </td>
                                                                                                    <td class="text-muted">
                                                                                                        PT613
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        350
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-success badge-pill">Dispatched</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Mary Payne
                                                                                                    </td>
                                                                                                    <td class="text-muted">
                                                                                                        ST456
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        520
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-warning badge-pill">Processing</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Adelaide Blake
                                                                                                    </td>
                                                                                                    <td class="text-muted">
                                                                                                        CS789
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        830
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-danger badge-pill">Failed</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Adeline King
                                                                                                    </td>
                                                                                                    <td class="text-muted">
                                                                                                        LP908
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        579
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-primary badge-pill">Delivered</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="font-weight-bold">
                                                                                                        Bertie Robbins
                                                                                                    </td>
                                                                                                    <td class="text-muted">
                                                                                                        HF675
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        790
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-info badge-pill">On Hold</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-calendar-alt"></i>
                                                                                        Calendar
                                                                                    </h4>
                                                                                    <div id="inline-datepicker-example" class="datepicker"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->
            <!-- <div class="row">
                                                                        <div class="col-md-7 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-thumbtack"></i>
                                                                                        Todo
                                                                                    </h4>
                                                                                    <div class="add-items d-flex">
                                                                                        <input type="text" class="form-control todo-list-input"
                                                                                            placeholder="What do you need to do today?">
                                                                                        <button class="add btn btn-primary font-weight-bold todo-list-add-btn"
                                                                                            id="add-task">Add</button>
                                                                                    </div>
                                                                                    <div class="list-wrapper">
                                                                                        <ul class="d-flex flex-column-reverse todo-list">
                                                                                            <li>
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox">
                                                                                                        Meeting with Alisa
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                            <li class="completed">
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox" checked>
                                                                                                        Call John
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox">
                                                                                                        Create invoice
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox">
                                                                                                        Print Statements
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                            <li class="completed">
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox" checked>
                                                                                                        Prepare for presentation
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                            <li>
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="checkbox" type="checkbox">
                                                                                                        Pick up kids from school
                                                                                                    </label>
                                                                                                </div>
                                                                                                <i class="remove fa fa-times-circle"></i>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-5 grid-margin stretch-card">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <h4 class="card-title">
                                                                                        <i class="fas fa-rocket"></i>
                                                                                        Projects
                                                                                    </h4>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>
                                                                                                        Assigned to
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        Project name
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        Priority
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face1.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        South Shyanne
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-warning badge-pill">Medium</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face2.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        New Trystan
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-danger badge-pill">High</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face3.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        East Helga
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-success badge-pill">Low</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td class="py-1">
                                                                                                        <img src="{{ asset('assets/dashboard/images/faces/face4.jpg') }}"
                                                                                                            alt="profile" class="img-sm rounded-circle" />
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        Omerbury
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <label class="badge badge-warning badge-pill">Medium</label>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <div class="d-md-flex justify-content-between align-items-center">
                                                                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                                                                            <button class="btn btn-social-icon btn-facebook btn-rounded">
                                                                                                <i class="fab fa-facebook-f"></i>
                                                                                            </button>
                                                                                            <div class="ml-4">
                                                                                                <h5 class="mb-0">Facebook</h5>
                                                                                                <p class="mb-0">813 friends</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                                                                            <button class="btn btn-social-icon btn-twitter btn-rounded">
                                                                                                <i class="fab fa-twitter"></i>
                                                                                            </button>
                                                                                            <div class="ml-4">
                                                                                                <h5 class="mb-0">Twitter</h5>
                                                                                                <p class="mb-0">9000 followers</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-flex align-items-center mb-3 mb-md-0">
                                                                                            <button class="btn btn-social-icon btn-google btn-rounded">
                                                                                                <i class="fab fa-google-plus-g"></i>
                                                                                            </button>
                                                                                            <div class="ml-4">
                                                                                                <h5 class="mb-0">Google plus</h5>
                                                                                                <p class="mb-0">780 friends</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <button class="btn btn-social-icon btn-linkedin btn-rounded">
                                                                                                <i class="fab fa-linkedin-in"></i>
                                                                                            </button>
                                                                                            <div class="ml-4">
                                                                                                <h5 class="mb-0">Linkedin</h5>
                                                                                                <p class="mb-0">1090 connections</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->
        </div>
        <!-- content-wrapper ends -->
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
            @if(auth()->user()->wallet_address)
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
        });

        $('#topUpModal').on('hidden.bs.modal', function() {
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0px';
            document.body.style.marginRight = '0px';
        });

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
            const availableBalance = {{ $walletBalance ?? 0 }};
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
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length === 0) {
                    throw new Error('Please connect your wallet first');
                }
                
                // USDT contract address on BSC (BEP20)
                const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
                
                // Get the USDT contract ABI (simplified for transfer function)
                const usdtABI = [
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
@endsection
