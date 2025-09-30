@extends('layouts.admin')

@section('content')
    <style>
        /* Page background */
        .content-wrapper {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            padding-top: 80px;
            min-height: calc(100vh - 80px);
            color: #fff;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
            color: #3bd17a;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* Card style */
        .card {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 20px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
            color: #fff;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.7);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        .card-body {
            padding: 2rem;
        }

        /* Wallet Connect Button */
        .wallet-connect-btn {
            background: linear-gradient(45deg, #3375bb, #4a90e2);
            border: none;
            border-radius: 15px;
            padding: 18px 35px;
            color: white;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(51, 117, 188, 0.4);
            position: relative;
            overflow: hidden;
        }

        .wallet-connect-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .wallet-connect-btn:hover::before {
            left: 100%;
        }

        .wallet-connect-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(51, 117, 188, 0.5);
            color: white;
        }

        .wallet-connect-btn:active {
            transform: translateY(-1px);
        }

        /* Test Button */
        .btn-warning {
            background: linear-gradient(45deg, #ffc107, #ff8c00);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        /* Wallet Info */
        .wallet-info {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 20px;
            padding: 2rem;
            border: 2px solid rgba(59, 209, 122, 0.3);
            position: relative;
            overflow: hidden;
        }

        .wallet-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        .wallet-address {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.4);
            padding: 15px;
            border-radius: 12px;
            word-break: break-all;
            font-size: 14px;
            border: 1px solid rgba(59, 209, 122, 0.2);
            position: relative;
        }

        .wallet-address::before {
            content: '🔗';
            position: absolute;
            top: -8px;
            left: 10px;
            background: #072d42;
            padding: 0 5px;
            font-size: 12px;
        }

        /* Feature Cards */
        .feature-card {
            background: rgba(59, 209, 122, 0.1);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(59, 209, 122, 0.15);
            transform: translateY(-2px);
        }

        .feature-card h6 {
            color: #3bd17a;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-card ul {
            margin-bottom: 0;
        }

        .feature-card li {
            color: #fff;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
        }

        .feature-card li::before {
            content: '✓';
            color: #3bd17a;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        /* Status Messages */
        .status-message {
            padding: 18px;
            border-radius: 12px;
            margin: 15px 0;
            font-weight: 600;
            border-left: 4px solid;
            position: relative;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .status-success {
            background: rgba(59, 209, 122, 0.15);
            border-left-color: #3bd17a;
            color: #3bd17a;
        }

        .status-error {
            background: rgba(220, 53, 69, 0.15);
            border-left-color: #dc3545;
            color: #dc3545;
        }

        .status-info {
            background: rgba(13, 202, 240, 0.15);
            border-left-color: #0dcaf0;
            color: #0dcaf0;
        }

        .status-warning {
            background: rgba(255, 193, 7, 0.15);
            border-left-color: #ffc107;
            color: #ffc107;
        }

        /* Action Buttons */
        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-success {
            border: 2px solid #3bd17a;
            color: #3bd17a;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background: #3bd17a;
            color: white;
            transform: translateY(-2px);
        }

        /* Badge Styles */
        .badge {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .badge-primary {
            background: linear-gradient(45deg, #3375bb, #4a90e2);
        }

        .badge-success {
            background: linear-gradient(45deg, #3bd17a, #00d4ff);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .wallet-connect-btn {
                padding: 15px 25px;
                font-size: 16px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Status Indicators */
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        /* Wallet Connection Section */
        .wallet-connection-section {
            background: linear-gradient(135deg, rgba(59, 209, 122, 0.1), rgba(0, 212, 255, 0.1));
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(59, 209, 122, 0.2);
            position: relative;
            overflow: hidden;
        }

        .wallet-connection-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Feature Grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            background: rgba(59, 209, 122, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(59, 209, 122, 0.1);
            transform: translateY(-5px);
        }

        .feature-item i {
            font-size: 2.5rem;
            color: #3bd17a;
            margin-bottom: 1rem;
        }

        .feature-item h6 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-item p {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .status-block {
            margin-right: 24px;
            /* gap between blocks */
        }
    </style>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">
                                <i class="fa fa-wallet me-3"></i> Admin Wallet Connect
                            </h1>
                            <p class="mb-0">Connect your Trust Wallet to manage transactions and view balances</p>
                        </div>
                        <div class="status-block d-flex align-items-center">
                            <div class="status-indicator bg-success me-2"></div>
                            <span class="text-success fw-bold">Secure</span>
                        </div>

                        <div class="status-block d-flex align-items-center">
                            <div class="status-indicator bg-info me-2"></div>
                            <span class="text-info fw-bold me-3">BSC Network</span>
                        </div>

                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Wallet Connection Section -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="wallet-icon" style="margin-right:15px;">
                                        <i class="fa fa-wallet fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-white mb-1">Trust Wallet Connection</h5>
                                        <p class="text-muted mb-0">Connect your wallet to manage admin transactions</p>
                                    </div>
                                </div>


                                @if ($adminWalletAddress)
                                    <div class="alert alert-success mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-check-circle me-3 fa-2x"></i>
                                            <div>
                                                <strong>Current Admin Wallet Address:</strong><br>
                                                <code class="text-dark">{{ $adminWalletAddress }}</code>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Trust Wallet Connect -->
                                <div id="walletConnectSection">
                                    <div class="wallet-connection-section">
                                        <h6 class="text-white mb-4">
                                            <i class="fa fa-mobile-alt me-2"></i> Connect Your Trust Wallet
                                        </h6>

                                        <!-- Trust Wallet Option -->
                                        <div class="text-center">
                                            <button class="wallet-connect-btn mb-3" onclick="connectMobileWallet('trust')">
                                                <i class="fa fa-wallet me-2"></i> Connect Trust Wallet
                                            </button>
                                            <p class="text-muted mb-4">Mobile & Desktop Trust Wallet DApp</p>

                                            <!-- Test Button -->
                                            <button class="btn btn-warning btn-sm" onclick="testButton()">
                                                <i class="fa fa-test-tube me-1"></i>Test Connection
                                            </button>

                                            <!-- Force Check Wallet Button -->
                                            <button class="btn btn-info btn-sm ms-2" onclick="forceCheckWallet()">
                                                <i class="fa fa-refresh me-1"></i>Force Check Wallet
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Wallet Info (Hidden by default) -->
                                <div id="walletInfoSection" class="wallet-info" style="display: none;">
                                    <div class="d-flex align-items-center mb-4">
                                        <i class="fa fa-check-circle fa-2x text-success me-3"></i>
                                        <div>
                                            <h5 class="text-success mb-1">Wallet Connected Successfully</h5>
                                            <p class="text-muted mb-0">Your wallet is ready for transactions</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-white fw-bold">Wallet Address:</label>
                                            <div id="walletAddress" class="wallet-address"></div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-white fw-bold">Wallet Type:</label>
                                            <div class="mt-2">
                                                <span id="walletType" class="badge badge-primary">Unknown</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-white fw-bold">Network:</label>
                                            <div class="mt-2">
                                                <span class="badge badge-success">Binance Smart Chain</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mt-4">
                                        <button id="disconnectWalletBtn" class="btn btn-outline-danger">
                                            <i class="fa fa-times me-2"></i>Disconnect Wallet
                                        </button>
                                        <button id="refreshWalletBtn" class="btn btn-outline-success">
                                            <i class="fa fa-refresh me-2"></i>Refresh Status
                                        </button>
                                    </div>
                                </div>

                                <!-- Status Messages -->
                                <div id="statusMessages"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Features Sidebar -->
                    <div class="col-lg-4" hidden>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-white mb-4">
                                    <i class="fa fa-star me-2"></i>DApp Features
                                </h5>

                                <div class="feature-grid">
                                    <div class="feature-item">
                                        <i class="fa fa-mobile-alt"></i>
                                        <h6>Mobile DApp</h6>
                                        <p>Direct Trust Wallet app connection</p>
                                    </div>

                                    <div class="feature-item">
                                        <i class="fa fa-coins"></i>
                                        <h6>Balance View</h6>
                                        <p>Real-time BNB & USDT balances</p>
                                    </div>

                                    <div class="feature-item">
                                        <i class="fa fa-paper-plane"></i>
                                        <h6>Send Transactions</h6>
                                        <p>Send crypto to any address</p>
                                    </div>

                                    <div class="feature-item">
                                        <i class="fa fa-cog"></i>
                                        <h6>Admin Management</h6>
                                        <p>Manage admin wallet settings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    // Re-initialize wallet after ethers loads
                    if (typeof initializeWallet === 'function') {
                        initializeWallet();
                    }
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

    <script>
        // Global variables
        let walletAddress = null;
        let walletType = null;
        let isWalletConnected = false;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin Wallet - DOM Content Loaded');
            console.log('Admin wallet address from server:', '{{ $adminWalletAddress ?? 'null' }}');

            // Initialize wallet immediately (ethers.js will be loaded by the script above)
            initializeWallet();

            function initializeWallet() {
                console.log('🔧 Initializing admin wallet...');

                // Event listeners
                const disconnectBtn = document.getElementById('disconnectWalletBtn');
                const refreshBtn = document.getElementById('refreshWalletBtn');

                if (disconnectBtn) {
                    disconnectBtn.addEventListener('click', disconnectWallet);
                }
                if (refreshBtn) {
                    refreshBtn.addEventListener('click', refreshWallet);
                }

                // Check if wallet is already connected
                checkWalletConnection();
            }
        });

        // Trust Wallet connection function (mobile DApp support)
        window.connectMobileWallet = async function(walletType) {
            console.log('Admin Trust Wallet connection:', walletType);

            // Only allow Trust Wallet
            if (walletType !== 'trust') {
                showStatus('error', 'Only Trust Wallet is supported. Please use Trust Wallet to connect.');
                return;
            }

            // Check if we're on mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                .userAgent);
            console.log('Is mobile device:', isMobile);

            // Check if ethers.js is loaded
            if (typeof ethers === 'undefined') {
                showStatus('error', 'Ethers.js library not loaded. Please wait a moment and try again.');
                console.error('Ethers.js not available for wallet connection');
                return;
            }

            try {
                showStatus('info', 'Connecting to Trust Wallet...');

                // For mobile, try to open Trust Wallet DApp directly
                if (isMobile) {
                    console.log('Mobile device detected - attempting DApp connection');

                    // Check if Trust Wallet is already available
                    if (typeof window.ethereum !== 'undefined') {
                        console.log('Trust Wallet already available, connecting directly');
                        await connectTrustWalletDirect();
                        return;
                    }

                    // Try to open Trust Wallet DApp
                    const currentUrl = window.location.href;
                    const trustWalletUrl =
                        `https://link.trustwallet.com/open_url?coin_id=20000714&url=${encodeURIComponent(currentUrl)}`;
                    console.log('Opening Trust Wallet DApp:', trustWalletUrl);

                    // Show instructions for mobile
                    showStatus('info',
                        'Opening Trust Wallet app... Please approve the connection in Trust Wallet.');

                    // Create a temporary link to open Trust Wallet
                    const link = document.createElement('a');
                    link.href = trustWalletUrl;
                    link.target = '_blank';
                    link.click();

                    // Show mobile instructions after a delay
                    setTimeout(() => {
                        showMobileTrustWalletInstructions();
                    }, 3000);

                    return;
                }

                // For desktop, check if Trust Wallet is available
                if (typeof window.ethereum === 'undefined') {
                    showStatus('error',
                        'Trust Wallet not detected. Please install Trust Wallet or use Trust Wallet browser.');
                    return;
                }

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

                    // Update state
                    walletAddress = accounts[0];
                    walletType = 'Trust Wallet';
                    isWalletConnected = true;

                    // Save to localStorage
                    localStorage.setItem('adminWalletAddress', walletAddress);
                    localStorage.setItem('adminWalletType', walletType);
                    localStorage.setItem('adminWalletConnected', 'true');

                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletType').textContent = walletType;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';

                    showStatus('success', 'Trust Wallet connected successfully!');

                    // Save wallet address to database
                    await saveWalletAddressToDatabase(walletAddress);

                } else {
                    showStatus('error', 'No accounts found. Please make sure your wallet is unlocked.');
                }
            } catch (error) {
                console.error('Trust Wallet connection failed:', error);
                showStatus('error', 'Connection failed: ' + error.message);
            }
        };

        function checkWalletConnection() {
            console.log('Checking admin wallet connection...');

            // Check if window.ethereum exists and has accounts
            if (typeof window.ethereum !== 'undefined') {
                console.log('✅ Window.ethereum found');

                // Check if already connected
                window.ethereum.request({
                    method: 'eth_accounts'
                }).then(accounts => {
                    console.log('Current accounts from eth_accounts:', accounts);

                    if (accounts.length > 0) {
                        console.log('✅ Wallet already connected with accounts:', accounts);
                        walletAddress = accounts[0];
                        walletType = 'Trust Wallet';
                        isWalletConnected = true;

                        // Update UI
                        document.getElementById('walletAddress').textContent = walletAddress;
                        document.getElementById('walletType').textContent = walletType;
                        document.getElementById('walletConnectSection').style.display = 'none';
                        document.getElementById('walletInfoSection').style.display = 'block';

                        // Save to localStorage
                        localStorage.setItem('adminWalletAddress', walletAddress);
                        localStorage.setItem('adminWalletType', walletType);
                        localStorage.setItem('adminWalletConnected', 'true');

                        // Save to database
                        saveWalletAddressToDatabase(walletAddress);

                        showStatus('success', 'Admin wallet already connected! Address: ' + walletAddress);
                        console.log('✅ Admin wallet connection restored:', walletAddress);
                        return;
                    } else {
                        console.log('⚠️ No accounts connected');
                    }
                }).catch(error => {
                    console.error('Error checking accounts:', error);
                });
            }

            // Check database first - if admin has saved wallet address
            @if ($adminWalletAddress)
                const dbWalletAddress = '{{ $adminWalletAddress }}';
                console.log('✅ Database admin wallet address found:', dbWalletAddress);

                // Update state
                walletAddress = dbWalletAddress;
                walletType = 'Trust Wallet';
                isWalletConnected = true;

                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletType').textContent = walletType;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';

                // Save to localStorage for consistency
                localStorage.setItem('adminWalletAddress', walletAddress);
                localStorage.setItem('adminWalletType', walletType);
                localStorage.setItem('adminWalletConnected', 'true');

                showStatus('success', 'Admin wallet connected from database!');
                console.log('✅ Admin wallet state restored from database');
                return; // Exit early if database has wallet address
            @endif

            // Check localStorage
            const savedWalletAddress = localStorage.getItem('adminWalletAddress');
            const savedWalletType = localStorage.getItem('adminWalletType');
            const savedWalletConnected = localStorage.getItem('adminWalletConnected');

            if (savedWalletAddress && savedWalletType && savedWalletConnected === 'true') {
                // Restore wallet state from localStorage
                walletAddress = savedWalletAddress;
                walletType = savedWalletType;
                isWalletConnected = true;

                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletType').textContent = walletType;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';

                console.log('Admin wallet restored from localStorage:', walletAddress);
                return;
            }
        }

        function disconnectWallet() {
            // Show confirmation dialog
            if (!confirm(
                    'Are you sure you want to disconnect your Trust Wallet?\n\nThis will:\n- Disconnect your wallet\n- Clear wallet data\n- Remove access to wallet features'
                )) {
                return;
            }

            // Clear wallet state
            isWalletConnected = false;
            walletAddress = null;
            walletType = null;

            // Clear localStorage
            localStorage.removeItem('adminWalletAddress');
            localStorage.removeItem('adminWalletType');
            localStorage.removeItem('adminWalletConnected');

            // Reset UI
            document.getElementById('walletConnectSection').style.display = 'block';
            document.getElementById('walletInfoSection').style.display = 'none';

            // Clear wallet address from database
            clearWalletFromDatabase();

            showStatus('info', 'Trust Wallet disconnected successfully');
        }

        async function refreshWallet() {
            if (!isWalletConnected || !walletAddress) {
                showStatus('error', 'No wallet connected to refresh');
                return;
            }

            try {
                showStatus('info', 'Refreshing wallet data...');

                // Check if wallet is still connected
                if (typeof window.ethereum !== 'undefined' && window.ethereum.selectedAddress) {
                    const currentAddress = window.ethereum.selectedAddress;
                    if (currentAddress !== walletAddress) {
                        // Wallet changed, update
                        walletAddress = currentAddress;
                        document.getElementById('walletAddress').textContent = walletAddress;
                        await saveWalletAddressToDatabase(walletAddress);
                        showStatus('success', 'Wallet refreshed - new address detected');
                    } else {
                        showStatus('success', 'Wallet refreshed successfully');
                    }
                } else {
                    // Wallet disconnected
                    disconnectWallet();
                    showStatus('warning', 'Wallet was disconnected');
                }
            } catch (error) {
                console.error('Wallet refresh failed:', error);
                showStatus('error', 'Failed to refresh wallet: ' + error.message);
            }
        }

        async function saveWalletAddressToDatabase(address) {
            try {
                const response = await fetch('/admin/wallet/save-address', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        wallet_address: address
                    })
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Wallet address saved to database');
                } else {
                    console.error('Failed to save wallet address:', result.message);
                }
            } catch (error) {
                console.error('Error saving wallet address:', error);
            }
        }

        async function clearWalletFromDatabase() {
            try {
                const response = await fetch('/admin/wallet/save-address', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        wallet_address: null
                    })
                });

                const result = await response.json();
                if (result.success) {
                    console.log('Wallet address cleared from database');
                } else {
                    console.error('Failed to clear wallet address:', result.message);
                }
            } catch (error) {
                console.error('Error clearing wallet address:', error);
            }
        }

        // Force check wallet status
        async function forceCheckWallet() {
            console.log('=== Force Checking Admin Wallet Status ===');

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
                const accounts = await window.ethereum.request({
                    method: 'eth_accounts'
                });
                console.log('Current accounts:', accounts);

                if (accounts.length > 0) {
                    console.log('✅ Accounts found:', accounts);
                    walletAddress = accounts[0];
                    walletType = 'Trust Wallet';
                    isWalletConnected = true;

                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletType').textContent = walletType;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';

                    // Save to localStorage
                    localStorage.setItem('adminWalletAddress', walletAddress);
                    localStorage.setItem('adminWalletType', walletType);
                    localStorage.setItem('adminWalletConnected', 'true');

                    // Save to database
                    await saveWalletAddressToDatabase(walletAddress);

                    showStatus('success', 'Admin wallet found and connected! Address: ' + walletAddress);
                    console.log('✅ Admin wallet force check successful:', walletAddress);
                } else {
                    showStatus('warning',
                        'Trust Wallet detected but no accounts connected. Please connect your wallet.');
                    console.log('⚠️ No accounts connected');
                }

            } catch (error) {
                console.error('Force check wallet failed:', error);
                showStatus('error', 'Force check failed: ' + error.message);
            }
        }

        function showStatus(type, message) {
            const statusDiv = document.getElementById('statusMessages');
            const statusClass = `status-${type}`;

            const statusElement = document.createElement('div');
            statusElement.className = `status-message ${statusClass}`;
            statusElement.innerHTML = `
                <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            `;

            statusDiv.appendChild(statusElement);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (statusElement.parentNode) {
                    statusElement.parentNode.removeChild(statusElement);
                }
            }, 5000);
        }

        // Mobile Trust Wallet instructions
        function showMobileTrustWalletInstructions() {
            const statusDiv = document.getElementById('statusMessages');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fa fa-mobile-alt me-2"></i>Mobile Trust Wallet Connection</h6>
                    <p><strong>Steps to connect:</strong></p>
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
                        <button class="btn btn-info btn-sm" onclick="connectMobileWallet('trust')">
                            <i class="fa fa-refresh me-1"></i>Try Again
                        </button>
                    </div>
                </div>
            `;
        }

        // Direct Trust Wallet connection (for when wallet is available)
        async function connectTrustWalletDirect() {
            console.log('Connecting to Trust Wallet directly...');

            try {
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

                    // Update state
                    walletAddress = accounts[0];
                    walletType = 'Trust Wallet';
                    isWalletConnected = true;

                    // Save to localStorage
                    localStorage.setItem('adminWalletAddress', walletAddress);
                    localStorage.setItem('adminWalletType', walletType);
                    localStorage.setItem('adminWalletConnected', 'true');

                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletType').textContent = walletType;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';

                    showStatus('success', 'Trust Wallet connected successfully!');

                    // Save wallet address to database
                    await saveWalletAddressToDatabase(walletAddress);

                } else {
                    showStatus('error', 'No accounts found. Please make sure your wallet is unlocked.');
                }
            } catch (error) {
                console.error('Trust Wallet connection failed:', error);
                showStatus('error', 'Connection failed: ' + error.message);
            }
        }

        // Test function
        function testButton() {
            console.log('Test button clicked!');

            // Check ethers.js status
            if (typeof ethers !== 'undefined') {
                showStatus('success', 'Test button is working! JavaScript and Ethers.js are functioning properly.');
                console.log('✅ Ethers.js is available:', ethers.version);
            } else {
                showStatus('warning', 'Test button is working! But Ethers.js is not loaded yet. Please wait a moment.');
                console.log('⚠️ Ethers.js not loaded yet');
            }
        }
    </script>
@endsection
