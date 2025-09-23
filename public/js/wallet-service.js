// Wallet Connection Service for BSC BEP20
class WalletService {
    constructor() {
        this.provider = null;
        this.signer = null;
        this.account = null;
        this.chainId = 56; // BSC Mainnet
        this.bscRpcUrl = 'https://bsc-dataseed.binance.org/';
        this.bscTestnetRpcUrl = 'https://data-seed-prebsc-1-s1.binance.org:8545/';
    }

    async connectWallet() {
        try {
            // Check for mobile Trust Wallet first
            if (this.isMobile() && typeof window.ethereum !== 'undefined') {
                return await this.connectMobileWallet();
            }
            
            if (typeof window.ethereum !== 'undefined') {
                // Desktop MetaMask or other Web3 wallet
                this.provider = new ethers.BrowserProvider(window.ethereum);
                await this.provider.send("eth_requestAccounts", []);
                this.signer = await this.provider.getSigner();
                this.account = await this.signer.getAddress();
                
                // Check if on BSC network
                await this.switchToBSC();
                return { success: true, account: this.account };
            } else {
                // Show user-friendly message with installation link
                const installMessage = `
                    <div style="text-align: center; padding: 20px;">
                        <h4>Web3 Wallet Required</h4>
                        <p>To use this DApp, you need to install a Web3 wallet like MetaMask or Trust Wallet.</p>
                        <div style="margin: 20px 0;">
                            <a href="https://metamask.io/download/" target="_blank" 
                               style="background: #f6851b; color: white; padding: 10px 20px; 
                                      text-decoration: none; border-radius: 5px; margin: 0 10px;">
                                Install MetaMask
                            </a>
                            <a href="https://trustwallet.com/" target="_blank" 
                               style="background: #3375bb; color: white; padding: 10px 20px; 
                                      text-decoration: none; border-radius: 5px; margin: 0 10px;">
                                Install Trust Wallet
                            </a>
                        </div>
                        <p style="font-size: 12px; color: #666;">
                            After installation, refresh this page and try again.
                        </p>
                    </div>
                `;
                
                // Show modal instead of alert
                this.showWalletInstallModal(installMessage);
                return { success: false, error: 'No Web3 wallet detected' };
            }
        } catch (error) {
            console.error('Wallet connection failed:', error);
            return { success: false, error: error.message };
        }
    }

    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    async connectMobileWallet() {
        try {
            console.log('Attempting mobile wallet connection...');
            
            // Check if Trust Wallet is available
            if (window.ethereum && window.ethereum.isTrust) {
                console.log('Trust Wallet detected');
                return await this.connectTrustWallet();
            }
            
            // Check if MetaMask Mobile is available
            if (window.ethereum && window.ethereum.isMetaMask) {
                console.log('MetaMask Mobile detected');
                return await this.connectMetaMaskMobile();
            }
            
            // Generic mobile wallet connection
            console.log('Generic mobile wallet connection');
            this.provider = new ethers.BrowserProvider(window.ethereum);
            await this.provider.send("eth_requestAccounts", []);
            this.signer = await this.provider.getSigner();
            this.account = await this.signer.getAddress();
            
            // Switch to BSC network
            await this.switchToBSC();
            return { success: true, account: this.account, walletType: 'mobile' };
            
        } catch (error) {
            console.error('Mobile wallet connection failed:', error);
            return { success: false, error: error.message };
        }
    }

    async connectTrustWallet() {
        try {
            console.log('Connecting to Trust Wallet...');
            
            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            
            if (accounts.length === 0) {
                throw new Error('No accounts found');
            }
            
            this.provider = new ethers.BrowserProvider(window.ethereum);
            this.signer = await this.provider.getSigner();
            this.account = await this.signer.getAddress();
            
            // Switch to BSC network
            await this.switchToBSC();
            
            console.log('Trust Wallet connected successfully:', this.account);
            return { success: true, account: this.account, walletType: 'trust' };
            
        } catch (error) {
            console.error('Trust Wallet connection failed:', error);
            return { success: false, error: error.message };
        }
    }

    async connectMetaMaskMobile() {
        try {
            console.log('Connecting to MetaMask Mobile...');
            
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });
            
            if (accounts.length === 0) {
                throw new Error('No accounts found');
            }
            
            this.provider = new ethers.BrowserProvider(window.ethereum);
            this.signer = await this.provider.getSigner();
            this.account = await this.signer.getAddress();
            
            // Switch to BSC network
            await this.switchToBSC();
            
