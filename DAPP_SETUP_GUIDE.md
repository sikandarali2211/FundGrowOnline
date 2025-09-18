# DApp Integration Guide - BSC BEP20 Wallet

## 🚀 Quick Setup

### 1. Install Dependencies
```bash
npm install ethers@6.0.0 @walletconnect/web3-provider@1.8.0 @walletconnect/modal@2.7.0
```

### 2. Run Database Migration
```bash
php artisan migrate
```

### 3. Get BSCScan API Key
1. Go to [BSCScan.com](https://bscscan.com/)
2. Create an account and get your API key
3. Update `app/Http/Controllers/TransactionController.php`:
   ```php
   private $apiKey = 'YOUR_BSCSCAN_API_KEY_HERE';
   ```

## 🔧 Features Implemented

### ✅ Wallet Connection
- MetaMask integration
- Automatic BSC network switching
- Wallet status display
- Account information

### ✅ BSC BEP20 Transactions
- Send BNB (native token)
- Send BEP20 tokens
- Real-time balance checking
- Transaction confirmation

### ✅ Transaction Verification
- BSCScan API integration
- Transaction status checking
- Block confirmation tracking
- Gas usage monitoring

### ✅ Database Storage
- Transaction history storage
- User-specific transaction tracking
- Status management (pending/confirmed/failed)

## 📱 How to Use

### 1. Access Wallet
- Login to your user dashboard
- Click "Wallet" in the sidebar
- Connect your MetaMask wallet

### 2. Send BNB
- Enter recipient address
- Enter amount in BNB
- Click "Send BNB"
- Confirm in MetaMask

### 3. Send BEP20 Tokens
- Enter token contract address
- Enter recipient address
- Enter amount
- Click "Send Token"
- Confirm in MetaMask

### 4. View Transaction History
- All transactions are automatically stored
- View status and details
- Click transaction hash to view on BSCScan

## 🔍 Transaction Verification Process

### Step 1: Transaction Submission
1. User initiates transaction via MetaMask
2. Transaction hash is returned
3. Transaction stored in database as "pending"

### Step 2: BSCScan Verification
1. System queries BSCScan API
2. Verifies transaction details match
3. Checks transaction status on blockchain

### Step 3: Confirmation Tracking
1. Monitors block confirmations
2. Updates status to "confirmed" when mined
3. Stores block number and gas used

### Step 4: Error Handling
1. Failed transactions marked as "failed"
2. Invalid transactions rejected
3. Network errors handled gracefully

## 🛡️ Security Features

### Frontend Security
- Input validation for all forms
- Address format validation
- Amount validation (positive numbers)
- CSRF protection on all forms

### Backend Security
- API key protection
- User authentication required
- Transaction ownership verification
- Rate limiting on API calls

### Blockchain Security
- BSCScan API verification
- Transaction hash validation
- Address checksum validation
- Gas limit protection

## 🔧 Configuration

### BSC Network Settings
```javascript
// In wallet-service.js
chainId: 56, // BSC Mainnet
bscRpcUrl: 'https://bsc-dataseed.binance.org/',
```

### Testnet Configuration
```javascript
// For BSC Testnet
chainId: 97,
bscRpcUrl: 'https://data-seed-prebsc-1-s1.binance.org:8545/',
```

## 📊 API Endpoints

### Transaction Verification
```
POST /User-dashboard/wallet/verify-transaction
{
    "txHash": "0x...",
    "fromAddress": "0x...",
    "toAddress": "0x...",
    "amount": 1.5,
    "tokenAddress": "0x..." // optional for BEP20
}
```

### Transaction Status
```
GET /User-dashboard/wallet/transaction-status/{txHash}
```

### Balance Check
```
GET /User-dashboard/wallet/balance/{address}
```

### Transaction History
```
GET /User-dashboard/wallet/transactions
```

## 🚨 Important Notes

### 1. API Key Security
- Never commit API keys to version control
- Use environment variables in production
- Rotate keys regularly

### 2. Gas Fees
- BSC has low gas fees (~$0.01-0.10)
- Users pay gas fees in BNB
- Gas estimation included

### 3. Network Reliability
- BSCScan API has rate limits
- Implement retry logic for production
- Consider backup RPC providers

### 4. User Experience
- Always show transaction status
- Provide clear error messages
- Include loading states

## 🔄 Testing

### Test with BSC Testnet
1. Switch to BSC Testnet in MetaMask
2. Get test BNB from [BSC Testnet Faucet](https://testnet.binance.org/faucet-smart)
3. Test all functionality

### Test with Mainnet
1. Use small amounts initially
2. Verify all transactions on BSCScan
3. Test with different token contracts

## 📈 Production Checklist

- [ ] BSCScan API key configured
- [ ] Database migrations run
- [ ] SSL certificate installed
- [ ] Error logging configured
- [ ] Rate limiting implemented
- [ ] Backup RPC providers added
- [ ] Monitoring alerts set up

## 🆘 Troubleshooting

### Common Issues

1. **Wallet not connecting**
   - Check if MetaMask is installed
   - Ensure BSC network is added
   - Clear browser cache

2. **Transaction failing**
   - Check BNB balance for gas
   - Verify recipient address
   - Check token contract address

3. **API errors**
   - Verify BSCScan API key
   - Check rate limits
   - Monitor server logs

### Support
- Check BSCScan documentation
- Review MetaMask troubleshooting
- Check Laravel logs in `storage/logs/`
