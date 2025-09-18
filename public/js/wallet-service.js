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
            if (typeof window.ethereum !== 'undefined') {
                // MetaMask or other Web3 wallet
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
                        <p>To use this DApp, you need to install a Web3 wallet like MetaMask.</p>
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
            const network = await this.provider.getNetwork();
            if (Number(network.chainId) !== this.chainId) {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: '0x38' }], // BSC Mainnet
                });
            }
        } catch (switchError) {
            // If BSC network is not added, add it
            if (switchError.code === 4902) {
                await this.addBSCNetwork();
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
