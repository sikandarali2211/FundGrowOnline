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
        
        /* Hide mobile connect button when wallet is connected */
        .wallet-connected #mobileConnectBtn {
            display: none !important;
            visibility: hidden !important;
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
                                    <button id="connectWalletBtn" class="btn btn-wallet w-100 mb-3" onclick="detectAndConnectTrustWallet()">
                                        <i class="fa fa-wallet me-2"></i>Connect Trust Wallet
                                    </button>
                                    <p class="text-muted text-center">
                                        Connect your Trust Wallet to proceed with payment
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
                                                <label for="paymentAmount" class="form-label">Amount (USD)</label>
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="depositFee" class="form-label">Deposit Fee (Flat)</label>
                                                <input type="text" class="form-control" id="depositFee" value="$1.00" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-1">
                                                <label for="payableAmount" class="form-label">Payable (You Send)</label>
                                                <input type="text" class="form-control" id="payableAmount" value="" readonly>
                                            </div>
                                            <small class="form-text text-muted">You send Net + $1 fee. Net amount is credited to your balance wallet.</small>
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
                                        
                                        <!-- Debug Button -->
                                        <button type="button" hidden class="btn btn-warning btn-sm mt-2" onclick="debugWalletState()">
                                            <i class="fa fa-bug me-1"></i>Debug Wallet State
                                        </button>
                                        
                                        <!-- Force Connect Button -->
                                        <button type="button" hidden class="btn btn-info btn-sm mt-2" onclick="forceWalletConnection()">
                                            <i class="fa fa-plug me-1"></i>Force Connect Wallet
                                        </button>
                                        
                                        <!-- Reset Transaction Button -->
                                        <button type="button" hidden class="btn btn-danger btn-sm mt-2" onclick="resetStuckTransaction()">
                                            <i class="fa fa-refresh me-1"></i>Reset Transaction
                                        </button>
                                        
                                        <!-- Simple Connect Button -->
                                        <button type="button" hidden class="btn btn-success btn-sm mt-2" onclick="connectWalletSimple()">
                                            <i class="fa fa-wallet me-1"></i>Simple Connect
                                        </button>
                                        
                                        <!-- Force Transaction Button -->
                                        <button type="button" hidden class="btn btn-warning btn-sm mt-2" onclick="forceTransaction()">
                                            <i class="fa fa-bolt me-1"></i>Force Transaction
                                        </button>
                                        
                                        <!-- Trust Wallet Instructions Button -->
                                        <button type="button" hidden class="btn btn-info btn-sm mt-2" onclick="showTrustWalletInstructions()">
                                            <i class="fa fa-question-circle me-1"></i>Trust Wallet Help
                                        </button>
                                        
                                        <!-- Check Trust Wallet Status Button -->
                                        <button type="button" hidden class="btn btn-success btn-sm mt-2" onclick="checkTrustWalletStatus()">
                                            <i class="fa fa-check-circle me-1"></i>Check Status
                                        </button>
                                        
                                        <!-- Test Wallet Button -->
                                        <button type="button" hidden class="btn btn-primary btn-sm mt-2" onclick="testWalletFunctionality()">
                                            <i class="fa fa-cog me-1"></i>Test Wallet
                                        </button>
                                        
                                        <!-- Remove MetaMask Button -->
                                        <button type="button" hidden class="btn btn-danger btn-sm mt-2" onclick="showMetaMaskRemovalInstructions()">
                                            <i class="fa fa-times-circle me-1"></i>Remove MetaMask
                                        </button>
                                        
                                        <!-- Detect Wallet Button -->
                                        <button type="button" hidden class="btn btn-info btn-sm mt-2" onclick="detectAndConnectTrustWallet()">
                                            <i class="fa fa-search me-1"></i>Detect Wallet
                                        </button>
                                        
                                        <!-- Mobile Connect Button - Hidden when wallet connected -->
                                        <button type="button" id="mobileConnectBtn" class="btn btn-success btn-sm mt-2" onclick="connectMobileTrustWallet()">
                                            <i class="fa fa-mobile me-1"></i>Mobile Connect
                                        </button>
                                        
                                        <!-- Force Check Wallet Button -->
                                        <button type="button" hidden class="btn btn-warning btn-sm mt-2" onclick="forceCheckWallet()">
                                            <i class="fa fa-refresh me-1"></i>Force Check Wallet
                                        </button>
                                        
                        <!-- Check Ethers Button -->
                        <button type="button" hidden class="btn btn-secondary btn-sm mt-2" onclick="checkEthersCompatibility()">
                            <i class="fa fa-code me-1"></i>Check Ethers
                        </button>
                        
                        <!-- Test USDT BEP20 Contract Button -->
                        <button type="button" hidden class="btn btn-info btn-sm mt-2" onclick="testUsdtContract()">
                            <i class="fa fa-coins me-1"></i>Test USDT BEP20
                        </button>
                        
                        <!-- Switch to BSC Button -->
                        <button type="button" hidden class="btn btn-warning btn-sm mt-2" onclick="switchToBSCNetwork()">
                            <i class="fa fa-exchange-alt me-1"></i>Switch to BSC
                        </button>
                                        
                                        <!-- Manual Balance Test -->
                                        <div class="mt-3">
                                            <label class="form-label text-white">Manual Balance Test (USDT BEP20):</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="manualBalance" placeholder="1.0" step="0.001" min="0">
                                                <button type="button" class="btn btn-info" onclick="setManualBalance()">
                                                    <i class="fa fa-check me-1"></i>Set USDT Balance
                                                </button>
                                            </div>
                                        </div>
                                        
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
    <!-- Multiple Ethers.js CDN sources for better reliability -->
    <script>
        // Try multiple CDN sources for ethers.js
        function loadEthersJS() {
            const cdnSources = [
                'https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js',
                'https://unpkg.com/ethers@5.7.2/dist/ethers.umd.min.js',
                'https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js'
            ];
            
            let currentIndex = 0;
            
            function tryLoadScript() {
                if (currentIndex >= cdnSources.length) {
                    console.error('❌ All ethers.js CDNs failed');
                    showStatus('error', 'Failed to load ethers.js library. Please check your internet connection.');
                    return;
                }
                
                const script = document.createElement('script');
                script.src = cdnSources[currentIndex];
                script.onload = function() {
                    console.log('✅ Ethers.js loaded successfully from:', cdnSources[currentIndex]);
                };
                script.onerror = function() {
                    console.log('⚠️ Failed to load from:', cdnSources[currentIndex]);
                    currentIndex++;
                    tryLoadScript();
                };
                document.head.appendChild(script);
            }
            
            tryLoadScript();
        }
        
        // Start loading ethers.js
        loadEthersJS();
    </script>
    
    <script src="{{ asset('js/wallet-service.js') }}"></script>

    <script>
        // Payment state
        let isWalletConnected = false;
        let walletAddress = null;
        let walletService = null;
        let userBalance = 0;
        let planAmount = {{ $plan['amount'] }}; // Net amount in USD
        const depositFee = 1.00; // Flat $1 fee
        
        // Auto-fill payment amount function
        function autoFillPaymentAmount() {
            try {
                const amountInput = document.getElementById('paymentAmount') || document.getElementById('amount');
                
                if (amountInput) {
                    amountInput.value = planAmount;
                    console.log('✅ Auto-filled payment amount:', planAmount);
                    showStatus('info', 'Payment amount auto-filled: $' + planAmount);
                }
                // Update fee/payable UI
                updateFeeUI();
            } catch (error) {
                console.error('Error auto-filling payment amount:', error);
            }
        }

        function updateFeeUI() {
            try {
                const payableInput = document.getElementById('payableAmount');
                if (payableInput) {
                    const gross = (parseFloat(planAmount) || 0) + depositFee;
                    payableInput.value = `$${gross.toFixed(2)}`;
                }
            } catch (e) {
                console.warn('Failed to update fee UI', e);
            }
        }
        
        // Update UI when wallet connects/disconnects
        function updateWalletUI(isConnected) {
            console.log('=== Updating Wallet UI ===');
            console.log('Wallet connected:', isConnected);
            
            const mobileConnectBtn = document.getElementById('mobileConnectBtn');
            const connectWalletBtn = document.getElementById('connectWalletBtn');
            const walletConnectSection = document.getElementById('walletConnectSection');
            
            console.log('Mobile connect button found:', !!mobileConnectBtn);
            console.log('Connect wallet button found:', !!connectWalletBtn);
            console.log('Wallet connect section found:', !!walletConnectSection);
            
            if (isConnected) {
                // Add CSS class to hide mobile connect button
                if (walletConnectSection) {
                    walletConnectSection.classList.add('wallet-connected');
                    console.log('✅ Added wallet-connected class');
                }
                
                // Hide mobile connect button when wallet is connected
                if (mobileConnectBtn) {
                    mobileConnectBtn.style.display = 'none';
                    mobileConnectBtn.style.visibility = 'hidden';
                    mobileConnectBtn.hidden = true;
                    console.log('✅ Mobile connect button hidden');
                }
                // Show connect wallet button for reconnection if needed
                if (connectWalletBtn) {
                    connectWalletBtn.style.display = 'block';
                    console.log('✅ Connect wallet button shown');
                }
            } else {
                // Remove CSS class to show mobile connect button
                if (walletConnectSection) {
                    walletConnectSection.classList.remove('wallet-connected');
                    console.log('✅ Removed wallet-connected class');
                }
                
                // Show mobile connect button when wallet is disconnected
                if (mobileConnectBtn) {
                    mobileConnectBtn.style.display = 'block';
                    mobileConnectBtn.style.visibility = 'visible';
                    mobileConnectBtn.hidden = false;
                    console.log('✅ Mobile connect button shown');
                }
            }
        }

        // Check ethers version and compatibility
        function checkEthersCompatibility() {
            console.log('=== Checking Ethers.js Compatibility ===');
            
            if (typeof ethers === 'undefined') {
                console.error('❌ Ethers.js not loaded');
                return false;
            }
            
            console.log('✅ Ethers.js loaded');
            console.log('Ethers version:', ethers.version || 'unknown');
            console.log('Ethers object keys:', Object.keys(ethers));
            
            // Check for v5 vs v6 compatibility
            if (ethers.version && ethers.version.startsWith('6')) {
                console.log('✅ Using Ethers.js v6');
                console.log('Available methods:', {
                    BrowserProvider: typeof ethers.BrowserProvider,
                    formatEther: typeof ethers.formatEther,
                    parseEther: typeof ethers.parseEther
                });
            } else {
                console.log('✅ Using Ethers.js v5');
                console.log('Available methods:', {
                    providers: typeof ethers.providers,
                    utils: typeof ethers.utils,
                    formatEther: typeof ethers.utils?.formatEther,
                    parseEther: typeof ethers.utils?.parseEther
                });
            }
            
            return true;
        }

        async function switchToBSCNetwork() {
            try {
                console.log('=== Switching to BSC Network ===');
                
                // BSC Mainnet configuration
                const BSC_MAINNET = {
                    chainId: '0x38', // 56 in decimal
                    chainName: 'Binance Smart Chain',
                    nativeCurrency: {
                        name: 'BNB',
                        symbol: 'BNB',
                        decimals: 18
                    },
                    rpcUrls: [
                        'https://bsc-dataseed.binance.org/',
                        'https://bsc-dataseed1.defibit.io/',
                        'https://bsc-dataseed1.ninicoin.io/'
                    ],
                    blockExplorerUrls: ['https://bscscan.com/']
                };
                
                // Check current network
                const currentChainId = await window.ethereum.request({ method: 'eth_chainId' });
                console.log('Current chain ID:', currentChainId);
                
                if (currentChainId === BSC_MAINNET.chainId) {
                    console.log('✅ Already on BSC network');
                    return;
                }
                
                console.log('Switching to BSC network...');
                showStatus('info', 'Switching to BSC network...');
                
                // Switch to BSC network
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: BSC_MAINNET.chainId }]
                });
                
                console.log('✅ Successfully switched to BSC network');
                showStatus('success', 'Successfully switched to BSC network');
                
            } catch (switchError) {
                console.log('Switch failed, trying to add network...', switchError);
                
                // If network doesn't exist, add it
                if (switchError.code === 4902) {
                    try {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [BSC_MAINNET]
                        });
                        console.log('✅ BSC network added and switched');
                        showStatus('success', 'BSC network added and switched');
                    } catch (addError) {
                        console.error('Failed to add BSC network:', addError);
                        throw new Error('Failed to add BSC network. Please add it manually in your wallet.');
                    }
                } else {
                    console.error('Failed to switch to BSC network:', switchError);
                    throw new Error('Failed to switch to BSC network. Please switch manually in your wallet.');
                }
            }
        }

        async function checkBnbBalanceForGas() {
            try {
                console.log('=== Checking BNB Balance for Gas Fees ===');
                
                if (typeof ethers === 'undefined') {
                    throw new Error('Ethers.js not loaded');
                }
                
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length === 0) {
                    throw new Error('No accounts connected');
                }
                
                let provider;
                if (ethers.version && ethers.version.startsWith('6')) {
                    provider = new ethers.BrowserProvider(window.ethereum);
                } else {
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                }
                
                const balance = await provider.getBalance(accounts[0]);
                let balanceInBnb;
                if (ethers.version && ethers.version.startsWith('6')) {
                    balanceInBnb = ethers.formatEther(balance);
                } else {
                    balanceInBnb = ethers.utils.formatEther(balance);
                }
                
                const bnbBalance = parseFloat(balanceInBnb);
                console.log('BNB Balance for gas:', bnbBalance);
                
                // Check if user has enough BNB for gas (minimum 0.001 BNB)
                const minBnbForGas = 0.001;
                if (bnbBalance < minBnbForGas) {
                    showStatus('warning', `Low BNB balance (${bnbBalance.toFixed(6)} BNB). You need at least ${minBnbForGas} BNB for gas fees. Please add some BNB to your wallet.`);
                } else {
                    console.log('✅ Sufficient BNB balance for gas fees');
                    showStatus('info', `BNB balance: ${bnbBalance.toFixed(6)} BNB (sufficient for gas)`);
                }
                
            } catch (error) {
                console.error('BNB balance check failed:', error);
                showStatus('warning', 'Could not check BNB balance for gas fees: ' + error.message);
            }
        }

        async function testUsdtContract() {
            console.log('=== Testing USDT BEP20 Contract ===');
            
            if (typeof ethers === 'undefined') {
                showStatus('error', 'Ethers.js not loaded');
                return;
            }
            
            if (!window.ethereum) {
                showStatus('error', 'No wallet detected');
                return;
            }
            
            try {
                // Switch to BSC first
                await switchToBSCNetwork();
                
                // Test BSC USDT BEP20 contract
                const USDT_BEP20_ADDRESS = '0x55d398326f99059fF775485246999027B3197955'; // USDT BEP20 on BSC
                
                const USDT_ABI = [
                    {
                        "constant": true,
                        "inputs": [],
                        "name": "decimals",
                        "outputs": [{"name": "", "type": "uint8"}],
                        "type": "function"
                    },
                    {
                        "constant": true,
                        "inputs": [{"name": "_owner", "type": "address"}],
                        "name": "balanceOf",
                        "outputs": [{"name": "balance", "type": "uint256"}],
                        "type": "function"
                    },
                    {
                        "constant": true,
                        "inputs": [],
                        "name": "name",
                        "outputs": [{"name": "", "type": "string"}],
                        "type": "function"
                    },
                    {
                        "constant": true,
                        "inputs": [],
                        "name": "symbol",
                        "outputs": [{"name": "", "type": "string"}],
                        "type": "function"
                    }
                ];
                
                let provider;
                if (ethers.version && ethers.version.startsWith('6')) {
                    provider = new ethers.BrowserProvider(window.ethereum);
                } else {
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                }
                
                console.log(`Testing BSC USDT BEP20 contract: ${USDT_BEP20_ADDRESS}`);
                
                const contract = new ethers.Contract(USDT_BEP20_ADDRESS, USDT_ABI, provider);
                
                // Get token info
                const name = await contract.name();
                const symbol = await contract.symbol();
                const decimals = await contract.decimals();
                
                console.log(`✅ Token Info: ${name} (${symbol}) - Decimals: ${decimals}`);
                
                // Get current account
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length > 0) {
                    const balance = await contract.balanceOf(accounts[0]);
                    let balanceFormatted;
                    if (ethers.version && ethers.version.startsWith('6')) {
                        balanceFormatted = ethers.formatUnits(balance, decimals);
                    } else {
                        balanceFormatted = ethers.utils.formatUnits(balance, decimals);
                    }
                    console.log(`✅ Your USDT BEP20 Balance: ${balanceFormatted} ${symbol}`);
                    showStatus('success', `USDT BEP20 working! Your balance: ${balanceFormatted} ${symbol}`);
                } else {
                    showStatus('success', `USDT BEP20 contract working! Token: ${name} (${symbol})`);
                }
                
            } catch (error) {
                console.error('USDT BEP20 contract test failed:', error);
                showStatus('error', 'USDT BEP20 contract test failed: ' + error.message);
            }
        }
        
        // Check wallet status on page load
        async function checkWalletStatusOnLoad() {
            console.log('=== Checking Wallet Status on Page Load ===');
            
            // Wait a bit for wallet to initialize
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            if (typeof window.ethereum !== 'undefined') {
                console.log('✅ Window.ethereum found on page load');
                
                try {
                    // Check accounts
                    const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                    console.log('Accounts on page load:', accounts);
                    
                    if (accounts.length > 0) {
                        console.log('✅ Wallet connected on page load:', accounts[0]);
                        walletAddress = accounts[0];
                        isWalletConnected = true;
                        
                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        // Auto-fill payment amount with plan amount
                        autoFillPaymentAmount();
                        
                        // Update UI to hide mobile connect button
                        updateWalletUI(true);
                        
                        showStatus('success', 'Wallet connected on page load! Address: ' + walletAddress);
                        return;
                    }
                    
                    // Check selectedAddress
                    if (window.ethereum.selectedAddress) {
                        console.log('✅ Wallet connected via selectedAddress on page load:', window.ethereum.selectedAddress);
                        walletAddress = window.ethereum.selectedAddress;
                        isWalletConnected = true;
                        
                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        showStatus('success', 'Wallet connected via selectedAddress! Address: ' + walletAddress);
                        return;
                    }
                    
                    console.log('⚠️ No wallet connection found on page load');
                    
                } catch (error) {
                    console.error('Error checking wallet on page load:', error);
                }
            } else {
                console.log('❌ No window.ethereum found on page load');
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Payment page - DOM Content Loaded');
            
            // Check wallet status first
            checkWalletStatusOnLoad();
            
            // Update UI based on current wallet state
            updateWalletUI(isWalletConnected);
            
            // Wait for wallet service to load
            let serviceWaitCount = 0;
            const maxServiceWait = 50; // 5 seconds max wait
            
            function waitForWalletService() {
            if (typeof window.walletService !== 'undefined') {
                    console.log('✅ Wallet service is available');
                walletService = window.walletService;
                    initializePayment();
                } else if (serviceWaitCount < maxServiceWait) {
                    console.log('⏳ Waiting for wallet service... (' + serviceWaitCount + '/' + maxServiceWait + ')');
                    serviceWaitCount++;
                    setTimeout(waitForWalletService, 100);
            } else {
                    console.error('❌ Wallet service failed to load after 5 seconds');
                showStatus('error', 'Wallet service not loaded. Please refresh the page.');
                }
            }

            function initializePayment() {
            // Event listeners
            document.getElementById('connectWalletBtn').addEventListener('click', connectWallet);
            document.getElementById('disconnectWalletBtn').addEventListener('click', disconnectWallet);
            document.getElementById('copyAddressBtn').addEventListener('click', copyAddress);
            document.getElementById('purchaseBtn').addEventListener('click', purchasePlan);

            // Check if wallet is already connected
            checkWalletConnection();
            }
            
            // Fallback initialization if wallet service fails to load
            setTimeout(() => {
                if (!walletService && typeof ethers !== 'undefined') {
                    console.log('Wallet service not available, using direct ethers.js for connection');
                    // Check wallet connection with direct ethers.js
                    checkWalletConnection();
                }
            }, 2000);
            
            // Start waiting for wallet service
            waitForWalletService();
        });

        async function connectWallet() {
            const connectBtn = document.getElementById('connectWalletBtn');
            const originalText = connectBtn.innerHTML;

            try {
                connectBtn.disabled = true;
                connectBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Connecting...';

                let result;
                
                // Try wallet service first, then fallback to direct ethers.js
                if (walletService) {
                    result = await walletService.connectWallet();
                } else {
                    result = await connectWalletDirect();
                }

                if (result.success) {
                    walletAddress = result.account;
                    isWalletConnected = true;

                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';

                    // Check balance and enable purchase
                    await checkBalanceAndEnablePurchase();
                    
                    // Auto-fill payment amount
                    autoFillPaymentAmount();
                    
                    // Update UI to hide mobile connect button
                    updateWalletUI(true);

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
        
        async function connectWalletDirect() {
            if (typeof window.ethereum === 'undefined') {
                throw new Error('No wallet detected. Please install Trust Wallet.');
            }

            try {
                // Request account access
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });

                if (accounts.length === 0) {
                    throw new Error('No accounts found');
                }

                const account = accounts[0];
                
                // Switch to BSC network
                try {
                    await window.ethereum.request({
                        method: 'wallet_switchEthereumChain',
                        params: [{ chainId: '0x38' }], // BSC mainnet
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
                    } else {
                        throw switchError;
                    }
                }

                // Save to localStorage
                localStorage.setItem('walletAddress', account);
                localStorage.setItem('walletType', 'trust');
                localStorage.setItem('walletConnected', 'true');

                return {
                    success: true,
                    account: account
                };

            } catch (error) {
                console.error('Direct wallet connection failed:', error);
                return {
                    success: false,
                    error: error.message
                };
            }
        }

        async function checkBalanceAndEnablePurchase() {
            try {
                console.log('=== Starting Balance Check ===');
                console.log('Wallet address:', walletAddress);
                console.log('Plan amount:', planAmount);
                console.log('Wallet service available:', !!walletService);
                console.log('Ethers.js available:', typeof ethers !== 'undefined');
                
                // Check if walletService is available
                if (!walletService) {
                    console.log('Wallet service not available, using direct ethers.js');
                    await checkBalanceWithEthers();
                    return;
                }
                
                // Get BNB balance using wallet service
                console.log('Using wallet service for balance check...');
                const balance = await walletService.getBalance();
                userBalance = parseFloat(balance);
                console.log('Balance from wallet service:', userBalance);
                
                updatePurchaseButton();
                
            } catch (error) {
                console.error('Balance check failed:', error);
                console.log('Trying direct ethers.js balance check...');
                
                // Fallback to direct ethers.js
                try {
                    await checkBalanceWithEthers();
                } catch (ethersError) {
                    console.error('Ethers balance check also failed:', ethersError);
                    showStatus('error', 'Failed to check balance: ' + ethersError.message);
                    
                    // Enable button anyway for testing
                    enablePurchaseButton('Test Mode');
                }
            }
        }
        
        async function checkBalanceWithEthers() {
            console.log('Starting ethers.js USDT balance check...');
            
            if (typeof ethers === 'undefined') {
                console.error('Ethers.js not loaded');
                throw new Error('Ethers.js not available');
            }
            
            if (!walletAddress) {
                console.error('No wallet address available');
                throw new Error('No wallet address');
            }
            
            console.log('Wallet address for USDT balance check:', walletAddress);
            
            // Switch to BSC network first
            try {
                await switchToBSCNetwork();
            } catch (error) {
                console.warn('Failed to switch to BSC network:', error);
                showStatus('warning', 'Please switch to BSC network manually for USDT BEP20 balance check');
            }
            
            try {
                // USDT BEP20 Contract Address on BSC (Tether USD)
                const USDT_BEP20_CONTRACT_ADDRESS = '0x55d398326f99059fF775485246999027B3197955';
                
                // USDT ABI (minimal for balance check)
                const USDT_ABI = [
                    {
                        "constant": true,
                        "inputs": [{"name": "_owner", "type": "address"}],
                        "name": "balanceOf",
                        "outputs": [{"name": "balance", "type": "uint256"}],
                        "type": "function"
                    },
                    {
                        "constant": true,
                        "inputs": [],
                        "name": "decimals",
                        "outputs": [{"name": "", "type": "uint8"}],
                        "type": "function"
                    }
                ];
                
                // Check ethers version and use appropriate syntax
                let provider, contract, balance, decimals;
                
                if (ethers.version && ethers.version.startsWith('6')) {
                    // Ethers.js v6 syntax
                    console.log('Using ethers.js v6 syntax for USDT balance');
                    provider = new ethers.BrowserProvider(window.ethereum);
                    contract = new ethers.Contract(USDT_BEP20_CONTRACT_ADDRESS, USDT_ABI, provider);
                } else {
                    // Ethers.js v5 syntax
                    console.log('Using ethers.js v5 syntax for USDT balance');
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                    contract = new ethers.Contract(USDT_BEP20_CONTRACT_ADDRESS, USDT_ABI, provider);
                }
                
                console.log('Connected to Trust Wallet provider');
                
                // Get USDT balance with error handling
                try {
                    balance = await contract.balanceOf(walletAddress);
                    console.log('Raw USDT balance from blockchain:', balance.toString());
                } catch (error) {
                    console.error('Failed to get USDT balance:', error);
                    throw new Error('Failed to get USDT balance. Please check your connection.');
                }
                
                // Get USDT decimals with error handling
                try {
                    decimals = await contract.decimals();
                    console.log('USDT decimals:', decimals);
                } catch (error) {
                    console.warn('Failed to get decimals from contract, using default 18:', error);
                    decimals = 18; // USDT typically has 18 decimals
                }
                
                // Convert balance to USDT units
                let balanceInUsdt;
                if (ethers.version && ethers.version.startsWith('6')) {
                    balanceInUsdt = ethers.formatUnits(balance, decimals);
                } else {
                    balanceInUsdt = ethers.utils.formatUnits(balance, decimals);
                }
                
                userBalance = parseFloat(balanceInUsdt);
                console.log('Formatted USDT balance:', userBalance);
                
                // Show balance to user
                showStatus('success', `Wallet connected! USDT BEP20 Balance: ${userBalance.toFixed(6)} USDT`);
                
                updatePurchaseButton();
                
            } catch (error) {
                console.error('USDT balance check failed:', error);
                
                // Set balance to 0 if check fails
                userBalance = 0;
                console.log('USDT balance set to 0 due to error');
                showStatus('error', 'Failed to get USDT balance. Please check your connection.');
                updatePurchaseButton();
            }
        }
        
        function updatePurchaseButton() {
            console.log('=== Updating Purchase Button ===');
            console.log('User balance:', userBalance);
            console.log('Plan amount:', planAmount);
                
                // For now, let's assume 1 BNB = $300 (you can get real-time rate later)
                const bnbToUsdRate = 300;
                const requiredBnb = planAmount / bnbToUsdRate;
            
            console.log('Required BNB:', requiredBnb);
            console.log('BNB to USD rate:', bnbToUsdRate);
                
                const balanceInfo = document.getElementById('balanceInfo');
                const balanceText = document.getElementById('balanceText');
                const purchaseBtn = document.getElementById('purchaseBtn');
            
            if (!balanceInfo || !balanceText || !purchaseBtn) {
                console.error('Required elements not found:', {
                    balanceInfo: !!balanceInfo,
                    balanceText: !!balanceText,
                    purchaseBtn: !!purchaseBtn
                });
                return;
            }
                
                if (userBalance >= requiredBnb) {
                console.log('✅ Sufficient balance - enabling purchase button');
                    balanceText.textContent = `Balance: ${userBalance.toFixed(4)} BNB ($${(userBalance * bnbToUsdRate).toFixed(2)}) - Sufficient for purchase`;
                    balanceInfo.className = 'mt-3 alert alert-success';
                enablePurchaseButton('Purchase Plan');
                } else {
                console.log('❌ Insufficient balance - disabling purchase button');
                    balanceText.textContent = `Balance: ${userBalance.toFixed(4)} BNB ($${(userBalance * bnbToUsdRate).toFixed(2)}) - Insufficient. Required: ${requiredBnb.toFixed(4)} BNB ($${planAmount})`;
                    balanceInfo.className = 'mt-3 alert alert-warning';
                enablePurchaseButton('Insufficient Balance', true);
                }
                
                balanceInfo.style.display = 'block';
            console.log('Balance info displayed');
        }
        
        function enablePurchaseButton(text, disabled = false) {
            console.log('=== Enabling Purchase Button ===');
            console.log('Button text:', text);
            console.log('Button disabled:', disabled);
            
                const purchaseBtn = document.getElementById('purchaseBtn');
            
            if (!purchaseBtn) {
                console.error('Purchase button not found!');
                return;
            }
            
            purchaseBtn.disabled = disabled;
            
            if (disabled) {
                purchaseBtn.innerHTML = `<i class="fa fa-exclamation-triangle me-2"></i>${text}`;
                console.log('❌ Button disabled with warning icon');
            } else {
                purchaseBtn.innerHTML = `<i class="fa fa-shopping-cart me-2"></i>${text}`;
                console.log('✅ Button enabled with cart icon');
            }
            
            console.log('Button state updated successfully');
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
            console.log('=== Purchase Plan Started ===');
            console.log('Wallet connected:', isWalletConnected);
            console.log('Wallet address:', walletAddress);
            
            // Debug: Check if all required elements exist
            console.log('=== Element Check ===');
            console.log('purchaseBtn:', !!document.getElementById('purchaseBtn'));
            console.log('paymentAmount:', !!document.getElementById('paymentAmount'));
            console.log('amount:', !!document.getElementById('amount'));
            console.log('adminWalletAddress:', !!document.getElementById('adminWalletAddress'));
            
            // Check if wallet is actually connected
            if (!isWalletConnected || !walletAddress) {
                console.log('Wallet not connected, attempting to reconnect...');
                await checkWalletConnection();
                
                if (!isWalletConnected || !walletAddress) {
                    showStatus('error', 'Please connect your wallet first. Click "Connect Wallet" button.');
                return;
                }
            }

            const purchaseBtn = document.getElementById('purchaseBtn');
            if (!purchaseBtn) {
                showStatus('error', 'Purchase button not found. Please refresh the page.');
                return;
            }
            const originalText = purchaseBtn.innerHTML;

            try {
                purchaseBtn.disabled = true;
                purchaseBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processing...';
                
                // Add cancel button
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'btn btn-danger btn-sm ms-2';
                cancelBtn.innerHTML = '<i class="fa fa-times me-1"></i>Cancel';
                cancelBtn.onclick = () => {
                    purchaseBtn.disabled = false;
                    purchaseBtn.innerHTML = originalText;
                    cancelBtn.remove();
                    showStatus('warning', 'Transaction cancelled by user');
                };
                
                // Check if parent node exists before appending
                if (purchaseBtn.parentNode) {
                    purchaseBtn.parentNode.appendChild(cancelBtn);
                } else {
                    console.warn('Purchase button parent node not found, cannot add cancel button');
                }

                // Get amount from input field (this is USD amount)
                const amountInput = document.getElementById('paymentAmount') || document.getElementById('amount');
                const inputAmount = parseFloat(amountInput ? amountInput.value : 0) || 0;
                console.log('Input amount (USD):', inputAmount);
                console.log('Plan amount (USD):', planAmount);
                
                // Use input amount if provided, otherwise use plan amount
                const actualAmount = inputAmount > 0 ? inputAmount : planAmount;
                console.log('Actual amount to use (USD):', actualAmount);
                
                // Calculate USDT amount needed (1 USD = 1 USDT)
                const grossAmount = actualAmount + depositFee; // Net + $1 fee
                const requiredUsdt = grossAmount; // 1 USD = 1 USDT
                
                // Round to 6 decimal places to avoid precision issues
                const roundedUsdt = Math.round(requiredUsdt * 1000000) / 1000000;
                console.log('Required USDT:', requiredUsdt);
                console.log('Rounded USDT:', roundedUsdt);
                console.log('User Balance:', userBalance);
                
                // Show user what they're sending
                showStatus('info', `Sending ${roundedUsdt.toFixed(6)} USDT BEP20 ($${grossAmount.toFixed(2)} incl. $1 fee) to admin wallet...`);
                
                // Check if user has enough USDT BEP20 balance
                if (userBalance < roundedUsdt) {
                    showStatus('error', `Insufficient USDT BEP20 balance! You have ${userBalance.toFixed(6)} USDT but need ${roundedUsdt.toFixed(6)} USDT. Please add more USDT BEP20 to your wallet.`);
                    purchaseBtn.disabled = false;
                    purchaseBtn.innerHTML = originalText;
                    if (cancelBtn && cancelBtn.remove) {
                        cancelBtn.remove();
                    }
                    return;
                }

                // Execute transaction
                const adminAddressElement = document.getElementById('adminWalletAddress');
                const adminAddress = adminAddressElement ? adminAddressElement.value : null;
                
                if (!adminAddress) {
                    showStatus('error', 'Admin wallet address not found. Please refresh the page.');
                    purchaseBtn.disabled = false;
                    purchaseBtn.innerHTML = originalText;
                    if (cancelBtn && cancelBtn.remove) {
                        cancelBtn.remove();
                    }
                    return;
                }
                let result;
                
                // Always use direct ethers.js for now since walletService signer is not properly set
                console.log('Using direct ethers.js for USDT transaction');
                result = await sendUsdtTransaction(adminAddress, roundedUsdt);

                if (result.success) {
                    // Save transaction to database
                    const txHash = result.transactionHash || result.txHash;
                    await saveTransactionToDatabase(txHash, grossAmount);
                    showStatus('success', 'Plan purchased successfully! Transaction: ' + txHash);
                } else {
                    showStatus('error', result.error || 'Transaction failed');
                }
            } catch (error) {
                showStatus('error', 'Purchase failed: ' + error.message);
            } finally {
                purchaseBtn.disabled = false;
                purchaseBtn.innerHTML = originalText;
                // Remove cancel button if it exists
                const cancelBtn = purchaseBtn.parentNode.querySelector('.btn-danger');
                if (cancelBtn) {
                    cancelBtn.remove();
                }
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
                        currency: 'USDT',
                        from_address: walletAddress,
                        to_address: document.getElementById('adminWalletAddress').value
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Failed to save transaction');
                }
                
                // Show success message and redirect
                showStatus('success', 'Payment processed successfully! Redirecting...');
                
                // Redirect to user dashboard after 2 seconds
                setTimeout(() => {
                    try {
                        window.location.href = '{{ route("user.index") }}';
                    } catch (e) {
                        // Fallback redirect
                        window.location.href = '/User-dashboard';
                    }
                }, 2000);
            } catch (error) {
                console.error('Failed to save transaction:', error);
                showStatus('error', 'Failed to save transaction: ' + error.message);
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

        async function checkWalletConnection() {
            console.log('=== Checking Wallet Connection ===');
            
            // Wait for ethers.js to load
            let ethersWaitCount = 0;
            const maxEthersWait = 50;
            
            while (typeof ethers === 'undefined' && ethersWaitCount < maxEthersWait) {
                console.log('Waiting for ethers.js... (' + ethersWaitCount + '/' + maxEthersWait + ')');
                await new Promise(resolve => setTimeout(resolve, 100));
                ethersWaitCount++;
            }
            
            if (typeof ethers === 'undefined') {
                console.error('Ethers.js not loaded after waiting');
                showStatus('error', 'Ethers.js library not loaded. Please refresh the page.');
                return;
            }
            
            console.log('Ethers.js loaded successfully');
            
            // Check if window.ethereum exists
            if (typeof window.ethereum === 'undefined') {
                console.log('❌ No window.ethereum found');
                showStatus('error', 'No wallet detected. Please install Trust Wallet app and refresh the page.');
                return;
            }
            
            console.log('✅ Window.ethereum found:', !!window.ethereum);
            
            // Check if already connected
            try {
                const currentAccounts = await window.ethereum.request({ method: 'eth_accounts' });
                console.log('Current accounts from eth_accounts:', currentAccounts);
                
                if (currentAccounts.length > 0) {
                    console.log('✅ Wallet already connected with accounts:', currentAccounts);
                    walletAddress = currentAccounts[0];
                    isWalletConnected = true;
                    
                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    
                    // Save to localStorage
                    localStorage.setItem('walletAddress', walletAddress);
                    localStorage.setItem('walletType', 'trust');
                    localStorage.setItem('walletConnected', 'true');
                    
                    // Check balance
                    await checkBalanceAndEnablePurchase();
                    
                    showStatus('success', 'Wallet already connected! Address: ' + walletAddress);
                    console.log('✅ Wallet connection restored:', walletAddress);
                    return;
                } else {
                    console.log('⚠️ No accounts in eth_accounts, checking selectedAddress');
                    
                    // Check if wallet is connected via selectedAddress
                    if (window.ethereum.selectedAddress) {
                        console.log('✅ Wallet connected via selectedAddress:', window.ethereum.selectedAddress);
                walletAddress = window.ethereum.selectedAddress;
                isWalletConnected = true;

                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        showStatus('success', 'Wallet connected via selectedAddress! Address: ' + walletAddress);
                        console.log('✅ Wallet connection restored via selectedAddress:', walletAddress);
                        return;
                    }
                }
            } catch (error) {
                console.error('Error checking current accounts:', error);
            }
            
            // Check database first - if user has saved wallet address
            @if(auth()->user()->wallet_address)
                const dbWalletAddress = '{{ auth()->user()->wallet_address }}';
                console.log('✅ Database user wallet address found:', dbWalletAddress);
                
                // Update state
                walletAddress = dbWalletAddress;
                isWalletConnected = true;
                
                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
                
                // Check balance and enable purchase
                await checkBalanceAndEnablePurchase();
                
                showStatus('success', 'Wallet connected from database! Ready for payment.');
                console.log('✅ User wallet state restored from database');
                return; // Exit early if database has wallet address
            @endif
            
            // Check localStorage
            const savedWalletAddress = localStorage.getItem('walletAddress');
            const savedWalletType = localStorage.getItem('walletType');
            const savedWalletConnected = localStorage.getItem('walletConnected');
            
            if (savedWalletAddress && savedWalletType && savedWalletConnected === 'true') {
                // Restore wallet state from localStorage
                walletAddress = savedWalletAddress;
                isWalletConnected = true;
                
                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
                
                // Check balance and enable purchase
                await checkBalanceAndEnablePurchase();
                
                console.log('User wallet restored from localStorage:', walletAddress);
                return;
            }
            
            // Check if wallet is already connected in browser
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                    if (accounts.length > 0) {
                        walletAddress = accounts[0];
                        isWalletConnected = true;

                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Check balance and enable purchase
                        await checkBalanceAndEnablePurchase();
                        
                        console.log('User wallet connected from browser:', walletAddress);
                    } else {
                        console.log('No accounts found in browser wallet');
                    }
                } catch (error) {
                    console.error('Error checking browser wallet:', error);
                }
            }
        }

        function debugWalletState() {
            console.log('=== WALLET STATE DEBUG ===');
            console.log('Wallet Address:', walletAddress);
            console.log('Is Wallet Connected:', isWalletConnected);
            console.log('User Balance:', userBalance);
            console.log('Plan Amount:', planAmount);
            console.log('Wallet Service Available:', !!walletService);
            console.log('Ethers.js Available:', typeof ethers !== 'undefined');
            console.log('Window Ethereum Available:', typeof window.ethereum !== 'undefined');
            
            // Check localStorage
            console.log('localStorage walletAddress:', localStorage.getItem('walletAddress'));
            console.log('localStorage walletType:', localStorage.getItem('walletType'));
            console.log('localStorage walletConnected:', localStorage.getItem('walletConnected'));
            
            // Check DOM elements
            const walletConnectSection = document.getElementById('walletConnectSection');
            const walletInfoSection = document.getElementById('walletInfoSection');
            const purchaseBtn = document.getElementById('purchaseBtn');
            const balanceInfo = document.getElementById('balanceInfo');
            
            console.log('DOM Elements:');
            console.log('- walletConnectSection display:', walletConnectSection ? walletConnectSection.style.display : 'NOT FOUND');
            console.log('- walletInfoSection display:', walletInfoSection ? walletInfoSection.style.display : 'NOT FOUND');
            console.log('- purchaseBtn disabled:', purchaseBtn ? purchaseBtn.disabled : 'NOT FOUND');
            console.log('- balanceInfo display:', balanceInfo ? balanceInfo.style.display : 'NOT FOUND');
            
            // Show status message
            showStatus('info', `Debug: Wallet=${walletAddress}, Balance=${userBalance}, Connected=${isWalletConnected}`);
        }
        
        function setManualBalance() {
            const manualBalanceInput = document.getElementById('manualBalance');
            const balance = parseFloat(manualBalanceInput.value);
            
            if (isNaN(balance) || balance < 0) {
                showStatus('error', 'Please enter a valid balance amount');
                return;
            }
            
            console.log('Setting manual balance:', balance);
            userBalance = balance;
            updatePurchaseButton();
            showStatus('success', `Balance set to ${balance} BNB for testing`);
        }
        
        async function forceWalletConnection() {
            console.log('=== Force Wallet Connection ===');
            
            try {
                showStatus('info', 'Attempting to connect wallet...');
                
                // Check if window.ethereum exists
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error', 'No wallet detected. Please install Trust Wallet.');
                    return;
                }
                
                console.log('Window.ethereum detected:', !!window.ethereum);
                
                // Try to connect using direct ethers.js
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
                
                console.log('Accounts received:', accounts);
                
                if (accounts.length > 0) {
                    walletAddress = accounts[0];
                    isWalletConnected = true;
                    
                    console.log('Wallet connected:', walletAddress);
                    
                    // Update UI immediately
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    
                    // Save to localStorage
                    localStorage.setItem('walletAddress', walletAddress);
                    localStorage.setItem('walletType', 'trust');
                    localStorage.setItem('walletConnected', 'true');
                    
                    // Initialize wallet service if available
                    if (walletService) {
                        try {
                            await walletService.connectWallet();
                            console.log('✅ Wallet service initialized');
                        } catch (serviceError) {
                            console.log('⚠️ Wallet service initialization failed, using direct ethers.js');
                        }
                    }
                    
                    // Check balance
                    await checkBalanceAndEnablePurchase();
                    
                    showStatus('success', 'Wallet connected successfully!');
                    console.log('✅ Wallet connected:', walletAddress);
                } else {
                    showStatus('error', 'No accounts found. Please unlock your wallet.');
                }
                
            } catch (error) {
                console.error('Force connection failed:', error);
                showStatus('error', 'Connection failed: ' + error.message);
            }
        }
        
        // Simple wallet connection function
        async function connectWalletSimple() {
            console.log('=== Simple Wallet Connection ===');
            
            try {
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error', 'No wallet detected. Please install Trust Wallet.');
                    return;
                }
                
                // Check if it's Trust Wallet
                const isTrustWallet = window.ethereum.isTrust || 
                                    window.ethereum.isTrustWallet ||
                                    (window.ethereum.providers && window.ethereum.providers.some(p => p.isTrust));
                
                // Reject MetaMask
                if (window.ethereum.isMetaMask) {
                    showStatus('error', 'MetaMask detected! Please disconnect MetaMask and use Trust Wallet only.');
                    return;
                }
                
                if (!isTrustWallet) {
                    showStatus('error', 'Trust Wallet not detected! Please install Trust Wallet app.');
                    return;
                } else {
                    console.log('✅ Trust Wallet detected');
                }
                
                showStatus('info', 'Connecting to Trust Wallet...');
                
                // Request accounts
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
                
                if (accounts.length > 0) {
                    walletAddress = accounts[0];
                    isWalletConnected = true;
                    
                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    
                    // Save to localStorage
                    localStorage.setItem('walletAddress', walletAddress);
                    localStorage.setItem('walletType', 'trust');
                    localStorage.setItem('walletConnected', 'true');
                    
                    // Check balance immediately
                    await checkBalanceAndEnablePurchase();
                    
                    showStatus('success', 'Trust Wallet connected successfully!');
                    console.log('✅ Simple wallet connection successful:', walletAddress);
                } else {
                    showStatus('error', 'No accounts found. Please unlock your wallet.');
                }
                
            } catch (error) {
                console.error('Simple connection failed:', error);
                showStatus('error', 'Connection failed: ' + error.message);
            }
        }
        
        async function sendTransactionDirect(toAddress, amount) {
            try {
                console.log('=== Direct Transaction Started ===');
                console.log('To address:', toAddress);
                console.log('Amount:', amount);
                
                if (typeof ethers === 'undefined') {
                    throw new Error('Ethers.js not loaded');
                }
                
                if (!window.ethereum) {
                    throw new Error('No wallet detected');
                }
                
                // Check if wallet is connected
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length === 0) {
                    throw new Error('No accounts connected. Please connect your wallet first.');
                }
                
                console.log('Connected account:', accounts[0]);
                
                // Check ethers version and use appropriate syntax
                let provider, signer, value, tx;
                
                // Round amount to 6 decimal places to avoid precision issues
                const roundedAmount = Math.round(amount * 1000000) / 1000000;
                console.log('Original amount:', amount);
                console.log('Rounded amount:', roundedAmount);
                
                // Validate amount is not too small
                if (roundedAmount < 0.000001) {
                    throw new Error('Amount too small. Minimum amount is 0.000001 BNB.');
                }
                
                if (ethers.version && ethers.version.startsWith('6')) {
                    // Ethers.js v6 syntax
                    console.log('Using ethers.js v6 syntax for transaction');
                    provider = new ethers.BrowserProvider(window.ethereum);
                    signer = await provider.getSigner();
                    value = ethers.parseEther(roundedAmount.toString());
                } else {
                    // Ethers.js v5 syntax
                    console.log('Using ethers.js v5 syntax for transaction');
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                    signer = provider.getSigner();
                    value = ethers.utils.parseEther(roundedAmount.toString());
                }
                
                console.log('Signer created:', !!signer);
                
                // Send transaction with longer timeout
                console.log('Sending transaction...');
                console.log('Transaction details:', {
                    to: toAddress,
                    value: value.toString(),
                    amount: amount
                });
                
                // Show user instruction
                showStatus('info', 'Please check your Trust Wallet app and approve the transaction!');
                
                tx = await Promise.race([
                    signer.sendTransaction({
                        to: toAddress,
                        value: value
                    }),
                    new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Transaction timeout after 30 seconds. Please check Trust Wallet app and approve the transaction!')), 30000)
                    )
                ]);
                
                console.log('Transaction sent:', tx.hash);
                console.log('Waiting for confirmation...');
                
                return {
                    success: true,
                    transactionHash: tx.hash,
                    tx: tx
                };
                
            } catch (error) {
                console.error('Direct transaction failed:', error);
                return {
                    success: false,
                    error: error.message
                };
            }
        }

        async function sendUsdtTransaction(toAddress, amount) {
            try {
                console.log('=== USDT Transaction Started ===');
                console.log('To address:', toAddress);
                console.log('Amount:', amount);
                
                if (typeof ethers === 'undefined') {
                    throw new Error('Ethers.js not loaded');
                }
                
                if (!window.ethereum) {
                    throw new Error('No wallet detected');
                }
                
                // Check if wallet is connected
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                if (accounts.length === 0) {
                    throw new Error('No accounts connected. Please connect your wallet first.');
                }
                
                console.log('Connected account:', accounts[0]);
                
                // Switch to BSC network
                await switchToBSCNetwork();
                
                // USDT BEP20 Contract Address on BSC (Tether USD)
                const USDT_BEP20_CONTRACT_ADDRESS = '0x55d398326f99059fF775485246999027B3197955';
                
                // USDT ABI (minimal for transfer)
                const USDT_ABI = [
                    {
                        "constant": false,
                        "inputs": [
                            {"name": "_to", "type": "address"},
                            {"name": "_value", "type": "uint256"}
                        ],
                        "name": "transfer",
                        "outputs": [{"name": "", "type": "bool"}],
                        "type": "function"
                    },
                    {
                        "constant": true,
                        "inputs": [],
                        "name": "decimals",
                        "outputs": [{"name": "", "type": "uint8"}],
                        "type": "function"
                    }
                ];
                
                // Check ethers version and use appropriate syntax
                let provider, signer, contract, tx;
                
                // Round amount to 6 decimal places to avoid precision issues
                const roundedAmount = Math.round(amount * 1000000) / 1000000;
                console.log('Original amount:', amount);
                console.log('Rounded amount:', roundedAmount);
                
                // Validate amount is not too small
                if (roundedAmount < 0.000001) {
                    throw new Error('Amount too small. Minimum amount is 0.000001 USDT.');
                }
                
                if (ethers.version && ethers.version.startsWith('6')) {
                    // Ethers.js v6 syntax
                    console.log('Using ethers.js v6 syntax for USDT BEP20 transaction');
                    provider = new ethers.BrowserProvider(window.ethereum);
                    signer = await provider.getSigner();
                    contract = new ethers.Contract(USDT_BEP20_CONTRACT_ADDRESS, USDT_ABI, signer);
                } else {
                    // Ethers.js v5 syntax
                    console.log('Using ethers.js v5 syntax for USDT BEP20 transaction');
                    provider = new ethers.providers.Web3Provider(window.ethereum);
                    signer = provider.getSigner();
                    contract = new ethers.Contract(USDT_BEP20_CONTRACT_ADDRESS, USDT_ABI, signer);
                }
                
                console.log('Contract created:', !!contract);
                
                // Get USDT decimals with error handling
                let decimals;
                try {
                    decimals = await contract.decimals();
                    console.log('USDT decimals:', decimals);
                } catch (error) {
                    console.warn('Failed to get decimals from contract, using default 18:', error);
                    decimals = 18; // USDT typically has 18 decimals
                }
                
                // Convert amount to wei (USDT has 18 decimals)
                let amountWei;
                if (ethers.version && ethers.version.startsWith('6')) {
                    amountWei = ethers.parseUnits(roundedAmount.toString(), decimals);
                } else {
                    amountWei = ethers.utils.parseUnits(roundedAmount.toString(), decimals);
                }
                
                console.log('Amount in wei:', amountWei.toString());
                
                // Send USDT transaction
                console.log('Sending USDT transaction...');
                console.log('Transaction details:', {
                    to: toAddress,
                    amount: roundedAmount,
                    amountWei: amountWei.toString()
                });
                
                // Show user instruction
                showStatus('info', 'Please check your Trust Wallet app and approve the USDT transaction!');
                
                tx = await Promise.race([
                    contract.transfer(toAddress, amountWei),
                    new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Transaction timeout after 30 seconds. Please check Trust Wallet app and approve the transaction!')), 30000)
                    )
                ]);
                
                console.log('USDT transaction sent:', tx.hash);
                console.log('Waiting for confirmation...');
                
                return { success: true, txHash: tx.hash, tx };
            } catch (error) {
                console.error('USDT transaction failed:', error);
                return { success: false, error: error.message };
            }
        }

        function resetStuckTransaction() {
            console.log('=== Resetting Stuck Transaction ===');
            
            // Reset button state
            const purchaseBtn = document.getElementById('purchaseBtn');
            purchaseBtn.disabled = false;
            purchaseBtn.innerHTML = '<i class="fa fa-shopping-cart me-2"></i>Purchase Plan';
            
            // Remove cancel button if exists
            const cancelBtn = purchaseBtn.parentNode.querySelector('.btn-danger');
            if (cancelBtn) {
                cancelBtn.remove();
            }
            
            // Reset wallet state
            isWalletConnected = false;
            walletAddress = null;
            
            // Show connect section
            document.getElementById('walletConnectSection').style.display = 'block';
            document.getElementById('walletInfoSection').style.display = 'none';
            
            showStatus('warning', 'Transaction reset. Please connect wallet and try again.');
            console.log('✅ Transaction reset successfully');
        }
        
        // Check if Trust Wallet popup is blocked
        function checkTrustWalletPopup() {
            console.log('=== Checking Trust Wallet Popup ===');
            
            // Check if popup is blocked
            const popup = window.open('', '_blank', 'width=1,height=1');
            if (!popup || popup.closed || typeof popup.closed == 'undefined') {
                console.log('❌ Popup blocked - Trust Wallet may not open');
                showStatus('error', 'Popup blocked! Please allow popups for this site and try again.');
                return false;
            } else {
                console.log('✅ Popup allowed');
                popup.close();
                return true;
            }
        }
        
        // Force transaction with popup check
        async function forceTransaction() {
            console.log('=== Force Transaction ===');
            
            // Check popup first
            if (!checkTrustWalletPopup()) {
                return;
            }
            
            try {
                showStatus('info', 'Forcing transaction...');
                
                // Check if wallet is connected
                if (!isWalletConnected || !walletAddress) {
                    showStatus('error', 'Wallet not connected. Please connect first.');
                    return;
                }
                
                // Get admin address
                const adminAddress = document.getElementById('adminWalletAddress').value;
                const requiredBnb = 0.0033333333333333335; // 1 USD / 300 BNB rate
                
                console.log('Force transaction details:', {
                    adminAddress,
                    requiredBnb,
                    walletAddress
                });
                
                // Try direct transaction
                const result = await sendTransactionDirect(adminAddress, requiredBnb);
                
                if (result.success) {
                    showStatus('success', 'Transaction successful! Hash: ' + result.transactionHash);
                } else {
                    showStatus('error', 'Transaction failed: ' + result.error);
                }
                
            } catch (error) {
                console.error('Force transaction failed:', error);
                showStatus('error', 'Force transaction failed: ' + error.message);
            }
        }
        
        // Check Trust Wallet app status
        function checkTrustWalletStatus() {
            console.log('=== Checking Trust Wallet Status ===');
            
            try {
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error', 'No wallet detected. Please install Trust Wallet app.');
                    return false;
                }
                
                console.log('Window.ethereum found:', !!window.ethereum);
                console.log('Ethereum object:', window.ethereum);
                
                // Check if it's Trust Wallet - strict check only
                const isTrustWallet = window.ethereum.isTrust || 
                                    window.ethereum.isTrustWallet ||
                                    (window.ethereum.providers && window.ethereum.providers.some(p => p.isTrust));
                
                // Reject MetaMask
                const isMetaMask = window.ethereum.isMetaMask;
                
                console.log('Trust Wallet detection:', {
                    isTrust: window.ethereum.isTrust,
                    isTrustWallet: window.ethereum.isTrustWallet,
                    isMetaMask: window.ethereum.isMetaMask,
                    providers: window.ethereum.providers,
                    isTrustWallet: isTrustWallet
                });
                
                if (isMetaMask) {
                    showStatus('error', 'MetaMask detected! Please disconnect MetaMask and use Trust Wallet only. Close MetaMask extension and refresh the page.');
                    console.log('❌ MetaMask detected - not allowed');
                    return false;
                } else if (!isTrustWallet) {
                    showStatus('error', 'Trust Wallet not detected! Please install Trust Wallet app and connect it.');
                    console.log('❌ Trust Wallet not detected');
                    return false;
                } else {
                    showStatus('success', 'Trust Wallet detected and ready!');
                    console.log('✅ Trust Wallet detected');
                }
                
                // Check if accounts are connected
                if (window.ethereum.selectedAddress) {
                    console.log('Selected address:', window.ethereum.selectedAddress);
                    showStatus('info', 'Wallet connected: ' + window.ethereum.selectedAddress);
                } else {
                    console.log('No selected address');
                    showStatus('info', 'Wallet detected but not connected. Please connect your wallet.');
                }
                
                return true;
                
            } catch (error) {
                console.error('Error checking Trust Wallet status:', error);
                showStatus('error', 'Error checking wallet status: ' + error.message);
                return false;
            }
        }
        
        // Show Trust Wallet instructions
        function showTrustWalletInstructions() {
            const statusDiv = document.getElementById('statusMessages');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fa fa-mobile-alt me-2"></i>Trust Wallet Transaction Instructions</h6>
                    <p><strong>Steps to complete payment:</strong></p>
                    <ol>
                        <li>Make sure Trust Wallet app is open on your phone</li>
                        <li>Look for a notification or popup asking to approve the transaction</li>
                        <li>Check the transaction details (Amount: 1 USDT, To: Admin address)</li>
                        <li>Tap "Approve" or "Confirm" in Trust Wallet</li>
                        <li>Wait for transaction confirmation</li>
                    </ol>
                    <div class="mt-3">
                        <button class="btn btn-warning btn-sm me-2" onclick="checkTrustWalletStatus()">
                            <i class="fa fa-check me-1"></i>Check Trust Wallet Status
                        </button>
                        <button class="btn btn-info btn-sm" onclick="forceTransaction()">
                            <i class="fa fa-bolt me-1"></i>Try Again
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Test wallet functionality
        async function testWalletFunctionality() {
            console.log('=== Testing Wallet Functionality ===');
            
            try {
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error', 'No wallet detected. Please install Trust Wallet.');
                    return false;
                }
                
                // Check for MetaMask first
                if (window.ethereum.isMetaMask) {
                    showStatus('error', 'MetaMask detected! Please disconnect MetaMask and use Trust Wallet only.');
                    return false;
                }
                
                // Test account access
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                console.log('Accounts found:', accounts);
                
                if (accounts.length === 0) {
                    showStatus('warning', 'No accounts connected. Please connect your wallet first.');
                    return false;
                }
                
                // Test network
                const chainId = await window.ethereum.request({ method: 'eth_chainId' });
                console.log('Chain ID:', chainId);
                
                if (chainId !== '0x38') { // BSC mainnet
                    showStatus('warning', 'Please switch to BSC network in Trust Wallet.');
                    return false;
                }
                
                showStatus('success', 'Trust Wallet is working properly! Ready for transactions.');
                return true;
                
            } catch (error) {
                console.error('Wallet test failed:', error);
                showStatus('error', 'Wallet test failed: ' + error.message);
                return false;
            }
        }
        
        // Show MetaMask removal instructions
        function showMetaMaskRemovalInstructions() {
            const statusDiv = document.getElementById('statusMessages');
            statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fa fa-exclamation-triangle me-2"></i>MetaMask Detected - Please Remove</h6>
                    <p><strong>Steps to remove MetaMask and use Trust Wallet:</strong></p>
                    <ol>
                        <li>Click on MetaMask extension icon in your browser</li>
                        <li>Click on the three dots (⋮) menu</li>
                        <li>Select "Disconnect" or "Remove"</li>
                        <li>Close all browser tabs</li>
                        <li>Open Trust Wallet app on your phone</li>
                        <li>Refresh this page</li>
                        <li>Click "Connect Wallet" to connect Trust Wallet</li>
                    </ol>
                    <div class="mt-3">
                        <button class="btn btn-warning btn-sm me-2" onclick="location.reload()">
                            <i class="fa fa-refresh me-1"></i>Refresh Page
                        </button>
                        <button class="btn btn-info btn-sm" onclick="checkTrustWalletStatus()">
                            <i class="fa fa-check me-1"></i>Check Status Again
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Detect and connect Trust Wallet
        async function detectAndConnectTrustWallet() {
            console.log('=== Detecting Trust Wallet ===');
            
            // Check if we're on mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            console.log('Is mobile device:', isMobile);
            
            if (isMobile) {
                console.log('📱 Mobile device detected - trying mobile connection');
                await connectMobileTrustWallet();
                return;
            }
            
            // Check for window.ethereum
            if (typeof window.ethereum === 'undefined') {
                showStatus('error', 'No wallet detected. Please install Trust Wallet browser extension or use Trust Wallet mobile app.');
                showTrustWalletInstallInstructions();
                return;
            }
            
            // Check if it's Trust Wallet
            const isTrustWallet = window.ethereum.isTrust || 
                                window.ethereum.isTrustWallet ||
                                (window.ethereum.providers && window.ethereum.providers.some(p => p.isTrust));
            
            if (window.ethereum.isMetaMask) {
                showStatus('error', 'MetaMask detected! Please disable MetaMask and use Trust Wallet only.');
                showMetaMaskRemovalInstructions();
                return;
            }
            
            if (!isTrustWallet) {
                showStatus('warning', 'Wallet detected but not Trust Wallet. Please use Trust Wallet for best experience.');
                showTrustWalletInstallInstructions();
                return;
            }
            
            // Try to connect
            try {
                showStatus('info', 'Connecting to Trust Wallet...');
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                
                if (accounts.length > 0) {
                    walletAddress = accounts[0];
                    isWalletConnected = true;
                    
                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    
                    // Save to localStorage
                    localStorage.setItem('walletAddress', walletAddress);
                    localStorage.setItem('walletType', 'trust');
                    localStorage.setItem('walletConnected', 'true');
                    
                    // Check balance
                    await checkBalanceAndEnablePurchase();
                    
                    showStatus('success', 'Trust Wallet connected successfully!');
                    console.log('✅ Trust Wallet connected:', walletAddress);
                } else {
                    showStatus('error', 'No accounts found. Please unlock your Trust Wallet.');
                }
                
            } catch (error) {
                console.error('Trust Wallet connection failed:', error);
                showStatus('error', 'Connection failed: ' + error.message);
            }
        }
        
        // Show Trust Wallet install instructions
        function showTrustWalletInstallInstructions() {
            const statusDiv = document.getElementById('statusMessages');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fa fa-download me-2"></i>Install Trust Wallet</h6>
                    <p><strong>Choose your device:</strong></p>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>📱 Mobile (Recommended)</h6>
                            <ol>
                                <li>Download Trust Wallet app from App Store/Play Store</li>
                                <li>Open Trust Wallet app</li>
                                <li>Go to DApp browser</li>
                                <li>Visit this page: <code>${window.location.href}</code></li>
                                <li>Click "Connect Wallet"</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>💻 Desktop</h6>
                            <ol>
                                <li>Install Trust Wallet browser extension</li>
                                <li>Open Trust Wallet extension</li>
                                <li>Create or import wallet</li>
                                <li>Refresh this page</li>
                                <li>Click "Connect Wallet"</li>
                            </ol>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm me-2">
                            <i class="fa fa-download me-1"></i>Download Trust Wallet
                        </a>
                        <button class="btn btn-info btn-sm" onclick="detectAndConnectTrustWallet()">
                            <i class="fa fa-refresh me-1"></i>Try Again
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Connect Trust Wallet on mobile
        async function connectMobileTrustWallet() {
            console.log('=== Mobile Trust Wallet Connection ===');
            
            try {
                // Check if Trust Wallet is already available
                if (typeof window.ethereum !== 'undefined') {
                    console.log('✅ Trust Wallet already available on mobile');
                    console.log('Ethereum object:', window.ethereum);
                    
                    // Check if it's Trust Wallet
                    const isTrustWallet = window.ethereum.isTrust || 
                                        window.ethereum.isTrustWallet ||
                                        (window.ethereum.providers && window.ethereum.providers.some(p => p.isTrust));
                    
                    console.log('Trust Wallet detection:', {
                        isTrust: window.ethereum.isTrust,
                        isTrustWallet: window.ethereum.isTrustWallet,
                        isMetaMask: window.ethereum.isMetaMask,
                        providers: window.ethereum.providers,
                        isTrustWallet: isTrustWallet
                    });
                    
                    if (window.ethereum.isMetaMask) {
                        showStatus('error', 'MetaMask detected on mobile! Please use Trust Wallet app instead.');
                        return;
                    }
                    
                    if (!isTrustWallet) {
                        showStatus('warning', 'Wallet detected but not Trust Wallet. Please use Trust Wallet app.');
                        showMobileTrustWalletInstructions();
                        return;
                    }
                    
                    // Check if already connected
                    const currentAccounts = await window.ethereum.request({ method: 'eth_accounts' });
                    console.log('Current accounts:', currentAccounts);
                    
                    if (currentAccounts.length > 0) {
                        console.log('✅ Already connected with accounts:', currentAccounts);
                        walletAddress = currentAccounts[0];
                        isWalletConnected = true;
                        
                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        showStatus('success', 'Trust Wallet already connected! Wallet: ' + walletAddress);
                        console.log('✅ Trust Wallet already connected on mobile:', walletAddress);
                        return;
                    }
                    
                    // Try to connect
                    showStatus('info', 'Connecting to Trust Wallet on mobile...');
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    
                    if (accounts.length > 0) {
                        walletAddress = accounts[0];
                        isWalletConnected = true;
                        
                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        showStatus('success', 'Trust Wallet connected successfully on mobile!');
                        console.log('✅ Trust Wallet connected on mobile:', walletAddress);
                    } else {
                        showStatus('error', 'No accounts found. Please unlock your Trust Wallet.');
                    }
                    
                } else {
                    console.log('❌ No window.ethereum on mobile - showing instructions');
                    showMobileTrustWalletInstructions();
                }
                
            } catch (error) {
                console.error('Mobile Trust Wallet connection failed:', error);
                showStatus('error', 'Mobile connection failed: ' + error.message);
                showMobileTrustWalletInstructions();
            }
        }
        
        // Force check wallet status
        async function forceCheckWallet() {
            console.log('=== Force Checking Wallet Status ===');
            
            try {
                // Check if window.ethereum exists
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error', 'No wallet detected. Please install Trust Wallet app.');
                    return;
                }
                
                console.log('✅ Window.ethereum found');
                console.log('Ethereum object:', window.ethereum);
                
                // Check if it's Trust Wallet
                const isTrustWallet = window.ethereum.isTrust || 
                                    window.ethereum.isTrustWallet ||
                                    (window.ethereum.providers && window.ethereum.providers.some(p => p.isTrust));
                
                console.log('Trust Wallet detection:', {
                    isTrust: window.ethereum.isTrust,
                    isTrustWallet: window.ethereum.isTrustWallet,
                    isMetaMask: window.ethereum.isMetaMask,
                    providers: window.ethereum.providers,
                    isTrustWallet: isTrustWallet
                });
                
                if (window.ethereum.isMetaMask) {
                    showStatus('error', 'MetaMask detected! Please use Trust Wallet only.');
                    return;
                }
                
                if (!isTrustWallet) {
                    showStatus('warning', 'Wallet detected but not Trust Wallet. Please use Trust Wallet.');
                    return;
                }
                
                // Check current accounts
                const accounts = await window.ethereum.request({ method: 'eth_accounts' });
                console.log('Current accounts:', accounts);
                
                if (accounts.length > 0) {
                    console.log('✅ Accounts found:', accounts);
                    walletAddress = accounts[0];
                    isWalletConnected = true;
                    
                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    
                    // Save to localStorage
                    localStorage.setItem('walletAddress', walletAddress);
                    localStorage.setItem('walletType', 'trust');
                    localStorage.setItem('walletConnected', 'true');
                    
                    // Check balance
                    await checkBalanceAndEnablePurchase();
                    
                    showStatus('success', 'Wallet found and connected! Address: ' + walletAddress);
                    console.log('✅ Wallet force check successful:', walletAddress);
                } else {
                    console.log('⚠️ No accounts in eth_accounts, checking selectedAddress');
                    
                    // Check if wallet is connected via selectedAddress
                    if (window.ethereum.selectedAddress) {
                        console.log('✅ Wallet connected via selectedAddress:', window.ethereum.selectedAddress);
                        walletAddress = window.ethereum.selectedAddress;
                        isWalletConnected = true;
                        
                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';
                        
                        // Save to localStorage
                        localStorage.setItem('walletAddress', walletAddress);
                        localStorage.setItem('walletType', 'trust');
                        localStorage.setItem('walletConnected', 'true');
                        
                        // Check balance
                        await checkBalanceAndEnablePurchase();
                        
                        showStatus('success', 'Wallet connected via selectedAddress! Address: ' + walletAddress);
                        console.log('✅ Wallet force check successful via selectedAddress:', walletAddress);
                    } else {
                        showStatus('warning', 'Trust Wallet detected but no accounts connected. Please connect your wallet.');
                        console.log('⚠️ No accounts connected');
                    }
                }
                
            } catch (error) {
                console.error('Force check wallet failed:', error);
                showStatus('error', 'Force check failed: ' + error.message);
            }
        }
        
        // Show mobile Trust Wallet instructions
        function showMobileTrustWalletInstructions() {
            const statusDiv = document.getElementById('statusMessages');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fa fa-mobile-alt me-2"></i>Mobile Trust Wallet Connection</h6>
                    <p><strong>Steps to connect on mobile:</strong></p>
                    <ol>
                        <li>Make sure Trust Wallet app is installed on your phone</li>
                        <li>Open Trust Wallet app</li>
                        <li>Go to DApp browser in Trust Wallet</li>
                        <li>Visit this page: <code>${window.location.href}</code></li>
                        <li>Click "Connect Trust Wallet" button</li>
                    </ol>
                    <div class="mt-3">
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm me-2">
                            <i class="fa fa-download me-1"></i>Download Trust Wallet
                        </a>
                        <button class="btn btn-info btn-sm" onclick="connectMobileTrustWallet()">
                            <i class="fa fa-refresh me-1"></i>Try Again
                        </button>
                    </div>
                </div>
            `;
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
