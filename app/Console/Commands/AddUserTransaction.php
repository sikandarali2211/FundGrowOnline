<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;

class AddUserTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add-transaction {user_id} {amount} {--hash= : Transaction hash (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new transaction for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = $this->argument('amount');
        $txHash = $this->option('hash');
        
        $this->info('➕ Adding transaction for User ID: ' . $userId);
        $this->info('💰 Amount: $' . $amount);
        $this->line('');
        
        try {
            // Get user details
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return 1;
            }

            // Get admin wallet
            $admin = User::where('utype', 'ADM')->first();
            if (!$admin) {
                $this->error("❌ Admin wallet not found");
                return 1;
            }

            // Generate transaction hash if not provided
            if (!$txHash) {
                $txHash = '0x' . str_repeat('a', 64) . time(); // Add timestamp to make it unique
                $this->warn("⚠️  No transaction hash provided, using: {$txHash}");
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

            $this->info("✅ Transaction added successfully!");
            $this->line("   • User: {$user->name} ({$user->email})");
            $this->line("   • Amount: $" . number_format($amount, 2) . " USDT");
            $this->line("   • Hash: {$transaction->tx_hash}");
            $this->line("   • Status: {$transaction->status}");
            $this->line("   • Date: {$transaction->created_at->format('Y-m-d H:i:s')}");
            $this->line("");

            // Show updated balance
            $totalAmount = Transaction::where('user_id', $userId)->sum('amount');
            $this->info("💰 Updated Balance: $" . number_format($totalAmount, 2));

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}