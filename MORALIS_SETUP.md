# Moralis Web3 Integration Setup Guide

This guide will help you set up Moralis Web3 integration in your FundGrowOnline Laravel application.

## Prerequisites

1. **Moralis Account**: Sign up at [moralis.io](https://moralis.io)
2. **Node.js**: Ensure Node.js is installed on your system
3. **Laravel Environment**: Your Laravel application should be properly configured

## Step 1: Install Dependencies

First, install the Moralis SDK:

```bash
npm install moralis
```

If you encounter PowerShell execution policy issues, you can:

1. Open PowerShell as Administrator
2. Run: `Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser`
3. Then run: `npm install moralis`

## Step 2: Environment Configuration

Add the following environment variables to your `.env` file:

```env
# Moralis Configuration
MORALIS_API_KEY=your_moralis_api_key_here
MORALIS_SERVER_URL=your_moralis_server_url_here
MORALIS_BASE_URL=https://deep-index.moralis.io/api/v2

# Web3Auth Configuration (Optional)
WEB3AUTH_CLIENT_ID=your_web3auth_client_id_here
```

### Getting Your Moralis Credentials:

1. **API Key**:
   - Go to [Moralis Admin Panel](https://admin.moralis.io)
   - Create a new project or select existing one
   - Go to "Settings" → "API Keys"
   - Copy your API key

2. **Server URL** (Optional for server-side operations):
   - In your Moralis project settings
   - Go to "Settings" → "Server Details"
   - Copy your server URL

## Step 3: Build Frontend Assets

Build the frontend assets to include Moralis:

```bash
npm run build
```

Or for development:

```bash
npm run dev
```

## Step 4: Database Migration (Optional)

If you want to store wallet addresses in your users table, add a migration:

```bash
php artisan make:migration add_wallet_address_to_users_table
```

Then add this to the migration:

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('wallet_address')->nullable()->after('email');
    });
}
```

Run the migration:

```bash
php artisan migrate
```

## Step 5: Test the Integration

1. Visit `/web3-demo` to see the Web3 integration in action
2. Connect your wallet using the provided interface
3. Test various Web3 features like balance checking, NFT viewing, etc.

## Features Included

### Frontend Features:
- **Wallet Connection**: Connect/disconnect Web3 wallets
- **Balance Checking**: View native token balances
- **NFT Support**: View NFT collections
- **Transaction History**: View recent transactions
- **Message Signing**: Sign messages with wallet
- **API Integration**: Test Laravel backend integration

### Backend Features:
- **Web3Service**: Laravel service for Moralis API calls
- **Web3Controller**: API endpoints for Web3 operations
- **Wallet Authentication**: Verify wallet signatures
- **User Data Management**: Store and retrieve user Web3 data

## API Endpoints

The following API endpoints are available:

- `GET /api/web3/wallet-balance` - Get wallet balance
- `GET /api/web3/nft-balance` - Get NFT balance
- `GET /api/web3/token-balance` - Get token balance
- `GET /api/web3/transaction-history` - Get transaction history
- `POST /api/web3/verify-signature` - Verify wallet signature
- `GET /api/web3/user-data` - Get user's Web3 data

## Usage in Your Views

To include the Web3 wallet component in any view:

```blade
@include('components.web3-wallet')
```

## Customization

### Adding New Web3 Features:

1. **Frontend**: Add new methods to `resources/js/web3.js`
2. **Backend**: Add new methods to `app/Services/Web3Service.php`
3. **API**: Add new routes in `routes/web.php`

### Styling:

The Web3 components use Bootstrap classes. You can customize the styling by modifying the CSS in the component files.

## Troubleshooting

### Common Issues:

1. **Moralis not loading**: Check if the API key is correctly set in your environment
2. **Wallet connection fails**: Ensure you have a Web3 wallet (MetaMask, etc.) installed
3. **API errors**: Check Laravel logs for detailed error messages
4. **Build errors**: Make sure all dependencies are installed with `npm install`

### Debug Mode:

Enable debug mode by adding to your `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Security Considerations

1. **API Keys**: Never commit your Moralis API keys to version control
2. **Signature Verification**: Always verify wallet signatures on the backend
3. **Rate Limiting**: Consider implementing rate limiting for API endpoints
4. **Input Validation**: Validate all user inputs before processing

## Support

For issues related to:
- **Moralis**: Check [Moralis Documentation](https://docs.moralis.io)
- **Laravel**: Check [Laravel Documentation](https://laravel.com/docs)
- **This Integration**: Check the code comments and this guide

## Next Steps

1. Customize the Web3 features according to your investment platform needs
2. Integrate Web3 payments for investment plans
3. Add more blockchain networks support
4. Implement smart contract interactions
5. Add more advanced Web3 features like DeFi protocols

Happy coding! 🚀
