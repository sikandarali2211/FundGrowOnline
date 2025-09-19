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
            padding: 20px;
            margin-bottom: 30px;
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
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }

        .loading-spinner {
            display: none;
        }

        .loading-spinner.show {
            display: inline-block;
        }
    </style>
<<<<<<< HEAD
    <div class="main-panel">
        <div class="payment-container">
            <div class="container">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h2 class="text-success mb-3">
                        <i class="fa fa-credit-card me-2"></i> Payment for Investment Plan
                    </h2>
                    <p class="text-muted">Complete your payment to activate your investment plan</p>
=======
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

                    <!-- Plan Information -->
                    <div class="payment-card mb-4">
                        <div class="plan-info">
                            <h4 class="text-success mb-3">
                                <i class="fa fa-chart-line me-2"></i>{{ $plan['name'] }}
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Investment Amount:</strong></p>
                                    <h3 class="text-warning">${{ number_format($plan['amount'], 2) }}</h3>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Expected Return:</strong></p>
                                    <h3 class="text-success">${{ number_format($plan['amount'] * (1 + $plan['return_percentage'] / 100), 2) }}</h3>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Duration:</strong> {{ $plan['duration_days'] }} days</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Return Rate:</strong> {{ $plan['return_percentage'] }}%</p>
                                </div>
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
                                <h6 class="text-success mb-3">
                                    <i class="fa fa-check-circle me-2"></i>Wallet Connected
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>Address:</strong>
                                        <div id="walletAddress" class="text-muted font-monospace"></div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button id="disconnectWalletBtn" class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-times me-1"></i>Disconnect
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                               value="{{ $adminWalletAddress }}" readonly>
                                        <button type="button" class="btn btn-outline-success" id="copyAddressBtn">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Send payment to this address</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="transactionHash" class="form-label">Transaction Hash</label>
                                    <input type="text" class="form-control" id="transactionHash" 
                                           placeholder="0x..." required>
                                    <small class="form-text text-muted">Enter the transaction hash after sending payment</small>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-payment btn-lg" id="submitPaymentBtn">
                                        <span class="loading-spinner me-2">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                        <i class="fa fa-paper-plane me-2"></i>Submit Payment
                                    </button>
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
                                <p id="statusMessage" class="text-muted mb-3">Please wait while we verify your payment</p>
                                <div id="statusBadge" class="status-badge status-pending">Pending</div>
                            </div>
                        </div>
                    </div>
>>>>>>> 0ace04b33a843d3e8034c6863b23a09c8745e1cb
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Investment Amount:</strong></p>
                                        <h3 class="text-warning">${{ number_format($plan['amount'], 2) }}</h3>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Expected Return:</strong></p>
                                        <h3 class="text-success">
                                            ${{ number_format($plan['amount'] * (1 + $plan['return_percentage'] / 100), 2) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Duration:</strong> {{ $plan['duration_days'] }} days</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Return Rate:</strong> {{ $plan['return_percentage'] }}%
                                        </p>
                                    </div>
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
                                    <h6 class="text-success mb-3">
                                        <i class="fa fa-check-circle me-2"></i>Wallet Connected
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <strong>Address:</strong>
                                            <div id="walletAddress" class="text-muted font-monospace"></div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button id="disconnectWalletBtn" class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-times me-1"></i>Disconnect
                                            </button>
                                        </div>
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
                                                value="0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6" readonly>
                                            <button type="button" class="btn btn-outline-success" id="copyAddressBtn">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Send payment to this address</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="transactionHash" class="form-label">Transaction Hash</label>
                                        <input type="text" class="form-control" id="transactionHash" placeholder="0x..."
                                            required>
                                        <small class="form-text text-muted">Enter the transaction hash after sending
                                            payment</small>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-payment btn-lg" id="submitPaymentBtn">
                                            <span class="loading-spinner me-2">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </span>
                                            <i class="fa fa-paper-plane me-2"></i>Submit Payment
                                        </button>
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
            document.getElementById('paymentForm').addEventListener('submit', submitPayment);

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

        function disconnectWallet() {
            if (walletService) {
                walletService.disconnect();
            }

            isWalletConnected = false;
            walletAddress = null;

            // Reset UI
            document.getElementById('walletConnectSection').style.display = 'block';
            document.getElementById('walletInfoSection').style.display = 'none';

            showStatus('info', 'Wallet disconnected');
        }

        function copyAddress() {
            const addressInput = document.getElementById('adminWalletAddress');
            addressInput.select();
            document.execCommand('copy');
            showStatus('success', 'Address copied to clipboard!');
        }

        async function submitPayment(event) {
            event.preventDefault();

            if (!isWalletConnected) {
                showStatus('error', 'Please connect your wallet first');
                return;
            }

            const transactionHash = document.getElementById('transactionHash').value.trim();
            const amount = document.getElementById('paymentAmount').value;
            const currency = document.getElementById('currency').value;

            if (!transactionHash) {
                showStatus('error', 'Please enter transaction hash');
                return;
            }

            if (!transactionHash.startsWith('0x') || transactionHash.length !== 66) {
                showStatus('error', 'Please enter a valid transaction hash');
                return;
            }

            try {
                const submitBtn = document.getElementById('submitPaymentBtn');
                const spinner = submitBtn.querySelector('.loading-spinner');

                submitBtn.disabled = true;
                spinner.classList.add('show');

                // Show payment status
                document.getElementById('paymentStatus').style.display = 'block';
                updatePaymentStatus('processing', 'Processing Payment...', 'Please wait while we verify your payment');

                // Submit payment
                const response = await fetch('{{ route('payment.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        plan_id: '{{ $plan['id'] }}',
                        transaction_hash: transactionHash,
                        from_address: walletAddress,
                        amount: amount,
                        currency: currency
                    })
                });

                const result = await response.json();

                if (result.success) {
                    updatePaymentStatus('success', 'Payment Submitted!',
                        'Your payment has been submitted and is pending verification');
                    showStatus('success', 'Payment submitted successfully!');
                } else {
                    updatePaymentStatus('error', 'Payment Failed', result.message);
                    showStatus('error', result.message);
                }

            } catch (error) {
                updatePaymentStatus('error', 'Payment Failed', 'An error occurred while processing payment');
                showStatus('error', 'Payment failed: ' + error.message);
            } finally {
                const submitBtn = document.getElementById('submitPaymentBtn');
                const spinner = submitBtn.querySelector('.loading-spinner');

                submitBtn.disabled = false;
                spinner.classList.remove('show');
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