            console.log('MetaMask Mobile connected successfully:', this.account);
            return { success: true, account: this.account, walletType: 'metamask' };
            
        } catch (error) {
            console.error('MetaMask Mobile connection failed:', error);
            return { success: false, error: error.message };
        }
    }

    showWalletInstallModal(message) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('walletInstallModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'walletInstallModal';
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Wallet Required</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="walletInstallContent">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        document.getElementById('walletInstallContent').innerHTML = message;
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }

    async switchToBSC() {
        try {
            console.log('Checking current network...');
            const network = await this.provider.getNetwork();
            console.log('Current network:', network);
            
            if (Number(network.chainId) !== this.chainId) {
                console.log('Switching to BSC network...');
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: '0x38' }], // BSC Mainnet
                });
                console.log('Successfully switched to BSC network');
            } else {
                console.log('Already on BSC network');
            }
        } catch (switchError) {
            console.error('Network switch error:', switchError);
            // If BSC network is not added, add it
            if (switchError.code === 4902) {
                console.log('Adding BSC network...');
                await this.addBSCNetwork();
            } else {
                throw switchError;
            }
        }
    }

    async addBSCNetwork() {
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
                rpcUrls: [this.bscRpcUrl],
                blockExplorerUrls: ['https://bscscan.com/'],
            }],
        });
    }

    async getBalance(tokenAddress = null) {
        try {
            if (!this.signer) throw new Error('Wallet not connected');
            
            if (tokenAddress) {
                // BEP20 Token balance
                const contract = new ethers.Contract(tokenAddress, BEP20_ABI, this.signer);
                const balance = await contract.balanceOf(this.account);
                const decimals = await contract.decimals();
                return ethers.formatUnits(balance, decimals);
            } else {
                // BNB balance
                const balance = await this.provider.getBalance(this.account);
                return ethers.formatEther(balance);
            }
        } catch (error) {
            console.error('Error getting balance:', error);
            return '0';
        }
    }

    async sendToken(toAddress, amount, tokenAddress = null) {
        try {
            if (!this.signer) throw new Error('Wallet not connected');
            
            if (tokenAddress) {
                // Send BEP20 Token
                const contract = new ethers.Contract(tokenAddress, BEP20_ABI, this.signer);
                const decimals = await contract.decimals();
                const amountWei = ethers.parseUnits(amount, decimals);
                
                const tx = await contract.transfer(toAddress, amountWei);
                return { success: true, txHash: tx.hash, tx };
            } else {
                // Send BNB
                const amountWei = ethers.parseEther(amount);
                const tx = await this.signer.sendTransaction({
                    to: toAddress,
                    value: amountWei
                });
                return { success: true, txHash: tx.hash, tx };
            }
        } catch (error) {
            console.error('Transaction failed:', error);
            return { success: false, error: error.message };
        }
    }

    async waitForTransaction(txHash) {
        try {
            const receipt = await this.provider.waitForTransaction(txHash);
            return {
                success: receipt.status === 1,
                receipt: receipt,
                blockNumber: receipt.blockNumber,
                gasUsed: receipt.gasUsed.toString()
            };
        } catch (error) {
            console.error('Error waiting for transaction:', error);
            return { success: false, error: error.message };
        }
    }

    disconnect() {
        this.provider = null;
        this.signer = null;
        this.account = null;
    }

    // Enhanced wallet detection for mobile
    detectWalletType() {
        if (typeof window.ethereum === 'undefined') {
            return 'none';
        }

        // Check for Trust Wallet
        if (window.ethereum.isTrust) {
            return 'trust';
        }

        // Check for MetaMask
        if (window.ethereum.isMetaMask) {
            return 'metamask';
        }

        // Check for other wallets
        if (window.ethereum.isCoinbaseWallet) {
            return 'coinbase';
        }

        // Generic Web3 provider
        return 'generic';
    }

    // Show specific mobile wallet instructions
    showMobileWalletInstructions(walletType) {
        let instructions = '';
        
        switch (walletType) {
            case 'trust':
                instructions = `
                    <div style="text-align: center; padding: 20px;">
                        <h4>Connect Trust Wallet</h4>
                        <p>To connect your Trust Wallet on mobile:</p>
                        <ol style="text-align: left; max-width: 300px; margin: 0 auto;">
                            <li>Make sure Trust Wallet app is installed and unlocked</li>
                            <li>Open this website in Trust Wallet's built-in browser</li>
                            <li>Or use the "Connect Wallet" button below</li>
                        </ol>
                        <div style="margin: 20px 0;">
                            <button class="btn btn-primary" onclick="window.walletService.connectWallet()">
                                Connect Trust Wallet
                            </button>
                        </div>
                        <p style="font-size: 12px; color: #666;">
                            If you don't have Trust Wallet, 
                            <a href="https://trustwallet.com/" target="_blank">download it here</a>
                        </p>
                    </div>
                `;
                break;
            case 'metamask':
                instructions = `
                    <div style="text-align: center; padding: 20px;">
                        <h4>Connect MetaMask Mobile</h4>
                        <p>To connect your MetaMask on mobile:</p>
                        <ol style="text-align: left; max-width: 300px; margin: 0 auto;">
                            <li>Make sure MetaMask app is installed and unlocked</li>
                            <li>Open this website in MetaMask's built-in browser</li>
                            <li>Or use the "Connect Wallet" button below</li>
                        </ol>
                        <div style="margin: 20px 0;">
                            <button class="btn btn-primary" onclick="window.walletService.connectWallet()">
                                Connect MetaMask
                            </button>
                        </div>
                        <p style="font-size: 12px; color: #666;">
                            If you don't have MetaMask, 
                            <a href="https://metamask.io/download/" target="_blank">download it here</a>
                        </p>
                    </div>
                `;
                break;
            default:
                instructions = `
                    <div style="text-align: center; padding: 20px;">
                        <h4>Mobile Wallet Connection</h4>
                        <p>To connect your wallet on mobile:</p>
                        <ol style="text-align: left; max-width: 300px; margin: 0 auto;">
                            <li>Make sure your wallet app is installed and unlocked</li>
                            <li>Open this website in your wallet's built-in browser</li>
                            <li>Or use the "Connect Wallet" button below</li>
                        </ol>
                        <div style="margin: 20px 0;">
                            <button class="btn btn-primary" onclick="window.walletService.connectWallet()">
                                Connect Wallet
                            </button>
                        </div>
                    </div>
                `;
        }

        this.showWalletInstallModal(instructions);
    }
}

