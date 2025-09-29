# Automated Withdrawal System Framework

## Overview

This automated withdrawal system provides a complete framework for processing USDT (BEP-20) withdrawals from admin wallet to user wallets using Web3 PHP and Laravel. The system includes security considerations, real-time monitoring, and automated processing capabilities.

## Features

### 🔄 Automated Processing
- **Real-time Processing**: Automatically processes withdrawal requests as they come in
- **Scheduled Tasks**: Runs every 5 minutes to process pending withdrawals
- **Transaction Monitoring**: Monitors blockchain confirmations every 2 minutes
- **Retry Mechanism**: Automatic retry with configurable attempts and delays

### 🔒 Security Features
- **Private Key Management**: Secure handling of admin private keys
- **Transaction Validation**: Comprehensive validation before processing
- **Rate Limiting**: Configurable rate limits per user and system
- **2FA Support**: Optional two-factor authentication for sensitive operations
- **Audit Logging**: Complete audit trail of all operations

### 📊 Monitoring & Analytics
- **Real-time Statistics**: Live dashboard with withdrawal statistics
- **Network Status**: BSC network connectivity monitoring
- **Admin Balance Tracking**: Real-time admin wallet balance monitoring
- **Transaction History**: Complete transaction history and status tracking

### 🛠️ Technical Features
- **Web3 Integration**: Direct BSC network integration using Web3 PHP
- **Gas Optimization**: Dynamic gas price calculation and optimization
- **Error Handling**: Comprehensive error handling and recovery
- **Queue Management**: Background processing with queue management
- **API Endpoints**: RESTful API for frontend integration

## Installation & Setup

### 1. Environment Configuration

Add the following environment variables to your `.env` file:

```env
# BSC Network Configuration
BSC_RPC_URL=https://bsc-dataseed.binance.org/
BSCSCAN_API_KEY=your_bscscan_api_key
BSCSCAN_CHAIN_ID=56

# Admin Wallet Configuration
ADMIN_WALLET_ADDRESS=0x...
ADMIN_PRIVATE_KEY=your_private_key_here

# Withdrawal Limits
WITHDRAWAL_MIN_AMOUNT=1.0
WITHDRAWAL_MAX_AMOUNT=10000.0
WITHDRAWAL_DAILY_LIMIT=50000.0

# Security Settings
WITHDRAWAL_REQUIRE_2FA=true
WITHDRAWAL_MAX_RETRIES=3
WITHDRAWAL_RETRY_DELAY=30
```

### 2. Database Migration

The system uses existing tables. Ensure your database is up to date:

```bash
php artisan migrate
```

### 3. Service Registration

The services are automatically registered. No additional configuration needed.

### 4. Schedule Setup

Add the following to your `crontab` for automated processing:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Usage

### 1. Manual Processing

Process withdrawals manually through the admin dashboard:

```php
// Process all pending withdrawals
POST /admin/withdrawals/process-all

// Process specific withdrawal
POST /admin/withdrawals/{id}/approve
{
    "auto_transfer": true,
    "admin_notes": "Processed automatically"
}
```

### 2. Command Line Processing

```bash
# Process all pending withdrawals
php artisan withdrawals:process

# Monitor transaction confirmations
php artisan withdrawals:process --monitor

# Show statistics
php artisan withdrawals:process --stats

# Force process in maintenance mode
php artisan withdrawals:process --force
```

### 3. API Endpoints

#### Get Withdrawal Statistics
```http
GET /admin/withdrawals/statistics
```

Response:
```json
{
    "success": true,
    "data": {
        "total_pending": 5,
        "total_approved": 10,
        "total_completed": 100,
        "total_rejected": 2,
        "total_amount_pending": "150.50",
        "total_amount_completed": "5000.00",
        "admin_balance": "10000.000000",
        "network_status": {
            "success": true,
            "network": "BSC Mainnet"
        }
    }
}
```

#### Process All Withdrawals
```http
POST /admin/withdrawals/process-all
```

Response:
```json
{
    "success": true,
    "message": "Withdrawal processing completed",
    "data": {
        "processed": 5,
        "successful": 4,
        "failed": 1,
        "errors": []
    }
}
```

## Architecture

### Core Components

1. **Web3Service**: Handles blockchain interactions
2. **AutomatedWithdrawalService**: Manages withdrawal processing logic
3. **ProcessWithdrawals Command**: Console command for processing
4. **AdminWithdrawalController**: API endpoints for admin operations
5. **AutomatedWithdrawalSystem**: Frontend JavaScript integration

### Data Flow

```
User Request → WithdrawalRequest → Validation → Web3Service → BSC Network → Confirmation → Update Status
```

