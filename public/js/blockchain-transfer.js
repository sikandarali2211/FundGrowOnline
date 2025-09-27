/**
 * Blockchain Transfer Service
 * Handles real USDT transfers from admin wallet to user wallets
 */

class BlockchainTransferService {
    constructor() {
        this.web3 = null;
        this.usdtContract = null;
        this.usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
        this.usdtAbi = [
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
                "inputs": [{"name": "_owner", "type": "address"}],
                "name": "balanceOf",
                "outputs": [{"name": "balance", "type": "uint256"}],
                "type": "function"
            }
        ];
    }

    /**
     * Initialize Web3 connection
     */
    async initialize() {
        try {
            if (typeof window.ethereum !== 'undefined') {
                this.web3 = new Web3(window.ethereum);
                await window.ethereum.enable();
                
                // Initialize USDT contract
                this.usdtContract = new this.web3.eth.Contract(
                    this.usdtAbi,
                    this.usdtContractAddress
                );
                
                console.log('Blockchain Transfer Service initialized');
                return true;
            } else {
                console.error('MetaMask or Web3 provider not found');
                return false;
            }
        } catch (error) {
            console.error('Failed to initialize Web3:', error);
            return false;
        }
    }

    /**
     * Transfer USDT from admin wallet to user wallet
     */
    async transferUSDT(toAddress, amount, adminPrivateKey) {
        try {
            if (!this.web3 || !this.usdtContract) {
                throw new Error('Web3 not initialized');
            }

            // Get admin account from private key
            const adminAccount = this.web3.eth.accounts.privateKeyToAccount(adminPrivateKey);
            this.web3.eth.accounts.wallet.add(adminAccount);

            // Convert amount to wei (USDT has 18 decimals)
            const amountWei = this.web3.utils.toWei(amount.toString(), 'ether');

            // Check admin balance
            const balance = await this.usdtContract.methods.balanceOf(adminAccount.address).call();
            const balanceEth = this.web3.utils.fromWei(balance, 'ether');

            if (parseFloat(balanceEth) < parseFloat(amount)) {
                throw new Error(`Insufficient balance. Available: ${balanceEth} USDT, Required: ${amount} USDT`);
            }

            // Prepare transaction
            const transferData = this.usdtContract.methods.transfer(toAddress, amountWei).encodeABI();

            const transaction = {
                from: adminAccount.address,
                to: this.usdtContractAddress,
                data: transferData,
                gas: 100000, // Gas limit for USDT transfer
                gasPrice: this.web3.utils.toWei('5', 'gwei') // Gas price in gwei
            };

            // Estimate gas
            const gasEstimate = await this.web3.eth.estimateGas(transaction);
            transaction.gas = gasEstimate;

            // Sign and send transaction
            const signedTx = await this.web3.eth.accounts.signTransaction(transaction, adminPrivateKey);
            const receipt = await this.web3.eth.sendSignedTransaction(signedTx.rawTransaction);

            console.log('USDT transfer successful:', {
                transactionHash: receipt.transactionHash,
                toAddress: toAddress,
                amount: amount,
                gasUsed: receipt.gasUsed
            });

            return {
                success: true,
                transactionHash: receipt.transactionHash,
                gasUsed: receipt.gasUsed
            };

        } catch (error) {
            console.error('USDT transfer failed:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Get USDT balance of an address
     */
    async getUSDTBalance(address) {
        try {
            if (!this.usdtContract) {
                throw new Error('USDT contract not initialized');
            }

            const balance = await this.usdtContract.methods.balanceOf(address).call();
            return this.web3.utils.fromWei(balance, 'ether');
        } catch (error) {
            console.error('Failed to get USDT balance:', error);
            return '0';
        }
    }

    /**
     * Check if transaction is confirmed
     */
    async isTransactionConfirmed(txHash) {
        try {
            const receipt = await this.web3.eth.getTransactionReceipt(txHash);
            return receipt && receipt.status === true;
        } catch (error) {
            console.error('Failed to check transaction status:', error);
            return false;
        }
    }
}

// Global instance
window.blockchainTransferService = new BlockchainTransferService();

// Auto-initialize when page loads
document.addEventListener('DOMContentLoaded', async () => {
    if (typeof Web3 !== 'undefined') {
        await window.blockchainTransferService.initialize();
    } else {
        console.warn('Web3.js not loaded. Please include Web3.js library.');
    }
});
