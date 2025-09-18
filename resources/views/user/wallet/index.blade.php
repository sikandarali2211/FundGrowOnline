@extends('layouts.user')

@section('content')
<<<<<<< HEAD
<div class="main-panel">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-wallet"></i> Wallet Connection & BSC Transactions
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Mobile-First Wallet Connection Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-gradient-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title mb-3">
                                            <i class="fas fa-mobile-alt"></i> Mobile Wallet Connection
                                        </h4>
                                        <p class="card-text mb-4">Connect your mobile wallet to deposit and manage BSC BEP20 tokens</p>

                                        <!-- Mobile Wallet Buttons -->
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <button class="btn btn-light btn-lg w-100" onclick="connectMobileWallet('trust')">
                                                    <i class="fas fa-mobile-alt fa-2x mb-2"></i><br>
                                                    <strong>Trust Wallet</strong><br>
                                                    <small>Mobile App</small>
                                                </button>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <button class="btn btn-light btn-lg w-100" onclick="connectMobileWallet('metamask')">
                                                    <i class="fab fa-ethereum fa-2x mb-2"></i><br>
                                                    <strong>MetaMask</strong><br>
                                                    <small>Mobile App</small>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- QR Code Section for Desktop Users -->
                                        <div class="mt-4" id="qrCodeSection" style="display: none;">
                                            <h6>Scan QR Code with Mobile Wallet</h6>
                                            <div id="qrCodeContainer" class="text-center"></div>
                                            <button class="btn btn-outline-light btn-sm mt-2" onclick="hideQRCode()">Close</button>
                                        </div>

                                        <!-- Alternative Connection Methods -->
                                        <div class="mt-3">
                                            <button class="btn btn-outline-light btn-sm" onclick="showAlternativeMethods()">
                                                <i class="fas fa-cog"></i> Other Methods
                                            </button>
                                        </div>

                                        <!-- Alternative Methods (Hidden by default) -->
                                        <div id="alternativeMethods" class="mt-3" style="display: none;">
                                            <div class="row">
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" onclick="simpleConnect()">
                                                        <i class="fas fa-link"></i><br>Direct
                                                    </button>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" onclick="testConnection()">
                                                        <i class="fas fa-bug"></i><br>Test
                                                    </button>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" onclick="showQRCode()">
                                                        <i class="fas fa-qrcode"></i><br>QR Code
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Wallet Status -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-mobile-alt"></i> Mobile Wallet Status
                                        </h5>
                                        <div id="mobileWalletStatus">
                                            <div class="alert alert-warning">
                                                <h6>📱 Mobile Wallet Required</h6>
                                                <p class="mb-2">For the best experience, use a mobile wallet app:</p>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm w-100">
                                                            <i class="fas fa-mobile-alt"></i> Trust Wallet
                                                        </a>
                                                    </div>
                                                    <div class="col-6">
                                                        <a href="https://metamask.io/download/" target="_blank" class="btn btn-info btn-sm w-100">
                                                            <i class="fab fa-ethereum"></i> MetaMask
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Wallet Status</h5>
                                        <div id="walletStatus">
                                            <p class="mb-1">Status: <span id="connectionStatus">Not Connected</span></p>
                                            <p class="mb-1">Account: <span id="accountAddress">-</span></p>
                                            <p class="mb-1">Network: <span id="networkName">-</span></p>
                                        </div>
                                    </div>
