<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProcessTransactionsSimple extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-simple {--user-id=15 : User ID to process transactions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process transactions manually without BSCScan API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        $this->info('🔍 Processing transactions for user ' . $userId);
        
        try {
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return 1;
            }

            $this->info("👤 User: {$user->name} ({$user->email})");
            $this->info("🔗 Wallet: {$user->wallet_address}");
            $this->line("");

            // Get admin wallet
            $admin = User::where('utype', 'ADM')
                ->whereNotNull('wallet_address')
                ->first();
            
            if (!$admin) {
                $this->error('❌ Admin wallet not found');
                return 1;
            }

            $this->info("📊 Admin wallet: {$admin->wallet_address}");
            $this->line("");

            // Get current transactions
            $currentTransactions = Transaction::where('user_id', $userId)->count();
            $this->info("📈 Current transactions: {$currentTransactions}");

            // Ask if user wants to add a new transaction
            if ($this->confirm('Do you want to add a new transaction for this user?')) {
                $amount = $this->ask('Enter amount (e.g., 1.5 for 1.5 USDT)', '1.0');
                $txHash = $this->ask('Enter transaction hash (or press enter for auto-generated)', '');
                
                if (empty($txHash)) {
                    $txHash = '0x' . str_repeat('a', 64);
                }

                // Create transaction
                $transaction = Transaction::create([
                    'user_id' => $userId,
                    'tx_hash' => $txHash,
                    'from_address' => $user->wallet_address,
                    'to_address' => $admin->wallet_address,
                    'amount' => (float)$amount,
                    'token_address' => '0x55d398326f99059fF775485246999027B3197955', // USDT BEP20
                    'token_symbol' => 'USDT',
                    'status' => 'confirmed',
                    'block_number' => rand(1000000, 9999999),
                    'confirmed_at' => now()
                ]);

                $this->info("✅ Transaction created successfully!");
                $this->line("   • Hash: {$transaction->tx_hash}");
                $this->line("   • Amount: {$transaction->amount} {$transaction->token_symbol}");
                $this->line("   • Status: {$transaction->status}");
                $this->line("");

                // Show updated balance
                $totalAmount = Transaction::where('user_id', $userId)->sum('amount');
                $this->info("💰 Updated balance: $" . number_format($totalAmount, 2));
            }

            // Show all transactions
            $transactions = Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($transactions->count() > 0) {
                $this->line("");
                $this->info("📋 All Transactions:");
                foreach ($transactions as $tx) {
                    $this->line("   • {$tx->created_at->format('Y-m-d H:i:s')} - $" . number_format($tx->amount, 2) . " {$tx->token_symbol} - {$tx->status}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            Log::error('Simple transaction processing error: ' . $e->getMessage());
            return 1;
        }
    }
}