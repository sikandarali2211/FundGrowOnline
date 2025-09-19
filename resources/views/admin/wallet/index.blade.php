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
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #3bd17a;
        }

        /* Card style */
        .card {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        /* Wallet Connect Button */
        .wallet-connect-btn {
            background: linear-gradient(45deg, #3375bb, #4a90e2);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(51, 117, 188, 0.3);
        }

        .wallet-connect-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(51, 117, 188, 0.4);
            color: white;
        }

        .wallet-connect-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        /* Wallet Info */
        .wallet-info {
            background: rgba(59, 209, 122, 0.1);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .wallet-address {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 8px;
            word-break: break-all;
            color: #3bd17a;
        }

        /* Balance Display */
        .balance-display {
            text-align: center;
            padding: 20px;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: bold;
            color: #3bd17a;
            margin: 10px 0;
        }

        .balance-label {
            color: #a5f2d5;
            font-size: 1.1rem;
        }

        /* Transaction Form */
        .transaction-form {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(59, 209, 122, 0.3);
            color: #fff;
            border-radius: 8px;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: #3bd17a;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.25);
        }

        .form-control::placeholder {
            color: #a5f2d5;
        }

        /* Status Messages */
        .status-message {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
        }

        .status-success {
            background: rgba(59, 209, 122, 0.2);
            border: 1px solid #3bd17a;
            color: #3bd17a;
        }

        .status-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .status-info {
            background: rgba(13, 202, 240, 0.2);
            border: 1px solid #0dcaf0;
            color: #0dcaf0;
        }

        /* Transaction History */
        .transaction-item {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #3bd17a;
        }

        .transaction-hash {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #3bd17a;
            word-break: break-all;
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .balance-amount {
                font-size: 2rem;
            }
            
            .wallet-connect-btn {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>

    <!-- MAIN PANEL -->
    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="page-header">
                <h3 class="page-title" style="color: #3bd17a;">
                    <i class="fa fa-wallet me-2"></i>Admin Wallet Connect
                </h3>
                <p class="mb-0">Connect your Trust Wallet to manage transactions and view balances</p>
            </div>

            <!-- Wallet Connection Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="card-title text-white mb-4">
                                <i class="fa fa-wallet me-2"></i>Trust Wallet Connection
                            </h4>
                            
                            @if($adminWalletAddress)
                                <div class="alert alert-success mb-4">
                                    <i class="fa fa-check-circle me-2"></i>
                                    <strong>Current Admin Wallet Address:</strong> {{ $adminWalletAddress }}
                                </div>
                            @endif

                            <!-- Wallet Connect Options -->
                            <div id="walletConnectSection">
                                <h6 class="text-white mb-3">Choose Wallet Type:</h6>
                                
                                <!-- Trust Wallet Option -->
                                <div class="wallet-option mb-3">
                                    <button id="connectTrustWalletBtn" class="wallet-connect-btn w-100">
                                        <i class="fa fa-wallet me-2"></i>Connect Trust Wallet
                                    </button>
                                    <small class="text-muted">Mobile & Desktop Trust Wallet</small>
                                </div>
                                
                                <!-- Web Extension Option -->
                                <div class="wallet-option mb-3">
                                    <button id="connectExtensionBtn" class="wallet-connect-btn w-100" style="background: linear-gradient(45deg, #f6851b, #ff9500);">
                                        <i class="fa fa-puzzle-piece me-2"></i>Connect Web Extension
                                    </button>
                                    <small class="text-muted">MetaMask, WalletConnect, or other Web3 extensions</small>
                                </div>
                                
                                <!-- Manual Address Option -->
                                <div class="wallet-option">
                                    <button id="manualAddressBtn" class="wallet-connect-btn w-100" style="background: linear-gradient(45deg, #6c757d, #495057);">
                                        <i class="fa fa-key me-2"></i>Enter Address Manually
                                    </button>
                                    <small class="text-muted">Enter wallet address directly</small>
                                </div>
                            </div>

                            <!-- Manual Address Form (Hidden by default) -->
                            <div id="manualAddressSection" class="wallet-info" style="display: none;">
                                <h5 class="text-info mb-3">
                                    <i class="fa fa-key me-2"></i>Enter Wallet Address
                                </h5>
                                <form id="manualAddressForm">
                                    <div class="form-group mb-3">
                                        <label for="walletAddressInput" class="form-label">Wallet Address</label>
                                        <input type="text" class="form-control" id="walletAddressInput" 
                                               placeholder="0x..." required>
                                        <small class="form-text text-muted">Enter your BSC wallet address</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check me-1"></i>Connect
                                        </button>
                                        <button type="button" id="cancelManualBtn" class="btn btn-secondary">
                                            <i class="fa fa-times me-1"></i>Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Wallet Info (Hidden by default) -->
                            <div id="walletInfoSection" class="wallet-info" style="display: none;">
                                <h5 class="text-success mb-3">
                                    <i class="fa fa-check-circle me-2"></i>Wallet Connected
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Wallet Address:</strong>
                                        <div id="walletAddress" class="wallet-address mt-2"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Wallet Type:</strong>
                                        <div class="mt-2">
                                            <span id="walletType" class="badge badge-primary">Unknown</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Network:</strong>
                                        <div class="mt-2">
                                            <span class="badge badge-success">Binance Smart Chain</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button id="disconnectWalletBtn" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-times me-1"></i>Disconnect
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance and Transaction Section (Hidden by default) -->
            <div id="walletFeaturesSection" style="display: none;">
                <!-- Balance Display -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body balance-display">
                                <h5 class="balance-label">BNB Balance</h5>
                                <div id="bnbBalance" class="balance-amount">0.0000</div>
                                <button id="refreshBalanceBtn" class="btn btn-outline-success btn-sm mt-2">
                                    <i class="fa fa-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body balance-display">
                                <h5 class="balance-label">USDT Balance</h5>
                                <div id="usdtBalance" class="balance-amount">0.0000</div>
                                <button id="refreshUsdtBalanceBtn" class="btn btn-outline-success btn-sm mt-2">
                                    <i class="fa fa-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-white mb-4">
                                    <i class="fa fa-paper-plane me-2"></i>Send Transaction
                                </h5>
                                
                                <form id="transactionForm" class="transaction-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="recipientAddress" class="form-label">Recipient Address</label>
                                                <input type="text" class="form-control" id="recipientAddress" 
                                                       placeholder="0x..." required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="amount" class="form-label">Amount</label>
                                                <input type="number" class="form-control" id="amount" 
                                                       placeholder="0.0" step="0.0001" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="tokenType" class="form-label">Token Type</label>
                                                <select class="form-control" id="tokenType">
                                                    <option value="bnb">BNB (Native)</option>
                                                    <option value="usdt">USDT (BEP-20)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fa fa-paper-plane me-2"></i>Send Transaction
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-white mb-4">
                                    <i class="fa fa-history me-2"></i>Recent Transactions
                                </h5>
                                <div id="transactionHistory">
                                    <div class="text-center text-muted py-4">
                                        <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                                        <p>Loading transaction history...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Messages -->
            <div id="statusMessages"></div>
        </div>
    </div>

    <script>
        // Wallet connection state
        let isWalletConnected = false;
        let walletAddress = null;
        let walletType = null;
        let walletService = null;

        // Wallet state persistence
        function saveWalletState(address, type) {
            localStorage.setItem('adminWalletAddress', address);
            localStorage.setItem('adminWalletType', type);
            localStorage.setItem('adminWalletConnected', 'true');
        }

        function loadWalletState() {
            const isConnected = localStorage.getItem('adminWalletConnected') === 'true';
            const address = localStorage.getItem('adminWalletAddress');
            const type = localStorage.getItem('adminWalletType');
            
            if (isConnected && address) {
                walletAddress = address;
                walletType = type;
                isWalletConnected = true;
                
                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletType').textContent = walletType;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
                document.getElementById('walletFeaturesSection').style.display = 'block';
                
                // Load balances and transaction history
                refreshBalances();
                loadTransactionHistory();
                
                return true;
            }
            return false;
        }

        function clearWalletState() {
            localStorage.removeItem('adminWalletAddress');
            localStorage.removeItem('adminWalletType');
            localStorage.removeItem('adminWalletConnected');
        }

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
            document.getElementById('connectTrustWalletBtn').addEventListener('click', () => connectWallet('trust'));
            document.getElementById('connectExtensionBtn').addEventListener('click', () => connectWallet('extension'));
            document.getElementById('manualAddressBtn').addEventListener('click', showManualAddressForm);
            document.getElementById('manualAddressForm').addEventListener('submit', connectManualAddress);
            document.getElementById('cancelManualBtn').addEventListener('click', hideManualAddressForm);
            document.getElementById('disconnectWalletBtn').addEventListener('click', disconnectWallet);
            document.getElementById('refreshBalanceBtn').addEventListener('click', refreshBalances);
            document.getElementById('refreshUsdtBalanceBtn').addEventListener('click', refreshUsdtBalance);
            document.getElementById('transactionForm').addEventListener('submit', sendTransaction);

            // Check if wallet is already connected
            if (!loadWalletState()) {
                checkWalletConnection();
            }
        });

        async function connectWallet(type) {
            const connectBtn = type === 'trust' ? 
                document.getElementById('connectTrustWalletBtn') : 
                document.getElementById('connectExtensionBtn');
            const originalText = connectBtn.innerHTML;
            
            try {
                connectBtn.disabled = true;
                connectBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
                
                let result;
                if (type === 'trust') {
                    result = await walletService.connectWallet();
                    walletType = 'Trust Wallet';
                } else if (type === 'extension') {
                    result = await connectWebExtension();
                    walletType = 'Web Extension';
                }
                
                if (result.success) {
                    walletAddress = result.account;
                    isWalletConnected = true;
                    
                    // Save wallet state
                    saveWalletState(walletAddress, walletType);
                    
                    // Update UI
                    document.getElementById('walletAddress').textContent = walletAddress;
                    document.getElementById('walletType').textContent = walletType;
                    document.getElementById('walletConnectSection').style.display = 'none';
                    document.getElementById('walletInfoSection').style.display = 'block';
                    document.getElementById('walletFeaturesSection').style.display = 'block';
                    
                    showStatus('success', `${walletType} connected successfully!`);
                    
                    // Load balances and transaction history
                    await refreshBalances();
                    await loadTransactionHistory();
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

        async function connectWebExtension() {
            try {
                if (typeof window.ethereum !== 'undefined') {
                    // Request account access
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    
                    if (accounts.length > 0) {
                        // Check if on BSC network
                        const chainId = await window.ethereum.request({ method: 'eth_chainId' });
                        if (chainId !== '0x38') { // BSC mainnet
                            await switchToBSC();
                        }
                        
                        return { success: true, account: accounts[0] };
                    } else {
                        return { success: false, error: 'No accounts found' };
                    }
                } else {
                    return { success: false, error: 'No Web3 extension found' };
                }
            } catch (error) {
                return { success: false, error: error.message };
            }
        }

        async function switchToBSC() {
            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: '0x38' }],
                });
            } catch (switchError) {
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
        }

        function showManualAddressForm() {
            document.getElementById('walletConnectSection').style.display = 'none';
            document.getElementById('manualAddressSection').style.display = 'block';
        }

        function hideManualAddressForm() {
            document.getElementById('manualAddressSection').style.display = 'none';
            document.getElementById('walletConnectSection').style.display = 'block';
        }

        function connectManualAddress(event) {
            event.preventDefault();
            
            const address = document.getElementById('walletAddressInput').value.trim();
            
            if (!address) {
                showStatus('error', 'Please enter a wallet address');
                return;
            }
            
            if (!address.startsWith('0x') || address.length !== 42) {
                showStatus('error', 'Please enter a valid BSC wallet address');
                return;
            }
            
            // Connect with manual address
            walletAddress = address;
            walletType = 'Manual Address';
            isWalletConnected = true;
            
            // Save wallet state
            saveWalletState(walletAddress, walletType);
            
            // Update UI
            document.getElementById('walletAddress').textContent = walletAddress;
            document.getElementById('walletType').textContent = walletType;
            document.getElementById('manualAddressSection').style.display = 'none';
            document.getElementById('walletInfoSection').style.display = 'block';
            document.getElementById('walletFeaturesSection').style.display = 'block';
            
            showStatus('success', 'Manual address connected successfully!');
            
            // Load balances and transaction history
            refreshBalances();
            loadTransactionHistory();
        }

        function disconnectWallet() {
            if (walletService) {
                walletService.disconnect();
            }
            
            isWalletConnected = false;
            walletAddress = null;
            walletType = null;
            
            // Clear wallet state
            clearWalletState();
            
            // Reset UI
            document.getElementById('walletConnectSection').style.display = 'block';
            document.getElementById('manualAddressSection').style.display = 'none';
            document.getElementById('walletInfoSection').style.display = 'none';
            document.getElementById('walletFeaturesSection').style.display = 'none';
            
            // Clear form
            document.getElementById('walletAddressInput').value = '';
            
            showStatus('info', 'Wallet disconnected');
        }

        async function refreshBalances() {
            if (!isWalletConnected || !walletAddress) return;
            
            try {
                // Get BNB balance
                const bnbBalance = await walletService.getBalance();
                document.getElementById('bnbBalance').textContent = parseFloat(bnbBalance).toFixed(4);
                
                // Get USDT balance (USDT contract address on BSC)
                const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
                const usdtBalance = await walletService.getBalance(usdtContractAddress);
                document.getElementById('usdtBalance').textContent = parseFloat(usdtBalance).toFixed(2);
                
            } catch (error) {
                showStatus('error', 'Failed to refresh balances: ' + error.message);
            }
        }

        async function refreshUsdtBalance() {
            if (!isWalletConnected || !walletAddress) return;
            
            try {
                const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
                const usdtBalance = await walletService.getBalance(usdtContractAddress);
                document.getElementById('usdtBalance').textContent = parseFloat(usdtBalance).toFixed(2);
                showStatus('success', 'USDT balance refreshed');
            } catch (error) {
                showStatus('error', 'Failed to refresh USDT balance: ' + error.message);
            }
        }

        async function sendTransaction(event) {
            event.preventDefault();
            
            if (!isWalletConnected) {
                showStatus('error', 'Please connect your wallet first');
                return;
            }
            
            const recipientAddress = document.getElementById('recipientAddress').value;
            const amount = document.getElementById('amount').value;
            const tokenType = document.getElementById('tokenType').value;
            
            if (!recipientAddress || !amount) {
                showStatus('error', 'Please fill in all required fields');
                return;
            }
            
            try {
                let result;
                
                if (tokenType === 'bnb') {
                    result = await walletService.sendToken(recipientAddress, amount);
                } else if (tokenType === 'usdt') {
                    const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
                    result = await walletService.sendToken(recipientAddress, amount, usdtContractAddress);
                }
                
                if (result.success) {
                    showStatus('success', `Transaction sent! Hash: ${result.txHash}`);
                    
                    // Wait for transaction confirmation
                    showStatus('info', 'Waiting for transaction confirmation...');
                    const confirmation = await walletService.waitForTransaction(result.txHash);
                    
                    if (confirmation.success) {
                        showStatus('success', 'Transaction confirmed!');
                        await refreshBalances();
                        await loadTransactionHistory();
                    } else {
                        showStatus('error', 'Transaction failed');
                    }
                } else {
                    showStatus('error', result.error || 'Transaction failed');
                }
            } catch (error) {
                showStatus('error', 'Transaction failed: ' + error.message);
            }
        }

        async function loadTransactionHistory() {
            // This would typically fetch from a blockchain explorer API
            // For now, we'll show a placeholder
            const historyDiv = document.getElementById('transactionHistory');
            historyDiv.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fa fa-info-circle fa-2x mb-3"></i>
                    <p>Transaction history will be loaded here</p>
                    <small>This feature requires integration with BSC explorer API</small>
                </div>
            `;
        }

        function checkWalletConnection() {
            // Check if wallet is already connected
            if (typeof window.ethereum !== 'undefined' && window.ethereum.selectedAddress) {
                walletAddress = window.ethereum.selectedAddress;
                walletType = 'Web Extension';
                isWalletConnected = true;
                
                // Update UI
                document.getElementById('walletAddress').textContent = walletAddress;
                document.getElementById('walletType').textContent = walletType;
                document.getElementById('walletConnectSection').style.display = 'none';
                document.getElementById('walletInfoSection').style.display = 'block';
                document.getElementById('walletFeaturesSection').style.display = 'block';
                
                // Load balances
                refreshBalances();
                
                // Save wallet address to database
                saveWalletAddressToDatabase(walletAddress);
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
    </script>
@endsection