=======
    <style>
        /* Base Glassy Card */
        .glassy-card {
            background: linear-gradient(145deg, #072d42, #061a2a);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            color: #e2e8f0;
            height: 100%;
            /* equal height for all */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .glassy-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.6);
        }

        /* Title */
        .glassy-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3bd17a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Wallet Buttons */
        .wallet-btn {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 110px;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .wallet-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .wallet-text {
            margin-top: 8px;
            text-align: center;
            line-height: 1.2;
        }

        .wallet-text strong {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            color: #111;
        }

        .wallet-text small {
            font-size: 0.75rem;
            color: #555;
        }

        /* Buttons */
        .btn-custom {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-custom-sm {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Balance Text */
        .balance-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3bd17a;
        }

        /* Input Styling */
        .input-group input {
            border-radius: 10px 0 0 10px !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-group button {
            border-radius: 0 10px 10px 0 !important;
        }

        /* Responsive Fix */
        @media(max-width: 768px) {
            .glassy-card {
                margin-bottom: 1rem;
            }

            .wallet-btn {
                height: auto;
                padding: 12px;
            }
        }

        /* Balance Values */
        .balance-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #3bd17a;
        }

        /* Align input + button */
        .input-group input {
            height: 44px;
            font-size: 0.9rem;
        }

        .input-group button {
            height: 44px;
            font-weight: 600;
        }

        /* Refresh / Check buttons */
        .btn-custom-sm {
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
        }


        /* Glassy Send Tokens Card */
        .glassy-card {
            background: linear-gradient(145deg, #072d42, #061a2a);
            border: 1px solid rgba(59, 209, 122, 0.25);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
            color: #e2e8f0;
        }

        /* Card Header Title */
        .glassy-title,
        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #3bd17a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Toggle Buttons */
        .btn-group .btn {
            font-weight: 600;
            border-radius: 10px !important;
            padding: 10px 18px;
            transition: all 0.3s ease;
            color: #3bd17a;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.4);
            gap: 4rem;
        }

        .btn-group .btn:hover,
        .btn-check:checked+.btn {
            background: #3bd17a;
            color: #fff;
            box-shadow: 0 4px 14px rgba(59, 209, 122, 0.4);
        }

        /* Dark Inputs */
        .form-control {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.9rem;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #3bd17a;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.3);
        }

        .form-control::placeholder {
            color: rgba(226, 232, 240, 0.6);
        }

        /* Submit Buttons */
        .btn-primary,
        .btn-success {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
        }

        .btn-primary:hover {
            background: #2563eb;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: #3bd17a;
            border: none;
        }

        .btn-success:hover {
            background: #2bbd65;
            box-shadow: 0 6px 16px rgba(59, 209, 122, 0.4);
        }

        /* Glassy Table */
        .table-dark-glassy {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            background: transparent;
        }

        .table-dark-glassy thead tr {
            background: rgba(59, 209, 122, 0.15);
            color: #3bd17a;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table-dark-glassy thead th {
            border: none;
            padding: 12px;
        }

        .table-dark-glassy tbody tr {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            color: #e2e8f0;
        }

        .table-dark-glassy tbody td {
            border: none;
            padding: 12px;
        }

        .table-dark-glassy tbody tr:hover {
            background: rgba(59, 209, 122, 0.1);
            transition: all 0.3s ease;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-success {
            background: rgba(59, 209, 122, 0.15);
            color: #3bd17a;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .status-failed {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }


        /* Toggle Buttons */
        .btn-send {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.4);
            color: #3bd17a;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-send:hover {
            background: rgba(59, 209, 122, 0.15);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 209, 122, 0.3);
        }

        #sendBnb:checked+label {
            background: #10b981;
            color: #fff !important;
        }

        #sendToken:checked+label {
            background: #3b82f6;
            color: #fff !important;
        }

        /* Mobile spacing fix */
        @media (max-width: 768px) {
            .glassy-card {
                margin-bottom: 1rem !important;
                /* har card ke neeche gap */
            }

            .row.g-4 {
                margin-bottom: 0;
                /* default Bootstrap ka negative margin hatao */
            }
        }
    </style>

    <div class="main-panel" style="margin-top:6rem;  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
        <div class="container-fluid">
            <!-- Row 1: Wallet Connection / Status / Wallet Status -->
            <div class="row g-4">
                <!-- Mobile Wallet Connection -->
                <div class="col-md-4 col-12 mt-4">
                    <div class="glassy-card">
                        <h4 class="glassy-title"><i class="fas fa-mobile-alt"></i> Mobile Wallet Connection</h4>
                        <div class="row g-2">
                            <div class="col-6 ">
                                <button class="wallet-btn" onclick="connectMobileWallet('trust')">
                                    <i class="fas fa-mobile-alt fa-2x"></i>
                                    <div class="wallet-text">
                                        <strong>Trust Wallet</strong>
                                        <small>Mobile App</small>
                                    </div>
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="wallet-btn" onclick="connectMobileWallet('metamask')">
                                    <i class="fab fa-ethereum fa-2x"></i>
                                    <div class="wallet-text">
                                        <strong>MetaMask</strong>
                                        <small>Mobile App</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn btn-outline-light btn-sm" onclick="showAlternativeMethods()">
                                <i class="fas fa-cog"></i> Other Methods
                            </button>
                        </div>
                        <!-- Alternative Methods (Hidden by default) -->
                        <div id="alternativeMethods" class="mt-3" style="display: none;">
                            <div class="row">
                                <div class="col-4">
                                    <button class="btn btn-outline-light btn-sm w-100" onclick="simpleConnect()">
                                        <i class="fas fa-link"></i><br>Direct
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-light btn-sm w-100" onclick="testConnection()">
                                        <i class="fas fa-bug"></i><br>Test
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-light btn-sm w-100" onclick="showQRCode()">
                                        <i class="fas fa-qrcode"></i><br>QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Wallet Status -->
                <div class="col-md-4 col-12 mt-4">
                    <div class="glassy-card">
                        <h4 class="glassy-title"><i class="fas fa-signal"></i> Mobile Wallet Status</h4>
                        <div id="mobileWalletStatus">
                            <div class="alert alert-warning">
                                <strong>📱 Mobile Wallet Required</strong><br>
                                For best experience, use a mobile wallet app.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Status -->
                <div class="col-md-4 col-12 mt-4">
                    <div class="glassy-card">
                        <h4 class="glassy-title"><i class="fas fa-wallet"></i> Wallet Status</h4>
                        <p>Status: <span id="connectionStatus">Not Connected</span></p>
                        <p>Account: <span id="accountAddress">-</span></p>
                        <p>Network: <span id="networkName">-</span></p>
                    </div>
                </div>
            </div>


            <!-- Row 2: BNB & Token Balance -->
            <div class="row g-4 mt-4">
                <!-- BNB Balance -->
                <div class="col-md-6 col-12">
                    <div class="glassy-card">
                        <h4 class="glassy-title"><i class="fas fa-coins"></i> BNB Balance</h4>
                        <div class="d-flex flex-column align-items-start justify-content-between h-100">
                            <h3 id="bnbBalance" class="balance-value mb-3">0.00 BNB</h3>
                            <button id="refreshBnbBtn" class="btn btn-outline-light btn-custom-sm">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Token Balance -->
                <div class="col-md-6 col-12 mt-4">
                    <div class="glassy-card">
                        <h4 class="glassy-title"><i class="fab fa-ethereum"></i> Token Balance</h4>
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="input-group mb-3">
                                <input type="text" id="tokenAddress" class="form-control"
                                    placeholder="Token Contract Address">
                                <button id="checkTokenBtn" class="btn btn-outline-light btn-custom-sm">
                                    <i class="fas fa-search"></i> Check
                                </button>
                            </div>
                            <h3 id="tokenBalance" class="balance-value">0.00 Tokens</h3>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Send Section -->
            <div class="col-12 mt-4">
                <div class="glassy-card">
                    <h5 class="card-title">
                        <i class="fas fa-paper-plane"></i> Send Tokens
                    </h5>
                    <div class="row">
                        <!-- Toggle Buttons -->
                        <div class="col-12 mb-3">
                            <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap; margin-top:8px;">

                                <!-- Send BNB -->
                                <input type="radio" class="btn-check" name="sendType" id="sendBnb" value="bnb"
                                    checked>
                                <label for="sendBnb"
                                    style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 20px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; border:2px solid #3b82f6; color:#3b82f6; background:transparent; transition:all 0.3s ease;">
                                    <i class="fas fa-coins"></i>
                                    <span>Send BNB</span>
                                </label>

                                <!-- Send Token -->
                                <input type="radio" class="btn-check" name="sendType" id="sendToken" value="token">
                                <label for="sendToken"
                                    style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 20px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; border:2px solid #10b981; color:#10b981; background:transparent; transition:all 0.3s ease;">
                                    <i class="fab fa-ethereum"></i>
                                    <span>Send Token</span>
                                </label>
                            </div>
                        </div>


                        <!-- BNB Send Form -->
                        <div class="col-12 mt-4" id="bnbSendForm">
                            <form id="sendBnbForm">
                                <div class="mb-3">
                                    <label class="form-label">Recipient Address</label>
                                    <input type="text" id="bnbRecipient" class="form-control" placeholder="0x...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount (BNB)</label>
                                    <input type="number" id="bnbAmount" class="form-control" step="0.000001"
                                        placeholder="0.00">
>>>>>>> b51f49c (Wallet Index update)
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane"></i> Send BNB
                                </button>
                            </form>
                        </div>

                        <!-- Token Send Form -->
                        <div class="col-12" id="tokenSendForm" style="display: none;">
                            <form id="sendTokenForm">
                                <div class="mb-3">
                                    <label class="form-label">Token Contract Address</label>
                                    <input type="text" id="tokenContract" class="form-control" placeholder="0x...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Recipient Address</label>
                                    <input type="text" id="tokenRecipient" class="form-control" placeholder="0x...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" id="tokenAmount" class="form-control" step="0.000001"
                                        placeholder="0.00">
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane"></i> Send Token
                                </button>
                            </form>
                        </div>
<<<<<<< HEAD

                        <!-- Balance Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">BNB Balance</h5>
                                        <h3 id="bnbBalance" class="text-primary">0.00 BNB</h3>
                                        <button id="refreshBnbBtn" class="btn btn-outline-primary btn-sm">Refresh</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Token Balance</h5>
                                        <div class="input-group mb-2">
                                            <input type="text" id="tokenAddress" class="form-control" placeholder="Token Contract Address">
                                            <button id="checkTokenBtn" class="btn btn-outline-secondary">Check</button>
                                        </div>
                                        <h3 id="tokenBalance" class="text-success">0.00 Tokens</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile-First Transaction Section -->
                        <div class="row">
                            <!-- Mobile Deposit Section -->
                            <div class="col-12 mb-4">
                                <div class="card bg-gradient-success text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title mb-3">
                                            <i class="fas fa-mobile-alt"></i> Mobile Deposit
                                        </h4>
                                        <p class="card-text mb-4">Deposit BNB or BEP20 tokens directly from your mobile wallet</p>

                                        <!-- Deposit Address -->
                                        <div class="mb-4">
                                            <h6>Your Deposit Address</h6>
                                            <div class="input-group">
                                                <input type="text" id="depositAddress" class="form-control" readonly
                                                    value="Connect wallet to get address">
                                                <button class="btn btn-light" onclick="copyDepositAddress()">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Send BNB or BEP20 tokens to this address</small>
                                        </div>

                                        <!-- Quick Deposit Buttons -->
                                        <div class="row">
                                            <div class="col-6">
                                                <button class="btn btn-light btn-lg w-100" onclick="openMobileWallet('bnb')">
                                                    <i class="fas fa-coins fa-2x mb-2"></i><br>
                                                    <strong>Deposit BNB</strong><br>
                                                    <small>Native Token</small>
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-light btn-lg w-100" onclick="openMobileWallet('token')">
                                                    <i class="fab fa-ethereum fa-2x mb-2"></i><br>
                                                    <strong>Deposit Token</strong><br>
                                                    <small>BEP20 Token</small>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Send Section -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-paper-plane"></i> Send Tokens
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Mobile-Optimized Send Form -->
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="sendType" id="sendBnb" value="bnb" checked>
                                                    <label class="btn btn-outline-primary" for="sendBnb">
                                                        <i class="fas fa-coins"></i> Send BNB
                                                    </label>

                                                    <input type="radio" class="btn-check" name="sendType" id="sendToken" value="token">
                                                    <label class="btn btn-outline-success" for="sendToken">
                                                        <i class="fab fa-ethereum"></i> Send Token
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- BNB Send Form -->
                                            <div class="col-12" id="bnbSendForm">
                                                <form id="sendBnbForm">
                                                    <div class="mb-3">
                                                        <label class="form-label">Recipient Address</label>
                                                        <input type="text" id="bnbRecipient" class="form-control" placeholder="0x...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount (BNB)</label>
                                                        <input type="number" id="bnbAmount" class="form-control" step="0.000001" placeholder="0.00">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <i class="fas fa-paper-plane"></i> Send BNB
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Token Send Form -->
                                            <div class="col-12" id="tokenSendForm" style="display: none;">
                                                <form id="sendTokenForm">
                                                    <div class="mb-3">
                                                        <label class="form-label">Token Contract Address</label>
                                                        <input type="text" id="tokenContract" class="form-control" placeholder="0x...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Recipient Address</label>
                                                        <input type="text" id="tokenRecipient" class="form-control" placeholder="0x...">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" id="tokenAmount" class="form-control" step="0.000001" placeholder="0.00">
                                                    </div>
                                                    <button type="submit" class="btn btn-success w-100">
                                                        <i class="fas fa-paper-plane"></i> Send Token
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction History -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Transaction History</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="transactionHistory">
                                            <p class="text-muted">No transactions yet</p>
                                        </div>
                                    </div>
                                </div>
=======
                    </div>
                </div>
            </div>


            <!-- Transaction History -->
            <div class="glassy-card mt-4 mb-4">
                <h4 class="glassy-title"><i class="fas fa-history"></i> Transaction History</h4>
                <div id="transactionHistory">
                    <table class="table table-dark-glassy table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tx Hash</th>
                                <th scope="col">Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-white">No transactions yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <!-- Transaction Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="transactionStatus">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
>>>>>>> b51f49c (Wallet Index update)
                            </div>
                            <p class="mt-2">Processing transaction...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
    <script>
        // Wait for ethers to load
        window.addEventListener('load', function() {
            if (typeof ethers === 'undefined') {
                console.error('Ethers.js not loaded');
                return;
            }

<<<<<<< HEAD
<script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
<script>
    // Wait for ethers to load
    window.addEventListener('load', function() {
        if (typeof ethers === 'undefined') {
            console.error('Ethers.js not loaded');
            return;
        }

        // Load wallet service after ethers is available
        const script = document.createElement('script');
        script.src = "{{ asset('js/wallet-service.js') }}";
        script.onload = function() {
            initializeWalletApp();
        };
        document.head.appendChild(script);
    });

    // Test function for debugging
    function testConnection() {
        console.log('Test button clicked');
        console.log('Ethers available:', typeof ethers !== 'undefined');
        console.log('Wallet service available:', typeof window.walletService !== 'undefined');
        console.log('Ethereum available:', typeof window.ethereum !== 'undefined');

        // Show test results in UI
        const testResults = `
=======
            // Load wallet service after ethers is available
            const script = document.createElement('script');
            script.src = "{{ asset('js/wallet-service.js') }}";
            script.onload = function() {
                initializeWalletApp();
            };
            document.head.appendChild(script);
        });

        // Test function for debugging
        function testConnection() {
            console.log('Test button clicked');
            console.log('Ethers available:', typeof ethers !== 'undefined');
            console.log('Wallet service available:', typeof window.walletService !== 'undefined');
            console.log('Ethereum available:', typeof window.ethereum !== 'undefined');

            // Show test results in UI
            const testResults = `
>>>>>>> b51f49c (Wallet Index update)
        <div class="alert alert-info">
            <h6>Test Results:</h6>
            <p>Ethers.js: ${typeof ethers !== 'undefined' ? '✅ Loaded' : '❌ Not Loaded'}</p>
            <p>Wallet Service: ${typeof window.walletService !== 'undefined' ? '✅ Loaded' : '❌ Not Loaded'}</p>
            <p>Ethereum: ${typeof window.ethereum !== 'undefined' ? '✅ Available' : '❌ Not Available'}</p>
        </div>
    `;

<<<<<<< HEAD
        // Show results in wallet status area
        const walletStatus = document.getElementById('walletStatus');
        walletStatus.innerHTML = testResults;

        if (typeof window.walletService !== 'undefined') {
            console.log('Wallet service found, testing connection...');
            window.walletService.connectWallet().then(result => {
                console.log('Connection result:', result);
            });
        } else {
            console.log('Wallet service not loaded yet');
        }
    }

    // Mobile wallet connection function
    window.connectMobileWallet = async function(walletType) {
        console.log('Mobile wallet connection:', walletType);

        // Check if we're on mobile
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        if (isMobile) {
            // Mobile device - direct connection
            await connectWalletDirect();
        } else {
            // Desktop - show QR code or redirect
            if (walletType === 'trust') {
                showTrustWalletInstructions();
            } else if (walletType === 'metamask') {
                showMetaMaskInstructions();
            }
        }
    };

    // Show Trust Wallet instructions for desktop users
    function showTrustWalletInstructions() {
        document.getElementById('mobileWalletStatus').innerHTML = `
        <div class="alert alert-info">
            <h6>📱 Trust Wallet Setup for Desktop</h6>
            <p class="mb-3">To use Trust Wallet on desktop:</p>
            <ol class="text-start">
                <li>Install Trust Wallet mobile app</li>
                <li>Open Trust Wallet in mobile browser</li>
                <li>Or install Trust Wallet Chrome extension</li>
            </ol>
            <div class="row">
                <div class="col-6">
                    <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-mobile-alt"></i> Mobile App
                    </a>
                </div>
                <div class="col-6">
                    <a href="https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph" target="_blank" class="btn btn-info btn-sm w-100">
                        <i class="fab fa-chrome"></i> Chrome Extension
                    </a>
                </div>
            </div>
=======
            // Show results in wallet status area
            const walletStatus = document.getElementById('walletStatus');
            walletStatus.innerHTML = testResults;

            if (typeof window.walletService !== 'undefined') {
                console.log('Wallet service found, testing connection...');
                window.walletService.connectWallet().then(result => {
                    console.log('Connection result:', result);
                });
            } else {
                console.log('Wallet service not loaded yet');
            }
        }

        // Mobile wallet connection function
        window.connectMobileWallet = async function(walletType) {
            console.log('Mobile wallet connection:', walletType);

            // Check if we're on mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                .userAgent);

            if (isMobile) {
                // Mobile device - direct connection
                await connectWalletDirect();
            } else {
                // Desktop - show QR code or redirect
                if (walletType === 'trust') {
                    showTrustWalletInstructions();
                } else if (walletType === 'metamask') {
                    showMetaMaskInstructions();
                }
            }
        };

        // Show Trust Wallet instructions for desktop users
        function showTrustWalletInstructions() {
            document.getElementById('mobileWalletStatus').innerHTML = `
    <div class="alert alert-info" style="color:#fff; background:#0b2c44; border-radius:10px; padding:15px;">
        <h6>📱 Trust Wallet Setup for Desktop</h6>
        <p class="mb-3">To use Trust Wallet on desktop:</p>
        <ol class="text-start">
            <li>Install Trust Wallet mobile app</li>
            <li>Open Trust Wallet in mobile browser</li>
            <li>Or install Trust Wallet Chrome extension</li>
        </ol>
        <div style="display:flex; gap:12px; margin-top:12px; flex-wrap:wrap;">
            <a href="https://trustwallet.com/" target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#f59e0b; white-space:nowrap; flex:1; text-align:center;">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile App</span>
            </a>

            <a href="https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph"
               target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#3b82f6; white-space:nowrap; flex:1; text-align:center;">
                <i class="fab fa-chrome"></i>
                <span>Chrome Extension</span>
            </a>
>>>>>>> b51f49c (Wallet Index update)
        </div>
    </div>
    `;
<<<<<<< HEAD
    }

    // Show MetaMask instructions for desktop users
    function showMetaMaskInstructions() {
        document.getElementById('mobileWalletStatus').innerHTML = `
        <div class="alert alert-info">
            <h6>🦊 MetaMask Setup for Desktop</h6>
            <p class="mb-3">To use MetaMask on desktop:</p>
            <ol class="text-start">
                <li>Install MetaMask mobile app</li>
                <li>Open MetaMask in mobile browser</li>
                <li>Or install MetaMask Chrome extension</li>
            </ol>
            <div class="row">
                <div class="col-6">
                    <a href="https://metamask.io/download/" target="_blank" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-mobile-alt"></i> Mobile App
                    </a>
                </div>
                <div class="col-6">
                    <a href="https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn" target="_blank" class="btn btn-info btn-sm w-100">
                        <i class="fab fa-chrome"></i> Chrome Extension
                    </a>
                </div>
            </div>
=======
        }

        // Show MetaMask instructions for desktop users
        function showMetaMaskInstructions() {
            document.getElementById('mobileWalletStatus').innerHTML = `
    <div class="alert alert-info" style="color:#fff; background:#0b2c44; border-radius:10px; padding:15px;">
        <h6>🦊 MetaMask Setup for Desktop</h6>
        <p class="mb-3">To use MetaMask on desktop:</p>
        <ol class="text-start">
            <li>Install MetaMask mobile app</li>
            <li>Open MetaMask in mobile browser</li>
            <li>Or install MetaMask Chrome extension</li>
        </ol>
        <div style="display:flex; gap:12px; margin-top:12px; flex-wrap:wrap;">
            <a href="https://metamask.io/download/" target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#f59e0b; white-space:nowrap; flex:1; text-align:center;">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile App</span>
            </a>

            <a href="https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn"
               target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#3b82f6; white-space:nowrap; flex:1; text-align:center;">
                <i class="fab fa-chrome"></i>
                <span>Chrome Extension</span>
            </a>
>>>>>>> b51f49c (Wallet Index update)
        </div>
    </div>
    `;
<<<<<<< HEAD
    }

    // Show alternative methods
    function showAlternativeMethods() {
        const methods = document.getElementById('alternativeMethods');
        methods.style.display = methods.style.display === 'none' ? 'block' : 'none';
    }

    // Show QR code for mobile connection
    function showQRCode() {
        const qrSection = document.getElementById('qrCodeSection');
        const qrContainer = document.getElementById('qrCodeContainer');

        // Generate QR code for current page URL
        const currentUrl = window.location.href;
        qrContainer.innerHTML = `
=======
        }

        // Show alternative methods
        function showAlternativeMethods() {
            const methods = document.getElementById('alternativeMethods');
            methods.style.display = methods.style.display === 'none' ? 'block' : 'none';
        }

        // Show QR code for mobile connection
        function showQRCode() {
            const qrSection = document.getElementById('qrCodeSection');
            const qrContainer = document.getElementById('qrCodeContainer');

            // Generate QR code for current page URL
            const currentUrl = window.location.href;
            qrContainer.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
        <div class="alert alert-light">
            <h6>📱 Scan with Mobile Wallet</h6>
            <p>Open this URL in your mobile wallet browser:</p>
            <code>${currentUrl}</code>
            <div class="mt-2">
                <small class="text-muted">Or copy the URL and paste it in your mobile wallet's browser</small>
            </div>
        </div>
    `;

<<<<<<< HEAD
        qrSection.style.display = 'block';
    }

    // Hide QR code
    function hideQRCode() {
        document.getElementById('qrCodeSection').style.display = 'none';
    }

    // Mobile deposit functions
    window.openMobileWallet = function(type) {
        if (!currentAccount) {
            showErrorMessage('Please connect your wallet first');
            return;
        }

        if (type === 'bnb') {
            // Open mobile wallet to send BNB
            const depositAddress = currentAccount;
            const bscScanUrl = `https://bscscan.com/address/${depositAddress}`;

            // Try to open in mobile wallet
            const walletUrl = `trust://wc?uri=${encodeURIComponent(window.location.href)}`;
            window.open(walletUrl, '_blank');

            // Show instructions
            document.getElementById('mobileWalletStatus').innerHTML = `
=======
            qrSection.style.display = 'block';
        }

        // Hide QR code
        function hideQRCode() {
            document.getElementById('qrCodeSection').style.display = 'none';
        }

        // Mobile deposit functions
        window.openMobileWallet = function(type) {
            if (!currentAccount) {
                showErrorMessage('Please connect your wallet first');
                return;
            }

            if (type === 'bnb') {
                // Open mobile wallet to send BNB
                const depositAddress = currentAccount;
                const bscScanUrl = `https://bscscan.com/address/${depositAddress}`;

                // Try to open in mobile wallet
                const walletUrl = `trust://wc?uri=${encodeURIComponent(window.location.href)}`;
                window.open(walletUrl, '_blank');

                // Show instructions
                document.getElementById('mobileWalletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-info">
                <h6>📱 Deposit BNB Instructions</h6>
                <p class="mb-2">To deposit BNB:</p>
                <ol class="text-start">
                    <li>Open your mobile wallet app</li>
                    <li>Go to BNB (BSC Network)</li>
                    <li>Send BNB to this address:</li>
                </ol>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="${depositAddress}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${depositAddress}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <a href="${bscScanUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> View on BSCScan
                    </a>
                </div>
            </div>
        `;
<<<<<<< HEAD
        } else if (type === 'token') {
            // Open mobile wallet to send BEP20 token
            const depositAddress = currentAccount;

            document.getElementById('mobileWalletStatus').innerHTML = `
=======
            } else if (type === 'token') {
                // Open mobile wallet to send BEP20 token
                const depositAddress = currentAccount;

                document.getElementById('mobileWalletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-info">
                <h6>📱 Deposit BEP20 Token Instructions</h6>
                <p class="mb-2">To deposit BEP20 tokens:</p>
                <ol class="text-start">
                    <li>Open your mobile wallet app</li>
                    <li>Go to the token you want to send</li>
                    <li>Make sure it's on BSC network</li>
                    <li>Send to this address:</li>
                </ol>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="${depositAddress}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${depositAddress}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Note: Make sure the token is BEP20 (BSC) version, not ERC20 (Ethereum)</small>
                </div>
            </div>
        `;
<<<<<<< HEAD
        }
    };

    // Copy deposit address
    window.copyDepositAddress = function() {
        if (currentAccount) {
            copyToClipboard(currentAccount);
            showSuccessMessage('Deposit address copied to clipboard!');
        } else {
            showErrorMessage('Please connect your wallet first');
        }
    };

    // Copy to clipboard utility
    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                console.log('Copied to clipboard:', text);
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    }

    // Simple connect function that definitely works
    window.simpleConnect = async function() {
        console.log('Simple connect called');

        // Check if any Web3 wallet is installed (MetaMask, TrustWallet, etc.)
        if (typeof window.ethereum === 'undefined') {
            document.getElementById('walletStatus').innerHTML = `
=======
            }
        };

        // Copy deposit address
        window.copyDepositAddress = function() {
            if (currentAccount) {
                copyToClipboard(currentAccount);
                showSuccessMessage('Deposit address copied to clipboard!');
            } else {
                showErrorMessage('Please connect your wallet first');
            }
        };

        // Copy to clipboard utility
        function copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    console.log('Copied to clipboard:', text);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
        }

        // Simple connect function that definitely works
        window.simpleConnect = async function() {
            console.log('Simple connect called');

            // Check if any Web3 wallet is installed (MetaMask, TrustWallet, etc.)
            if (typeof window.ethereum === 'undefined') {
                document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-danger">
                <h6>Web3 Wallet Not Found</h6>
                <p>Please install a Web3 wallet:</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="https://metamask.io/download/" target="_blank" class="btn btn-danger btn-sm">
                        <i class="fab fa-ethereum"></i> MetaMask
                    </a>
                    <a href="https://trustwallet.com/" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-mobile-alt"></i> Trust Wallet
                    </a>
                    <a href="https://www.coinbase.com/wallet" target="_blank" class="btn btn-info btn-sm">
                        <i class="fas fa-wallet"></i> Coinbase Wallet
                    </a>
                </div>
            </div>
        `;
<<<<<<< HEAD
            return;
        }

        try {
            // Detect wallet type
            let walletType = 'Unknown';
            if (window.ethereum.isMetaMask) {
                walletType = 'MetaMask';
            } else if (window.ethereum.isTrust) {
                walletType = 'Trust Wallet';
            } else if (window.ethereum.isCoinbaseWallet) {
                walletType = 'Coinbase Wallet';
            }

            console.log('Detected wallet:', walletType);

            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length > 0) {
                // Switch to BSC network
                try {
                    await window.ethereum.request({
                        method: 'wallet_switchEthereumChain',
                        params: [{
                            chainId: '0x38'
                        }], // BSC Mainnet
                    });
                } catch (switchError) {
                    // If BSC network is not added, add it
                    if (switchError.code === 4902) {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [{
                                chainId: '0x38',
                                chainName: 'Binance Smart Chain',
                                nativeCurrency: {
                                    name: 'BNB',
                                    symbol: 'BNB',
                                    decimals: 18,
                                },
                                rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                blockExplorerUrls: ['https://bscscan.com/'],
                            }],
                        });
                    }
                }

                // Update UI
                document.getElementById('connectionStatus').textContent = 'Connected';
                document.getElementById('accountAddress').textContent = accounts[0];
                document.getElementById('networkName').textContent = 'BSC Mainnet';

                // Update button
                const connectBtn = document.getElementById('connectWalletBtn');
                connectBtn.textContent = 'Disconnect';
                connectBtn.className = 'btn btn-danger';

                // Show success message
                document.getElementById('walletStatus').innerHTML = `
=======
                return;
            }

            try {
                // Detect wallet type
                let walletType = 'Unknown';
                if (window.ethereum.isMetaMask) {
                    walletType = 'MetaMask';
                } else if (window.ethereum.isTrust) {
                    walletType = 'Trust Wallet';
                } else if (window.ethereum.isCoinbaseWallet) {
                    walletType = 'Coinbase Wallet';
                }

                console.log('Detected wallet:', walletType);

                // Request account access
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });

                if (accounts.length > 0) {
                    // Switch to BSC network
                    try {
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{
                                chainId: '0x38'
                            }], // BSC Mainnet
                        });
                    } catch (switchError) {
                        // If BSC network is not added, add it
                        if (switchError.code === 4902) {
                            await window.ethereum.request({
                                method: 'wallet_addEthereumChain',
                                params: [{
                                    chainId: '0x38',
                                    chainName: 'Binance Smart Chain',
                                    nativeCurrency: {
                                        name: 'BNB',
                                        symbol: 'BNB',
                                        decimals: 18,
                                    },
                                    rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                    blockExplorerUrls: ['https://bscscan.com/'],
                                }],
                            });
                        }
                    }

                    // Update UI
                    document.getElementById('connectionStatus').textContent = 'Connected';
                    document.getElementById('accountAddress').textContent = accounts[0];
                    document.getElementById('networkName').textContent = 'BSC Mainnet';

                    // Update button
                    const connectBtn = document.getElementById('connectWalletBtn');
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.className = 'btn btn-danger';

                    // Show success message
                    document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-success">
                    <h6>✅ Wallet Connected Successfully!</h6>
                    <p><strong>Wallet:</strong> ${walletType}</p>
                    <p><strong>Account:</strong> ${accounts[0]}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                </div>
            `;

<<<<<<< HEAD
                console.log('Wallet connected:', accounts[0], 'Type:', walletType);
            }
        } catch (error) {
            console.error('Connection error:', error);
            document.getElementById('walletStatus').innerHTML = `
=======
                    console.log('Wallet connected:', accounts[0], 'Type:', walletType);
                }
            } catch (error) {
                console.error('Connection error:', error);
                document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-danger">
                <h6>❌ Connection Failed</h6>
                <p>Error: ${error.message}</p>
            </div>
        `;
<<<<<<< HEAD
        }
    };

    // Trust Wallet specific connection
    window.connectTrustWallet = async function() {
        console.log('Trust Wallet connection called');

        // Check if Trust Wallet is available
        if (typeof window.ethereum === 'undefined') {
            document.getElementById('walletStatus').innerHTML = `
=======
            }
        };

        // Trust Wallet specific connection
        window.connectTrustWallet = async function() {
            console.log('Trust Wallet connection called');

            // Check if Trust Wallet is available
            if (typeof window.ethereum === 'undefined') {
                document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-info">
                <h6>📱 Trust Wallet Setup Required</h6>
                <p>To use Trust Wallet with this DApp:</p>
                <ol class="text-start">
                    <li>Install Trust Wallet mobile app</li>
                    <li>Open Trust Wallet in mobile browser</li>
                    <li>Or use Trust Wallet browser extension</li>
                </ol>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="https://trustwallet.com/" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-mobile-alt"></i> Download Trust Wallet
                    </a>
                    <a href="https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph" target="_blank" class="btn btn-info btn-sm">
                        <i class="fab fa-chrome"></i> Chrome Extension
                    </a>
                </div>
            </div>
        `;
<<<<<<< HEAD
            return;
        }

        // Check if it's Trust Wallet
        if (!window.ethereum.isTrust) {
            document.getElementById('walletStatus').innerHTML = `
=======
                return;
            }

            // Check if it's Trust Wallet
            if (!window.ethereum.isTrust) {
                document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-warning">
                <h6>⚠️ Trust Wallet Not Detected</h6>
                <p>Please make sure you're using Trust Wallet browser or have Trust Wallet extension installed.</p>
                <p>Current wallet: ${window.ethereum.isMetaMask ? 'MetaMask' : 'Other'}</p>
            </div>
        `;
<<<<<<< HEAD
            return;
        }

        try {
            // Connect to Trust Wallet
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length > 0) {
                // Switch to BSC network
                try {
                    await window.ethereum.request({
                        method: 'wallet_switchEthereumChain',
                        params: [{
                            chainId: '0x38'
                        }], // BSC Mainnet
                    });
                } catch (switchError) {
                    // Add BSC network if not present
                    if (switchError.code === 4902) {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [{
                                chainId: '0x38',
                                chainName: 'Binance Smart Chain',
                                nativeCurrency: {
                                    name: 'BNB',
                                    symbol: 'BNB',
                                    decimals: 18,
                                },
                                rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                blockExplorerUrls: ['https://bscscan.com/'],
                            }],
                        });
                    }
                }

                // Update UI
                document.getElementById('connectionStatus').textContent = 'Connected';
                document.getElementById('accountAddress').textContent = accounts[0];
                document.getElementById('networkName').textContent = 'BSC Mainnet';

                // Update button
                const connectBtn = document.getElementById('connectWalletBtn');
                connectBtn.textContent = 'Disconnect';
                connectBtn.className = 'btn btn-danger';

                // Show success message
                document.getElementById('walletStatus').innerHTML = `
=======
                return;
            }

            try {
                // Connect to Trust Wallet
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });

                if (accounts.length > 0) {
                    // Switch to BSC network
                    try {
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{
                                chainId: '0x38'
                            }], // BSC Mainnet
                        });
                    } catch (switchError) {
                        // Add BSC network if not present
                        if (switchError.code === 4902) {
                            await window.ethereum.request({
                                method: 'wallet_addEthereumChain',
                                params: [{
                                    chainId: '0x38',
                                    chainName: 'Binance Smart Chain',
                                    nativeCurrency: {
                                        name: 'BNB',
                                        symbol: 'BNB',
                                        decimals: 18,
                                    },
                                    rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                    blockExplorerUrls: ['https://bscscan.com/'],
                                }],
                            });
                        }
                    }

                    // Update UI
                    document.getElementById('connectionStatus').textContent = 'Connected';
                    document.getElementById('accountAddress').textContent = accounts[0];
                    document.getElementById('networkName').textContent = 'BSC Mainnet';

                    // Update button
                    const connectBtn = document.getElementById('connectWalletBtn');
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.className = 'btn btn-danger';

                    // Show success message
                    document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-success">
                    <h6>✅ Trust Wallet Connected Successfully!</h6>
                    <p><strong>Wallet:</strong> Trust Wallet</p>
                    <p><strong>Account:</strong> ${accounts[0]}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                    <small class="text-muted">You can now send/receive BSC BEP20 tokens!</small>
                </div>
            `;

<<<<<<< HEAD
                console.log('Trust Wallet connected:', accounts[0]);
            }
        } catch (error) {
            console.error('Trust Wallet connection error:', error);
            document.getElementById('walletStatus').innerHTML = `
