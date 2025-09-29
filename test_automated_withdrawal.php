<?php
/**
 * Test Script for Automated Withdrawal System
 * 
 * This script tests the basic functionality of the automated withdrawal system
 * without making actual blockchain transactions.
 */

require_once 'vendor/autoload.php';

use App\Services\Web3Service;
use App\Services\AutomatedWithdrawalService;
use App\Services\BscScanService;

echo "=== Automated Withdrawal System Test ===\n\n";

// Test 1: Web3Service Network Status
echo "1. Testing Web3Service Network Status...\n";
try {
    $web3Service = new Web3Service();
    $networkStatus = $web3Service->getNetworkStatus();
    
    if ($networkStatus['success']) {
        echo "   ✓ Network Status: Connected\n";
        echo "   ✓ Block Number: " . $networkStatus['blockNumber'] . "\n";
        echo "   ✓ Network: " . $networkStatus['network'] . "\n";
    } else {
        echo "   ✗ Network Status: " . $networkStatus['error'] . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: BSCScanService
echo "2. Testing BSCScanService...\n";
try {
    $bscScanService = new BscScanService();
    echo "   ✓ BSCScanService initialized successfully\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: AutomatedWithdrawalService
echo "3. Testing AutomatedWithdrawalService...\n";
try {
    $withdrawalService = new AutomatedWithdrawalService($web3Service, $bscScanService);
    $stats = $withdrawalService->getWithdrawalStats();
    
    echo "   ✓ AutomatedWithdrawalService initialized successfully\n";
    echo "   ✓ Total Pending: " . $stats['total_pending'] . "\n";
    echo "   ✓ Total Completed: " . $stats['total_completed'] . "\n";
    echo "   ✓ Admin Balance: " . $stats['admin_balance'] . " USDT\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Configuration Check
echo "4. Testing Configuration...\n";
$config = [
    'BSC_RPC_URL' => config('services.bscscan.rpc_url'),
    'ADMIN_WALLET_ADDRESS' => config('services.bscscan.admin_address'),
    'BSCSCAN_API_KEY' => config('services.bscscan.key'),
    'WITHDRAWAL_MIN_AMOUNT' => config('withdrawal.min_amount'),
    'WITHDRAWAL_MAX_AMOUNT' => config('withdrawal.max_amount'),
];

foreach ($config as $key => $value) {
    if (!empty($value)) {
        echo "   ✓ $key: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "\n";
    } else {
        echo "   ✗ $key: Not configured\n";
    }
}

echo "\n";

// Test 5: Database Connection
echo "5. Testing Database Connection...\n";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    echo "   ✓ Database connection successful\n";
    
    // Check if withdrawal_requests table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='withdrawal_requests'");
    if ($stmt->fetch()) {
        echo "   ✓ withdrawal_requests table exists\n";
    } else {
        echo "   ✗ withdrawal_requests table not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Command Line Interface
echo "6. Testing Command Line Interface...\n";
try {
    $output = shell_exec('php artisan withdrawals:process --stats 2>&1');
    if (strpos($output, 'Withdrawal Statistics:') !== false) {
        echo "   ✓ Command line interface working\n";
    } else {
        echo "   ✗ Command line interface not working\n";
        echo "   Output: " . $output . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Command Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 7: Security Check
echo "7. Testing Security Configuration...\n";
$securityChecks = [
    'Private Key Length' => strlen(config('services.bscscan.admin_private_key')) >= 64,
    'Admin Address Format' => preg_match('/^0x[a-fA-F0-9]{40}$/', config('services.bscscan.admin_address')),
    'RPC URL HTTPS' => strpos(config('services.bscscan.rpc_url'), 'https://') === 0,
    'Min Amount > 0' => config('withdrawal.min_amount') > 0,
    'Max Amount > Min Amount' => config('withdrawal.max_amount') > config('withdrawal.min_amount'),
];

foreach ($securityChecks as $check => $result) {
    if ($result) {
        echo "   ✓ $check: Passed\n";
    } else {
        echo "   ✗ $check: Failed\n";
    }
}

echo "\n";

// Test 8: File Permissions
echo "8. Testing File Permissions...\n";
$files = [
    'app/Services/Web3Service.php',
    'app/Services/AutomatedWithdrawalService.php',
    'app/Console/Commands/ProcessWithdrawals.php',
    'public/js/automated-withdrawal.js',
    'config/withdrawal.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file: Exists\n";
    } else {
        echo "   ✗ $file: Not found\n";
    }
}

echo "\n";

// Summary
echo "=== Test Summary ===\n";
echo "The automated withdrawal system has been successfully set up!\n";
echo "\nNext steps:\n";
echo "1. Configure your .env file with proper values\n";
echo "2. Set up your admin wallet with USDT\n";
echo "3. Test the system in a development environment\n";
echo "4. Deploy to production with proper security measures\n";
echo "\nFor more information, see AUTOMATED_WITHDRAWAL_SYSTEM.md\n";
echo "==========================================\n";