// BEP20 Token ABI (minimal)
const BEP20_ABI = [
    "function balanceOf(address owner) view returns (uint256)",
    "function transfer(address to, uint256 amount) returns (bool)",
    "function decimals() view returns (uint8)",
    "function symbol() view returns (string)",
    "function name() view returns (string)",
    "event Transfer(address indexed from, address indexed to, uint256 value)"
];

// Initialize wallet service
window.walletService = new WalletService();

// Add global helper functions for mobile wallet connection
window.connectTrustWallet = async function() {
    console.log('Global Trust Wallet connection called');
    try {
        const result = await window.walletService.connectWallet();
        if (result.success) {
            console.log('Trust Wallet connected:', result.account);
            // Trigger a custom event for the UI to update
            window.dispatchEvent(new CustomEvent('walletConnected', {
                detail: { account: result.account, walletType: 'trust' }
            }));
        } else {
            console.error('Trust Wallet connection failed:', result.error);
            window.dispatchEvent(new CustomEvent('walletConnectionFailed', {
                detail: { error: result.error }
            }));
        }
    } catch (error) {
        console.error('Trust Wallet connection error:', error);
    }
};

window.connectMobileWallet = async function() {
    console.log('Global mobile wallet connection called');
    try {
        const result = await window.walletService.connectWallet();
        if (result.success) {
            console.log('Mobile wallet connected:', result.account);
            window.dispatchEvent(new CustomEvent('walletConnected', {
                detail: { account: result.account, walletType: result.walletType || 'mobile' }
            }));
        } else {
            console.error('Mobile wallet connection failed:', result.error);
            window.dispatchEvent(new CustomEvent('walletConnectionFailed', {
                detail: { error: result.error }
            }));
        }
    } catch (error) {
        console.error('Mobile wallet connection error:', error);
    }
};

// Add debugging information on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Wallet Service Debug Info:');
    console.log('- User Agent:', navigator.userAgent);
    console.log('- Is Mobile:', window.walletService.isMobile());
    console.log('- Wallet Type:', window.walletService.detectWalletType());
    console.log('- Ethereum Available:', typeof window.ethereum !== 'undefined');
    
    if (window.ethereum) {
        console.log('- Ethereum Provider:', window.ethereum);
        console.log('- Is Trust Wallet:', window.ethereum.isTrust);
        console.log('- Is MetaMask:', window.ethereum.isMetaMask);
    }
});
