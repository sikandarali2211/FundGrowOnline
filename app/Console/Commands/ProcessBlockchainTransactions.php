<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessBlockchainTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:process {--limit=20 : Number of recent transactions to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process blockchain transactions and save to database';

    private $bscApiUrl = 'https://api.bscscan.com/api';
    private $apiKey = 'YourBSCScanAPIKey'; // Replace with your actual API key

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = config('services.bscscan.api_key', env('BSCSCAN_API_KEY', 'YourBSCScanAPIKey'));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Processing blockchain transactions...');
        
        // Check if API key is set
        if ($this->apiKey === 'YourBSCScanAPIKey') {
            $this->error('❌ BSCScan API key not configured');
            $this->line('Please set BSCSCAN_API_KEY in your .env file');
            $this->line('For now, we will process manually...');
            return $this->processManually();
        }
        
        try {
            // Get admin wallet address
            $admin = User::where('utype', 'ADM')
                ->whereNotNull('wallet_address')
                ->first();
            
            if (!$admin) {
                $this->error('❌ Admin wallet not found');
                return 1;
            }

            $adminWalletAddress = $admin->wallet_address;
            $this->info("📊 Admin wallet: {$adminWalletAddress}");

            // Get recent transactions from BSCScan API
            $response = Http::get($this->bscApiUrl, [
                'module' => 'account',
                'action' => 'txlist',
                'address' => $adminWalletAddress,
                'startblock' => 0,
                'endblock' => 99999999,
                'page' => 1,
                'offset' => $this->option('limit'),
                'sort' => 'desc',
                'apikey' => $this->apiKey
            ]);

            if (!$response->successful()) {
                $this->error('❌ Failed to fetch transactions from BSCScan');
                return 1;
            }

            $data = $response->json();
            $transactions = $data['result'] ?? [];

            if (!is_array($transactions)) {
                $this->error("❌ Invalid response from BSCScan API");
                $this->line("Response: " . $response->body());
                $this->line("Switching to manual mode...");
                return $this->processManually();
            }

            $this->info("📈 Found " . count($transactions) . " transactions from BSCScan");

            $newTransactions = 0;
            $processedTransactions = 0;

            foreach ($transactions as $tx) {
                $processedTransactions++;
                
                // Check if transaction is USDT (BEP20) to admin wallet
                if ($tx['input'] === '0xa9059cbb' && $tx['to'] === $adminWalletAddress) {
                    // Check if transaction already exists in our database
                    $existingTx = Transaction::where('tx_hash', $tx['hash'])->first();
                    
                    if (!$existingTx) {
                        // This is a new USDT transaction to admin wallet
                        $this->info("🆕 New transaction found: {$tx['hash']}");
                        
                        // Find user by from address
                        $user = User::where('wallet_address', $tx['from'])->first();
                        
                        if ($user) {
                            // Calculate amount (convert from wei to decimal)
                            $amount = hexdec($tx['value']) / pow(10, 18);
                            
                            // Create transaction record
                            Transaction::create([
                                'user_id' => $user->id,
                                'tx_hash' => $tx['hash'],
                                'from_address' => $tx['from'],
                                'to_address' => $tx['to'],
                                'amount' => $amount,
                                'token_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20
                                'token_symbol' => 'USDT',
                                'status' => 'confirmed',
                                'block_number' => hexdec($tx['blockNumber']),
                                'confirmed_at' => now()
                            ]);
                            
                            $newTransactions++;
                            $this->info("✅ Added transaction for user {$user->id} ({$user->name}): {$amount} USDT");
                        } else {
                            $this->warn("⚠️  User not found for wallet: {$tx['from']}");
                        }
                    } else {
                        $this->line("⏭️  Transaction already exists: {$tx['hash']}");
                    }
                }
            }

            $this->info("📊 Summary:");
            $this->info("   • Checked: {$processedTransactions} transactions");
            $this->info("   • New: {$newTransactions} transactions added");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process transactions manually when API is not available
     */
    private function processManually()
    {
        $this->info('🔧 Manual processing mode...');
        
        // Get admin wallet
        $admin = User::where('utype', 'ADM')->first();
        if (!$admin) {
            $this->error('❌ Admin wallet not found');
            return 1;
        }

        $this->info("📊 Admin wallet: {$admin->wallet_address}");
        $this->line('');

        // Show current database status
        $currentTransactions = Transaction::count();
        $this->info("📈 Current database transactions: {$currentTransactions}");

        // Ask if user wants to add transactions manually
        if ($this->confirm('Do you want to add transactions manually?')) {
            $this->line('');
            $this->info('Available users:');
            
            $users = User::whereNotNull('wallet_address')->get();
            foreach ($users as $user) {
                $this->line("   • ID: {$user->id} | Name: {$user->name} | Wallet: {$user->wallet_address}");
            }
            
            $this->line('');
            
            while (true) {
                $userId = $this->ask('Enter user ID (or press enter to finish)');
                if (empty($userId)) break;
                
                $amount = $this->ask('Enter amount (e.g., 1.5 for 1.5 USDT)');
                if (empty($amount)) continue;
                
                $txHash = $this->ask('Enter transaction hash (or press enter for auto-generated)');
                if (empty($txHash)) {
                    $txHash = '0x' . str_repeat('a', 64) . time();
                }
                
                try {
                    $user = User::find($userId);
                    if (!$user) {
                        $this->error("❌ User not found");
                        continue;
                    }
                    
                    Transaction::create([
                        'user_id' => $userId,
                        'tx_hash' => $txHash,
                        'from_address' => $user->wallet_address,
                        'to_address' => $admin->wallet_address,
                        'amount' => (float)$amount,
                        'token_address' => '0x55d398326f99059fF775485246999027B3197955',
                        'token_symbol' => 'USDT',
                        'status' => 'confirmed',
                        'block_number' => rand(1000000, 9999999),
                        'confirmed_at' => now()
                    ]);
                    
                    $this->info("✅ Added transaction for user {$user->name}: $" . number_format($amount, 2));
                    
                } catch (\Exception $e) {
                    $this->error("❌ Error: " . $e->getMessage());
                }
                
                $this->line('');
            }
        }

        return 0;
    }
}