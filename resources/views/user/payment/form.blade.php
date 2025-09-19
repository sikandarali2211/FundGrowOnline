@extends('layouts.user')

@section('content')
    <style>
        .payment-container {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            padding-top: 80px;
        }

        .payment-card {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 20px;
            border: 1px solid rgba(59, 209, 122, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            color: #fff;
        }

        .plan-info {
            background: rgba(59, 209, 122, 0.1);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .plan-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(59, 209, 122, 0.2);
        }

        .plan-detail-item:last-child {
            border-bottom: none;
        }

        .plan-detail-item .label {
            color: #ccc;
            font-weight: 500;
        }

        .plan-detail-item .value {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .wallet-connect-section {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .payment-form {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 25px;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(59, 209, 122, 0.3);
            color: #fff;
            border-radius: 10px;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #3bd17a;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.25);
        }

        .form-control::placeholder {
            color: #a5f2d5;
        }

        .btn-payment {
            background: linear-gradient(45deg, #3bd17a, #00d4aa);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            color: #0d1b2a;
            transition: all 0.3s ease;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 209, 122, 0.4);
            color: #0d1b2a;
        }

        .btn-wallet {
            background: linear-gradient(45deg, #3375bb, #4a90e2);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-wallet:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(51, 117, 188, 0.4);
            color: white;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .wallet-info {
            background: rgba(59, 209, 122, 0.1);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
        }

        .wallet-address-container {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 15px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #fff;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(59, 209, 122, 0.3);
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #3bd17a;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.25);
        }

        .form-control::placeholder {
            color: #999;
        }

        .loading-spinner {
            display: none;
        }

        .loading-spinner.show {
            display: inline-block;
        }
    </style>

<div class="main-panel">
    <div class="payment-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="text-success mb-3">
                            <i class="fa fa-credit-card me-2"></i>Payment for Investment Plan
                        </h2>
                        <p class="text-muted">Complete your payment to activate your investment plan</p>
                    </div>

                

                </div>

                <div class="row">
                    <!-- LEFT SIDE -->
                    <div class="col-lg-6 col-md-12">
                        <!-- Plan Information -->
                        <div class="payment-card mb-4">
                            <div class="plan-info">
                                <h4 class="text-success mb-3">
                                    <i class="fa fa-chart-line me-2"></i>{{ $plan['name'] }}
                                </h4>
                                <div class="plan-detail-item">
                                    <span class="label">Investment Amount:</span>
                                    <span class="value text-warning">${{ number_format($plan['amount'], 2) }}</span>
                                </div>
                                <div class="plan-detail-item">
                                    <span class="label">Duration:</span>
                                    <span class="value">{{ $plan['duration_days'] }} days</span>
                                </div>
                                <div class="plan-detail-item">
                                    <span class="label">Expected Return:</span>
                                    <span class="value text-success">${{ number_format($plan['amount'] * (1 + $plan['return_percentage'] / 100), 2) }}</span>
                                </div>
                                <div class="plan-detail-item">
                                    <span class="label">Return Rate:</span>
                                    <span class="value">{{ $plan['return_percentage'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Connection Section -->
                        <div class="payment-card mb-4">
                            <div class="wallet-connect-section">
                                <h5 class="text-white mb-4">
                                    <i class="fa fa-wallet me-2"></i>Connect Your Wallet
                                </h5>

                                <div id="walletConnectSection">
                                    <button id="connectWalletBtn" class="btn btn-wallet w-100 mb-3">
                                        <i class="fa fa-wallet me-2"></i>Connect Wallet
                                    </button>
                                    <p class="text-muted text-center">
                                        Connect your Trust Wallet or MetaMask to proceed with payment
                                    </p>
                                </div>

                                <div id="walletInfoSection" class="wallet-info" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="text-success mb-0">
                                            <i class="fa fa-check-circle me-2"></i>Wallet Connected
                                        </h6>
                                        <button id="disconnectWalletBtn" class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-times me-1"></i>Disconnect
                                        </button>
                                    </div>
                                    <div class="wallet-address-container">
                                        <strong class="text-muted">Address:</strong>
                                        <div id="walletAddress" class="text-white font-monospace mt-1 p-2 bg-dark rounded" style="word-break: break-all; font-size: 0.9rem;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-lg-6 col-md-12">
                        <!-- Payment Form -->
                        <div class="payment-card">
                            <div class="payment-form">
                                <h5 class="text-white mb-4">
                                    <i class="fa fa-credit-card me-2"></i>Payment Details
                                </h5>

                                <form id="paymentForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="paymentAmount" class="form-label">Amount (BNB)</label>
                                                <input type="number" class="form-control" id="paymentAmount"
                                                    value="{{ $plan['amount'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="currency" class="form-label">Currency</label>
                                                <select class="form-control" id="currency">
                                                    <option value="BNB">BNB</option>
                                                    <option value="USDT">USDT</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="adminWalletAddress" class="form-label">Admin Wallet Address</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="adminWalletAddress"
                                                value="{{ $adminWalletAddress }}" readonly style="font-family: monospace; font-size: 0.9rem;">
                                            <button type="button" class="btn btn-outline-success" id="copyAddressBtn">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Send payment to this address</small>
                                    </div>

                                    <!-- Transaction Hash will be auto-generated -->

                                    <div class="text-center">
                                        <button type="button" class="btn btn-payment btn-lg" id="purchaseBtn" disabled>
                                            <span class="loading-spinner me-2">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </span>
                                            <i class="fa fa-shopping-cart me-2"></i>Purchase Plan
                                        </button>
                                        <div id="balanceInfo" class="mt-3" style="display: none;">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle me-2"></i>
                                                <span id="balanceText"></span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div id="paymentStatus" class="mt-4" style="display: none;">
                            <div class="payment-card">
                                <div class="text-center">
                                    <div id="statusIcon" class="mb-3">
                                        <i class="fa fa-spinner fa-spin fa-3x text-warning"></i>
                                    </div>
                                    <h4 id="statusTitle" class="text-white mb-3">Processing Payment...</h4>
                                    <p id="statusMessage" class="text-muted mb-3">Please wait while we verify your payment
                                    </p>
                                    <div id="statusBadge" class="status-badge status-pending">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row end -->
            </div>
        </div>
    </div>
</div>
    <!-- Ethers.js for Web3 functionality -->
    <script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
    <script src="{{ asset('js/wallet-service.js') }}"></script>

    <script>
        // Payment state
        let isWalletConnected = false;
        let walletAddress = null;
        let walletService = null;
        let userBalance = 0;
        let planAmount = {{ $plan['amount'] }}; // This is in dollars, we need to convert to BNB

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if wallet service is available
            if (typeof window.walletService !== 'undefined') {
                walletService = window.walletService;
            } else {
                showStatus('error', 'Wallet service not loaded. Please refresh the page.');
                return;
            }

            // Event listeners
            document.getElementById('connectWalletBtn').addEventListener('click', connectWallet);
            document.getElementById('disconnectWalletBtn').addEventListener('click', disconnectWallet);
            document.getElementById('copyAddressBtn').addEventListener('click', copyAddress);
            document.getElementById('purchaseBtn').addEventListener('click', purchasePlan);

            // Check if wallet is already connected
            checkWalletConnection();
        });

        async function connectWallet() {
            const connectBtn = document.getElementById('connectWalletBtn');
            const originalText = connectBtn.innerHTML;

            try {
                connectBtn.disabled = true;
                connectBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Connecting...';

                const result = await walletService.connectWallet();

                if (result.success) {
                    walletAddress = result.account;
                    isWalletConnected = true;

                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';

                    // Check balance and enable purchase
                    await checkBalanceAndEnablePurchase();

                    showStatus('success', 'Wallet connected successfully!');
                } else {
                    showStatus('error', result.error || 'Failed to connect wallet');
                }
            } catch (error) {
                showStatus('error', 'Connection failed: ' + error.message);
            } finally {
                connectBtn.disabled = false;
                connectBtn.innerHTML = originalText;
            }
        }

        async function checkBalanceAndEnablePurchase() {
            try {
                // Get BNB balance
                const balance = await walletService.getBalance();
                userBalance = parseFloat(balance);
                
                // For now, let's assume 1 BNB = $300 (you can get real-time rate later)
                const bnbToUsdRate = 300;
                const requiredBnb = planAmount / bnbToUsdRate;
                
                const balanceInfo = document.getElementById('balanceInfo');
                const balanceText = document.getElementById('balanceText');
                const purchaseBtn = document.getElementById('purchaseBtn');
                
                if (userBalance >= requiredBnb) {
                    balanceText.textContent = `Balance: ${userBalance.toFixed(4)} BNB ($${(userBalance * bnbToUsdRate).toFixed(2)}) - Sufficient for purchase`;
                    balanceInfo.className = 'mt-3 alert alert-success';
                    purchaseBtn.disabled = false;
                    purchaseBtn.innerHTML = '<i class="fa fa-shopping-cart me-2"></i>Purchase Plan';
                } else {
                    balanceText.textContent = `Balance: ${userBalance.toFixed(4)} BNB ($${(userBalance * bnbToUsdRate).toFixed(2)}) - Insufficient. Required: ${requiredBnb.toFixed(4)} BNB ($${planAmount})`;
                    balanceInfo.className = 'mt-3 alert alert-warning';
                    purchaseBtn.disabled = true;
                    purchaseBtn.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i>Insufficient Balance';
                }
                
                balanceInfo.style.display = 'block';
            } catch (error) {
                console.error('Balance check failed:', error);
                showStatus('error', 'Failed to check balance');
                
                // Enable button anyway for testing
                const purchaseBtn = document.getElementById('purchaseBtn');
                purchaseBtn.disabled = false;
                purchaseBtn.innerHTML = '<i class="fa fa-shopping-cart me-2"></i>Purchase Plan (Test Mode)';
            }
        }

        function disconnectWallet() {
            if (walletService) {
                walletService.disconnect();
            }

            isWalletConnected = false;
            walletAddress = null;
            userBalance = 0;

            // Reset UI
            document.getElementById('walletConnectSection').style.display = 'block';
            document.getElementById('walletInfoSection').style.display = 'none';
            document.getElementById('balanceInfo').style.display = 'none';
            document.getElementById('purchaseBtn').disabled = true;

            showStatus('info', 'Wallet disconnected');
        }

        function copyAddress() {
            const addressInput = document.getElementById('adminWalletAddress');
            addressInput.select();
            document.execCommand('copy');
            showStatus('success', 'Address copied to clipboard!');
        }

        async function purchasePlan() {
            if (!isWalletConnected) {
                showStatus('error', 'Please connect your wallet first');
                return;
            }

            const purchaseBtn = document.getElementById('purchaseBtn');
            const originalText = purchaseBtn.innerHTML;

            try {
                purchaseBtn.disabled = true;
                purchaseBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processing...';

                // Calculate BNB amount needed
                const bnbToUsdRate = 300;
                const requiredBnb = planAmount / bnbToUsdRate;

                // Execute transaction
                const adminAddress = document.getElementById('adminWalletAddress').value;
                const result = await walletService.sendTransaction(adminAddress, requiredBnb);

                if (result.success) {
                    // Save transaction to database
                    await saveTransactionToDatabase(result.transactionHash, planAmount);
                    showStatus('success', 'Plan purchased successfully! Transaction: ' + result.transactionHash);
                } else {
                    showStatus('error', result.error || 'Transaction failed');
                }
            } catch (error) {
                showStatus('error', 'Purchase failed: ' + error.message);
            } finally {
                purchaseBtn.disabled = false;
                purchaseBtn.innerHTML = originalText;
            }
        }

        async function saveTransactionToDatabase(transactionHash, amount) {
            try {
                const response = await fetch('/payment/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        plan_id: '{{ $plan["id"] }}',
                        transaction_hash: transactionHash,
                        amount: amount,
                        currency: 'BNB',
                        from_address: walletAddress,
                        to_address: document.getElementById('adminWalletAddress').value
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Failed to save transaction');
                }
            } catch (error) {
                console.error('Failed to save transaction:', error);
                throw error;
            }
        }


        function updatePaymentStatus(type, title, message) {
            const statusIcon = document.getElementById('statusIcon');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');
            const statusBadge = document.getElementById('statusBadge');

            statusTitle.textContent = title;
            statusMessage.textContent = message;

            if (type === 'success') {
                statusIcon.innerHTML = '<i class="fa fa-check-circle fa-3x text-success"></i>';
                statusBadge.className = 'status-badge status-success';
                statusBadge.textContent = 'Success';
            } else if (type === 'error') {
                statusIcon.innerHTML = '<i class="fa fa-times-circle fa-3x text-danger"></i>';
                statusBadge.className = 'status-badge status-error';
                statusBadge.textContent = 'Failed';
            } else {
                statusIcon.innerHTML = '<i class="fa fa-spinner fa-spin fa-3x text-warning"></i>';
                statusBadge.className = 'status-badge status-pending';
                statusBadge.textContent = 'Processing';
            }
        }

        function checkWalletConnection() {
            // Check if wallet is already connected
            if (typeof window.ethereum !== 'undefined' && window.ethereum.selectedAddress) {
                walletAddress = window.ethereum.selectedAddress;
                isWalletConnected = true;

                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
            }
        }

        function showStatus(type, message) {
            // Create status message
            const statusDiv = document.createElement('div');
            statusDiv.className =
                `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
            statusDiv.style.position = 'fixed';
            statusDiv.style.top = '20px';
            statusDiv.style.right = '20px';
            statusDiv.style.zIndex = '9999';
            statusDiv.style.minWidth = '300px';

            statusDiv.innerHTML = `
                <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(statusDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (statusDiv.parentNode) {
                    statusDiv.parentNode.removeChild(statusDiv);
                }
            }, 5000);
        }
    </script>
@endsection