=======
                    console.log('Trust Wallet connected:', accounts[0]);
                }
            } catch (error) {
                console.error('Trust Wallet connection error:', error);
                document.getElementById('walletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
            <div class="alert alert-danger">
                <h6>❌ Trust Wallet Connection Failed</h6>
                <p>Error: ${error.message}</p>
                <small class="text-muted">Make sure Trust Wallet is unlocked and try again.</small>
            </div>
        `;
<<<<<<< HEAD
        }
    };

    function initializeWalletApp() {
        document.addEventListener('DOMContentLoaded', function() {
            const connectBtn = document.getElementById('connectWalletBtn');
            const refreshBnbBtn = document.getElementById('refreshBnbBtn');
            const checkTokenBtn = document.getElementById('checkTokenBtn');
            const sendBnbForm = document.getElementById('sendBnbForm');
            const sendTokenForm = document.getElementById('sendTokenForm');

            let currentAccount = null;

            // Check wallet availability on page load
            function checkWalletAvailability() {
                if (typeof window.ethereum === 'undefined') {
                    // Show helpful message
                    const walletStatus = document.getElementById('walletStatus');
                    walletStatus.innerHTML = `
=======
            }
        };

        function initializeWalletApp() {
            document.addEventListener('DOMContentLoaded', function() {
                const connectBtn = document.getElementById('connectWalletBtn');
                const refreshBnbBtn = document.getElementById('refreshBnbBtn');
                const checkTokenBtn = document.getElementById('checkTokenBtn');
                const sendBnbForm = document.getElementById('sendBnbForm');
                const sendTokenForm = document.getElementById('sendTokenForm');

                let currentAccount = null;

                // Check wallet availability on page load
                function checkWalletAvailability() {
                    if (typeof window.ethereum === 'undefined') {
                        // Show helpful message
                        const walletStatus = document.getElementById('walletStatus');
                        walletStatus.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-warning">
                    <h6>Wallet Not Detected</h6>
                    <p class="mb-2">To use this DApp, please install a Web3 wallet:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://metamask.io/download/" target="_blank" class="btn btn-sm btn-warning">
                            <i class="fab fa-ethereum"></i> Install MetaMask
                        </a>
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-mobile-alt"></i> Trust Wallet
                        </a>
                    </div>
                    <small class="text-muted">After installation, refresh this page.</small>
                </div>
            `;
<<<<<<< HEAD
                }
            }

            // Run check on page load
            checkWalletAvailability();

            // Mobile form switching
            document.querySelectorAll('input[name="sendType"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const bnbForm = document.getElementById('bnbSendForm');
                    const tokenForm = document.getElementById('tokenSendForm');

                    if (this.value === 'bnb') {
                        bnbForm.style.display = 'block';
                        tokenForm.style.display = 'none';
                    } else {
                        bnbForm.style.display = 'none';
                        tokenForm.style.display = 'block';
                    }
                });
            });

            // Add direct connection method as fallback
            window.connectWalletDirect = async function() {
                console.log('Direct wallet connection called');

                if (typeof window.ethereum === 'undefined') {
                    showErrorMessage('No Web3 wallet detected. Please install MetaMask or similar wallet.');
                    return;
                }

                try {
                    const accounts = await window.ethereum.request({
                        method: 'eth_requestAccounts'
                    });
                    if (accounts.length > 0) {
                        currentAccount = accounts[0];
                        updateWalletStatus();
                        loadBalances();
                        showSuccessMessage('Wallet connected successfully!');
                    }
                } catch (error) {
                    console.error('Direct connection error:', error);
                    showErrorMessage('Connection failed: ' + error.message);
                }
            };

            // Utility functions for messages
            function showSuccessMessage(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            function showErrorMessage(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

                // Auto remove after 8 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 8000);
            }

            // Connect wallet
            connectBtn.addEventListener('click', async function() {
                console.log('Connect wallet button clicked');

                // Check if wallet service is available
                if (typeof window.walletService === 'undefined') {
                    showErrorMessage('Wallet service not loaded. Please refresh the page.');
                    return;
                }

                try {
                    const result = await window.walletService.connectWallet();
                    if (result.success) {
                        currentAccount = result.account;
                        updateWalletStatus();
                        loadBalances();
                        showSuccessMessage('Wallet connected successfully!');
                    } else {
                        // Error message already shown in modal
                        console.log('Connection failed:', result.error);
                    }
                } catch (error) {
                    console.error('Connection error:', error);
                    showErrorMessage('Connection failed: ' + error.message);
                }
            });

            // Update wallet status display
            function updateWalletStatus() {
                if (currentAccount) {
                    document.getElementById('connectionStatus').textContent = 'Connected';
                    document.getElementById('accountAddress').textContent = currentAccount;
                    document.getElementById('networkName').textContent = 'BSC Mainnet';
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.className = 'btn btn-danger';

                    // Update deposit address
                    document.getElementById('depositAddress').value = currentAccount;

                    // Update mobile wallet status
                    document.getElementById('mobileWalletStatus').innerHTML = `
=======
                    }
                }

                // Run check on page load
                checkWalletAvailability();

                // Mobile form switching
                document.querySelectorAll('input[name="sendType"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const bnbForm = document.getElementById('bnbSendForm');
                        const tokenForm = document.getElementById('tokenSendForm');

                        if (this.value === 'bnb') {
                            bnbForm.style.display = 'block';
                            tokenForm.style.display = 'none';
                        } else {
                            bnbForm.style.display = 'none';
                            tokenForm.style.display = 'block';
                        }
                    });
                });

                // Add direct connection method as fallback
                window.connectWalletDirect = async function() {
                    console.log('Direct wallet connection called');

                    if (typeof window.ethereum === 'undefined') {
                        showErrorMessage(
                            'No Web3 wallet detected. Please install MetaMask or similar wallet.');
                        return;
                    }

                    try {
                        const accounts = await window.ethereum.request({
                            method: 'eth_requestAccounts'
                        });
                        if (accounts.length > 0) {
                            currentAccount = accounts[0];
                            updateWalletStatus();
                            loadBalances();
                            showSuccessMessage('Wallet connected successfully!');
                        }
                    } catch (error) {
                        console.error('Direct connection error:', error);
                        showErrorMessage('Connection failed: ' + error.message);
                    }
                };

                // Utility functions for messages
                function showSuccessMessage(message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body')
                        .firstChild);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 5000);
                }

                function showErrorMessage(message) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body')
                        .firstChild);

                    // Auto remove after 8 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 8000);
                }

                // Connect wallet
                connectBtn.addEventListener('click', async function() {
                    console.log('Connect wallet button clicked');

                    // Check if wallet service is available
                    if (typeof window.walletService === 'undefined') {
                        showErrorMessage('Wallet service not loaded. Please refresh the page.');
                        return;
                    }

                    try {
                        const result = await window.walletService.connectWallet();
                        if (result.success) {
                            currentAccount = result.account;
                            updateWalletStatus();
                            loadBalances();
                            showSuccessMessage('Wallet connected successfully!');
                        } else {
                            // Error message already shown in modal
                            console.log('Connection failed:', result.error);
                        }
                    } catch (error) {
                        console.error('Connection error:', error);
                        showErrorMessage('Connection failed: ' + error.message);
                    }
                });

                // Update wallet status display
                function updateWalletStatus() {
                    if (currentAccount) {
                        document.getElementById('connectionStatus').textContent = 'Connected';
                        document.getElementById('accountAddress').textContent = currentAccount;
                        document.getElementById('networkName').textContent = 'BSC Mainnet';
                        connectBtn.textContent = 'Disconnect';
                        connectBtn.className = 'btn btn-danger';

                        // Update deposit address
                        document.getElementById('depositAddress').value = currentAccount;

                        // Update mobile wallet status
                        document.getElementById('mobileWalletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-success">
                    <h6>✅ Mobile Wallet Connected!</h6>
                    <p><strong>Account:</strong> ${currentAccount}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                    <p class="mb-0">You can now deposit and send BSC BEP20 tokens!</p>
                </div>
            `;
<<<<<<< HEAD
                } else {
                    document.getElementById('connectionStatus').textContent = 'Not Connected';
                    document.getElementById('accountAddress').textContent = '-';
                    document.getElementById('networkName').textContent = '-';
                    connectBtn.textContent = 'Connect Wallet';
                    connectBtn.className = 'btn btn-light';

                    // Reset deposit address
                    document.getElementById('depositAddress').value = 'Connect wallet to get address';

                    // Reset mobile wallet status
                    document.getElementById('mobileWalletStatus').innerHTML = `
=======
                    } else {
                        document.getElementById('connectionStatus').textContent = 'Not Connected';
                        document.getElementById('accountAddress').textContent = '-';
                        document.getElementById('networkName').textContent = '-';
                        connectBtn.textContent = 'Connect Wallet';
                        connectBtn.className = 'btn btn-light';

                        // Reset deposit address
                        document.getElementById('depositAddress').value = 'Connect wallet to get address';

                        // Reset mobile wallet status
                        document.getElementById('mobileWalletStatus').innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-warning">
                    <h6>📱 Mobile Wallet Required</h6>
                    <p class="mb-2">For the best experience, use a mobile wallet app:</p>
                    <div class="row">
                        <div class="col-6">
                            <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-mobile-alt"></i> Trust Wallet
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="https://metamask.io/download/" target="_blank" class="btn btn-info btn-sm w-100">
                                <i class="fab fa-ethereum"></i> MetaMask
                            </a>
                        </div>
                    </div>
                </div>
            `;
<<<<<<< HEAD
                }
            }

            // Load balances
            async function loadBalances() {
                if (!currentAccount) return;

                try {
                    const bnbBalance = await window.walletService.getBalance();
                    document.getElementById('bnbBalance').textContent = parseFloat(bnbBalance).toFixed(6) + ' BNB';
                } catch (error) {
                    console.error('Error loading BNB balance:', error);
                }
            }

            // Refresh BNB balance
            refreshBnbBtn.addEventListener('click', loadBalances);

            // Check token balance
            checkTokenBtn.addEventListener('click', async function() {
                const tokenAddress = document.getElementById('tokenAddress').value;
                if (!tokenAddress) {
                    alert('Please enter token contract address');
                    return;
                }

                try {
                    const balance = await window.walletService.getBalance(tokenAddress);
                    document.getElementById('tokenBalance').textContent = parseFloat(balance).toFixed(6) + ' Tokens';
                } catch (error) {
                    alert('Error checking token balance: ' + error.message);
                }
            });

            // Send BNB
            sendBnbForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const recipient = document.getElementById('bnbRecipient').value;
                const amount = document.getElementById('bnbAmount').value;

                if (!recipient || !amount) {
                    alert('Please fill all fields');
                    return;
                }

                showTransactionModal();
                const result = await window.walletService.sendToken(recipient, amount);
                handleTransactionResult(result);
            });

            // Send Token
            sendTokenForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const contract = document.getElementById('tokenContract').value;
                const recipient = document.getElementById('tokenRecipient').value;
                const amount = document.getElementById('tokenAmount').value;

                if (!contract || !recipient || !amount) {
                    alert('Please fill all fields');
                    return;
                }

                showTransactionModal();
                const result = await window.walletService.sendToken(recipient, amount, contract);
                handleTransactionResult(result);
            });

            // Show transaction modal
            function showTransactionModal() {
                const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
                modal.show();
            }

            // Handle transaction result
            async function handleTransactionResult(result) {
                const statusDiv = document.getElementById('transactionStatus');

                if (result.success) {
                    statusDiv.innerHTML = `
=======
                    }
                }

                // Load balances
                async function loadBalances() {
                    if (!currentAccount) return;

                    try {
                        const bnbBalance = await window.walletService.getBalance();
                        document.getElementById('bnbBalance').textContent = parseFloat(bnbBalance).toFixed(6) +
                            ' BNB';
                    } catch (error) {
                        console.error('Error loading BNB balance:', error);
                    }
                }

                // Refresh BNB balance
                refreshBnbBtn.addEventListener('click', loadBalances);

                // Check token balance
                checkTokenBtn.addEventListener('click', async function() {
                    const tokenAddress = document.getElementById('tokenAddress').value;
                    if (!tokenAddress) {
                        alert('Please enter token contract address');
                        return;
                    }

                    try {
                        const balance = await window.walletService.getBalance(tokenAddress);
                        document.getElementById('tokenBalance').textContent = parseFloat(balance)
                            .toFixed(6) + ' Tokens';
                    } catch (error) {
                        alert('Error checking token balance: ' + error.message);
                    }
                });

                // Send BNB
                sendBnbForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const recipient = document.getElementById('bnbRecipient').value;
                    const amount = document.getElementById('bnbAmount').value;

                    if (!recipient || !amount) {
                        alert('Please fill all fields');
                        return;
                    }

                    showTransactionModal();
                    const result = await window.walletService.sendToken(recipient, amount);
                    handleTransactionResult(result);
                });

                // Send Token
                sendTokenForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const contract = document.getElementById('tokenContract').value;
                    const recipient = document.getElementById('tokenRecipient').value;
                    const amount = document.getElementById('tokenAmount').value;

                    if (!contract || !recipient || !amount) {
                        alert('Please fill all fields');
                        return;
                    }

                    showTransactionModal();
                    const result = await window.walletService.sendToken(recipient, amount, contract);
                    handleTransactionResult(result);
                });

                // Show transaction modal
                function showTransactionModal() {
                    const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
                    modal.show();
                }

                // Handle transaction result
                async function handleTransactionResult(result) {
                    const statusDiv = document.getElementById('transactionStatus');

                    if (result.success) {
                        statusDiv.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-info">
                    <h6>Transaction Submitted!</h6>
                    <p>Tx Hash: <a href="https://bscscan.com/tx/${result.txHash}" target="_blank">${result.txHash}</a></p>
                    <p>Waiting for confirmation...</p>
                </div>
            `;

<<<<<<< HEAD
                    // Wait for confirmation
                    const confirmation = await window.walletService.waitForTransaction(result.txHash);
                    if (confirmation.success) {
                        statusDiv.innerHTML = `
=======
                        // Wait for confirmation
                        const confirmation = await window.walletService.waitForTransaction(result.txHash);
                        if (confirmation.success) {
                            statusDiv.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                    <div class="alert alert-success">
                        <h6>Transaction Confirmed!</h6>
                        <p>Block: ${confirmation.blockNumber}</p>
                        <p>Gas Used: ${confirmation.gasUsed}</p>
                    </div>
                `;
<<<<<<< HEAD
                        loadBalances(); // Refresh balances
                    } else {
                        statusDiv.innerHTML = `
=======
                            loadBalances(); // Refresh balances
                        } else {
                            statusDiv.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                    <div class="alert alert-danger">
                        <h6>Transaction Failed!</h6>
                        <p>${confirmation.error}</p>
                    </div>
                `;
<<<<<<< HEAD
                    }
                } else {
                    statusDiv.innerHTML = `
=======
                        }
                    } else {
                        statusDiv.innerHTML = `
>>>>>>> b51f49c (Wallet Index update)
                <div class="alert alert-danger">
                    <h6>Transaction Failed!</h6>
                    <p>${result.error}</p>
                </div>
            `;
<<<<<<< HEAD
                }
            }
        });
    }
</script>
@endsection
=======
                    }
                }
            });
        }
    </script>
@endsection
>>>>>>> b51f49c (Wallet Index update)
