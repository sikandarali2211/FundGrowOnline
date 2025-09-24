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
                <!-- Wallet Connection Status (Full Width) -->
                <div class="col-lg-12">
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
                                    <h5 class="mb-0 "style="color: #3bd17a">Wallet Not Connected</h5>
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
                            <h2 style="color: #3bd17a" class="font-weight-bold mb-0">${{ $walletBalance ?? '0.00' }}</h2>
                        </div>
                        <div class="d-flex flex-column">
                            <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                                data-toggle="modal" data-target="#topUpModal">
                                <i class="fas fa-arrow-down mr-1"></i> Top Up
                            </button>
                            <button class="btn btn-light btn-sm" style="border-radius: 20px; min-width: 120px;">
                                <i class="fas fa-arrow-up mr-1"></i> Cash Out
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side Wallet Cards -->
                <div class="col-md-6 col-lg-6">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('balance-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3  h-100 clickable-card">
                                    <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                                    <h6 class="mb-1">Balance Wallet</h6>
                                    <span class="font-weight-bold">${{ $walletBalance ?? '0.00' }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pool-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3  h-100 clickable-card">
                                    <i class="fas fa-box fa-2x text-success mb-2"></i>
                                    <h6 class="mb-1">Pool Wallet</h6>
                                    <span class="font-weight-bold">${{ $walletBalance ?? '0.00' }}</span>
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
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pools') }}" class="text-decoration-none">
                                <div class="card text-center p-3  h-100 clickable-card">
                                    <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1">Pools</h6>
                                </div>
                            </a>
                        </div>
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

        <!-- Exchange Modal -->
        <div class="modal fade" id="exchangemodal" tabindex="-1" role="dialog" aria-labelledby="topUpModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="background: #072d42; color: #fff; border-radius: 15px;">
                    <!-- Header -->
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="topUpModalLabel" style="color:#3bd17a;">Exchange Balance</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Body -->
                    <div class="modal-body text-center">
                        <!-- Amount Input -->
                        <form method="POST" action=""> @csrf
                            <div class="mb-4">
                                <label for="amount" class="form-label text-warning">Amount to
                                    Transfer</label>
                                <input type="number" class="form-control" id="amount" name="amount" required
                                    min="1" placeholder="Enter amount to transfer">
                            </div>
                            <!-- Balance Wallet -->
                            <div class="mb-3">
                                <label for="balanceWallet" class="form-label text-warning">Balance
                                    Wallet</label>
                                <input type="text" id="balanceWallet" class="form-control" value="1000 USDT"
                                    readonly>
                            </div>
                            <!-- Pool Wallet -->
                            <div class="mb-3">
                                <label for="poolWallet" class="form-label text-warning">Pool
                                    Wallet</label>
                                <input type="text" id="poolWallet" class="form-control" value="500 USDT" readonly>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success">Transfer</button>
                        </form>
                        <!-- Instructions -->
                        <div class="p-3 rounded text-warning mt-3" style="background: rgba(255,255,255,0.1);">
                            <strong>Important:</strong> Please ensure the transfer amount is valid and check your balance
                            before proceeding.
                        </div>
                    </div> <!-- Footer -->
                    <div class="modal-footer border-0"> <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Close</button> </div>
                </div>
            </div>
        </div> {{-- Exchange modal end --}}

        <div class="row grid-margin ">
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
                        </div>
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
            @if (auth()->user()->wallet_address)
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
    </script>
@endsection
