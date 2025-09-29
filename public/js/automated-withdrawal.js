/**
 * Automated Withdrawal System - Frontend JavaScript
 * Handles real-time withdrawal processing and monitoring
 */

class AutomatedWithdrawalSystem {
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
            },
            {
                "constant": true,
                "inputs": [],
                "name": "decimals",
                "outputs": [{"name": "", "type": "uint8"}],
                "type": "function"
            }
        ];
        this.isProcessing = false;
        this.processingQueue = [];
    }

    /**
     * Initialize the automated withdrawal system
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
                
                console.log('Automated Withdrawal System initialized');
                this.startMonitoring();
                return true;
            } else {
                console.error('MetaMask or Web3 provider not found');
                return false;
            }
        } catch (error) {
            console.error('Failed to initialize Automated Withdrawal System:', error);
            return false;
        }
    }

    /**
     * Start monitoring for withdrawal requests
     */
    startMonitoring() {
        // Check for pending withdrawals every 30 seconds
        setInterval(() => {
            this.checkPendingWithdrawals();
        }, 30000);

        // Monitor transaction confirmations every 60 seconds
        setInterval(() => {
            this.monitorConfirmations();
        }, 60000);

        console.log('Withdrawal monitoring started');
    }

    /**
     * Check for pending withdrawal requests
     */
    async checkPendingWithdrawals() {
        try {
            const response = await fetch('/admin/withdrawals/statistics', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateStatistics(data.data);
                }
            }
        } catch (error) {
            console.error('Failed to check pending withdrawals:', error);
        }
    }

    /**
     * Update statistics display
     */
    updateStatistics(stats) {
        // Update statistics in the admin dashboard
        const statsElements = {
            'pending-count': stats.total_pending,
            'approved-count': stats.total_approved,
            'completed-count': stats.total_completed,
            'rejected-count': stats.total_rejected,
            'pending-amount': '$' + parseFloat(stats.total_amount_pending).toFixed(2),
            'completed-amount': '$' + parseFloat(stats.total_amount_completed).toFixed(2),
            'admin-balance': parseFloat(stats.admin_balance).toFixed(6) + ' USDT'
        };

        Object.entries(statsElements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });

        // Update network status
        const networkStatus = document.getElementById('network-status');
        if (networkStatus) {
            networkStatus.textContent = stats.network_status.success ? 'Connected' : 'Disconnected';
            networkStatus.className = stats.network_status.success ? 'badge badge-success' : 'badge badge-danger';
        }
    }

    /**
     * Process a single withdrawal request
     */
    async processWithdrawal(withdrawalId, autoTransfer = false) {
        if (this.isProcessing) {
            console.warn('Withdrawal processing is already in progress');
            return;
        }

        this.isProcessing = true;

        try {
            const response = await fetch(`/admin/withdrawals/${withdrawalId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    auto_transfer: autoTransfer,
                    admin_notes: 'Processed automatically'
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('success', data.message);
                this.refreshWithdrawalList();
            } else {
                this.showNotification('error', data.message);
            }

        } catch (error) {
            console.error('Failed to process withdrawal:', error);
            this.showNotification('error', 'Failed to process withdrawal');
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Process all pending withdrawals
     */
    async processAllWithdrawals() {
        if (this.isProcessing) {
            console.warn('Withdrawal processing is already in progress');
            return;
        }

        this.isProcessing = true;

        try {
            const response = await fetch('/admin/withdrawals/process-all', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('success', data.message);
                this.updateStatistics(data.data);
                this.refreshWithdrawalList();
            } else {
                this.showNotification('error', data.message);
            }

        } catch (error) {
            console.error('Failed to process all withdrawals:', error);
            this.showNotification('error', 'Failed to process withdrawals');
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Monitor transaction confirmations
     */
    async monitorConfirmations() {
        try {
            // This would typically call a backend endpoint to check confirmations
            // For now, we'll just log that monitoring is active
            console.log('Monitoring transaction confirmations...');
        } catch (error) {
            console.error('Failed to monitor confirmations:', error);
        }
    }

    /**
     * Transfer USDT from admin wallet to user wallet
     */
    async transferUSDT(toAddress, amount) {
        try {
            if (!this.web3 || !this.usdtContract) {
                throw new Error('Web3 not initialized');
            }

            // Get admin account (this would need to be securely handled)
            const accounts = await this.web3.eth.getAccounts();
            const adminAccount = accounts[0];

            // Convert amount to wei (USDT has 18 decimals)
            const amountWei = this.web3.utils.toWei(amount.toString(), 'ether');

            // Check admin balance
            const balance = await this.usdtContract.methods.balanceOf(adminAccount).call();
            const balanceEth = this.web3.utils.fromWei(balance, 'ether');

            if (parseFloat(balanceEth) < parseFloat(amount)) {
                throw new Error(`Insufficient balance. Available: ${balanceEth} USDT, Required: ${amount} USDT`);
            }

            // Prepare transaction
            const transferData = this.usdtContract.methods.transfer(toAddress, amountWei).encodeABI();

            const transaction = {
                from: adminAccount,
                to: this.usdtContractAddress,
                data: transferData,
                gas: 100000,
                gasPrice: this.web3.utils.toWei('5', 'gwei')
            };

            // Estimate gas
            const gasEstimate = await this.web3.eth.estimateGas(transaction);
            transaction.gas = gasEstimate;

            // Send transaction
            const receipt = await this.web3.eth.sendTransaction(transaction);

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
     * Refresh withdrawal list
     */
    refreshWithdrawalList() {
        // Reload the page or update the withdrawal list via AJAX
        window.location.reload();
    }

    /**
     * Show notification
     */
    showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        // Add to page
        const container = document.querySelector('.container-fluid') || document.body;
        container.insertBefore(notification, container.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    /**
     * Enable/disable automated processing
     */
    toggleAutomatedProcessing(enabled) {
        const button = document.getElementById('toggle-automation');
        if (button) {
            button.textContent = enabled ? 'Disable Automation' : 'Enable Automation';
            button.className = enabled ? 'btn btn-danger' : 'btn btn-success';
        }

        // Store preference in localStorage
        localStorage.setItem('automatedWithdrawal', enabled);
    }

    /**
     * Check if automated processing is enabled
     */
    isAutomatedProcessingEnabled() {
        return localStorage.getItem('automatedWithdrawal') === 'true';
    }
}

// Global instance
window.automatedWithdrawalSystem = new AutomatedWithdrawalSystem();

// Auto-initialize when page loads
document.addEventListener('DOMContentLoaded', async () => {
    if (typeof Web3 !== 'undefined') {
        await window.automatedWithdrawalSystem.initialize();
    } else {
        console.warn('Web3.js not loaded. Please include Web3.js library.');
    }
});