### Security Flow

```
Request → Authentication → Authorization → Validation → Rate Limiting → Processing → Audit Log
```

## Configuration

### Withdrawal Limits

```php
// config/withdrawal.php
'min_amount' => 1.0,           // Minimum withdrawal amount
'max_amount' => 10000.0,       // Maximum withdrawal amount
'daily_limit' => 50000.0,      // Daily withdrawal limit
'rate_limit_per_hour' => 10,   // Rate limit per hour
'rate_limit_per_day' => 50,    // Rate limit per day
```

### Security Settings

```php
'require_2fa' => true,         // Require 2FA for withdrawals
'max_retries' => 3,            // Maximum retry attempts
'retry_delay' => 30,           // Delay between retries (seconds)
'timeout_hours' => 24,         // Transaction timeout (hours)
```

### Blockchain Settings

```php
'gas_limit' => 100000,         // Gas limit for transactions
'gas_price' => '5000000000',   // Gas price in wei (5 Gwei)
'confirmation_blocks' => 3,    // Required confirmation blocks
```

## Monitoring & Maintenance

### 1. Log Monitoring

Monitor the Laravel logs for withdrawal processing:

```bash
tail -f storage/logs/laravel.log | grep -i withdrawal
```

### 2. Database Monitoring

Check withdrawal statistics:

```sql
SELECT 
    status,
    COUNT(*) as count,
    SUM(amount) as total_amount
FROM withdrawal_requests 
GROUP BY status;
```

### 3. System Health

Check system health:

```bash
php artisan withdrawals:process --stats
```

### 4. Maintenance Mode

Enable maintenance mode to pause processing:

```bash
php artisan down
```

## Error Handling

### Common Errors

1. **Insufficient Balance**: Admin wallet doesn't have enough USDT
2. **Network Issues**: BSC network connectivity problems
3. **Invalid Address**: Malformed wallet addresses
4. **Gas Issues**: Insufficient gas or gas price problems
5. **Rate Limiting**: Too many requests from same user

### Error Recovery

The system includes automatic retry mechanisms and error recovery:

- **Retry Logic**: Automatic retry with exponential backoff
- **Fallback Processing**: Manual processing when automation fails
- **Error Notifications**: Real-time error notifications
- **Audit Trail**: Complete error logging and tracking

## Security Considerations

### 1. Private Key Security

- Store private keys in environment variables
- Use hardware wallets for production
- Implement key rotation policies
- Monitor key usage and access

### 2. Network Security

- Use HTTPS for all API endpoints
- Implement rate limiting
- Monitor for suspicious activity
- Regular security audits

### 3. Data Protection

- Encrypt sensitive data
- Implement access controls
- Regular backup procedures
- GDPR compliance considerations

## Performance Optimization

### 1. Database Optimization

- Index frequently queried columns
- Regular database maintenance
- Query optimization
- Connection pooling

### 2. Caching

- Cache frequently accessed data
- Use Redis for session storage
- Implement query result caching
- Cache blockchain data

### 3. Queue Management

- Use background queues for processing
- Implement queue monitoring
- Set up queue failure handling
- Optimize queue workers

## Troubleshooting

### Common Issues

1. **Web3 Connection Failed**
   - Check BSC RPC URL
   - Verify network connectivity
   - Check API key validity

2. **Transaction Failed**
   - Check gas settings
   - Verify wallet balance
   - Check transaction parameters

3. **Processing Stuck**
   - Check queue status
   - Restart queue workers
   - Clear failed jobs

### Debug Commands

```bash
# Check system status
php artisan withdrawals:process --stats

# Test Web3 connection
php artisan tinker
>>> app(App\Services\Web3Service::class)->getNetworkStatus()

# Check queue status
php artisan queue:work --verbose
```

## Support & Maintenance

### Regular Maintenance

1. **Daily**: Monitor system logs and statistics
2. **Weekly**: Review failed transactions and retry
3. **Monthly**: Update dependencies and security patches
4. **Quarterly**: Security audit and performance review

### Monitoring Alerts

Set up alerts for:
- High failure rates
- Low admin wallet balance
- Network connectivity issues
- Unusual withdrawal patterns

## License

This automated withdrawal system is part of the FundGrowOnline project and follows the same licensing terms.

## Contributing

When contributing to this system:

1. Follow Laravel coding standards
2. Add comprehensive tests
3. Update documentation
4. Follow security best practices
5. Test thoroughly before deployment

---

**Note**: This system handles real cryptocurrency transactions. Always test thoroughly in a development environment before deploying to production. Ensure proper security measures are in place and consider consulting with blockchain security experts for production deployment.
