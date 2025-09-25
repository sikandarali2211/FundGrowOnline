<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessNewTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-new {--limit=10 : Number of recent transactions to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process new blockchain transactions and add them to database';

    private $bscApiUrl = 'https://api.bscscan.com/api';
    private $apiKey = 'YourBSCScanAPIKey'; // Replace with your actual API key
    
    public function __construct()
    {
        parent::__construct();
        // Try to get API key from config or environment
        $this->apiKey = config('services.bscscan.api_key', env('BSCSCAN_API_KEY', 'YourBSCScanAPIKey'));
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for new transactions...');
        
        // Check if API key is set
        if ($this->apiKey === 'YourBSCScanAPIKey') {
            $this->error('❌ BSCScan API key not configured');
            $this->line('Please set BSCSCAN_API_KEY in your .env file or config/services.php');
            return 1;
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

            // Check if result is valid
            if (!is_array($transactions)) {
                $this->error("❌ Invalid response from BSCScan API");
                $this->line("Response: " . $response->body());
                return 1;
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
                            
                            // Log for admin
                            Log::info("Auto-processed transaction", [
                                'user_id' => $user->id,
                                'user_email' => $user->email,
                                'tx_hash' => $tx['hash'],
                                'amount' => $amount,
                                'from' => $tx['from'],
                                'to' => $tx['to']
                            ]);
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
            $this->info("   • Admin wallet: {$adminWalletAddress}");

            if ($newTransactions > 0) {
                $this->info("🎉 Successfully processed {$newTransactions} new transactions!");
            } else {
                $this->info("ℹ️  No new transactions found.");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            Log::error('Transaction processing error: ' . $e->getMessage());
            return 1;
        }
    }
}