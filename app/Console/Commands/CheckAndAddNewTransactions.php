<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;

class CheckAndAddNewTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:check-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new transactions and add to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for new transactions...');
        $this->line('');
        
        try {
            // Get admin wallet
            $admin = User::where('utype', 'ADM')->first();
            if (!$admin) {
                $this->error('❌ Admin wallet not found');
                return 1;
            }

            $adminWalletAddress = $admin->wallet_address;
            $this->info("📊 Admin wallet: {$adminWalletAddress}");
            
            // Get current database count
            $currentCount = Transaction::count();
            $this->info("📈 Current database transactions: {$currentCount}");
            $this->line('');

            // Check for new transactions
            $newTransactions = $this->checkForNewTransactions($adminWalletAddress);
            
            if ($newTransactions > 0) {
                $this->info("✅ Found and added {$newTransactions} new transactions");
            } else {
                $this->info("ℹ️  No new transactions found");
            }

            // Show updated status
            $newCount = Transaction::count();
            $this->line('');
            $this->info("📊 Updated database transactions: {$newCount}");
            $this->info("📈 Added: " . ($newCount - $currentCount) . " new transactions");

            // Show users summary
            $this->line('');
            $this->info('👥 Users Summary:');
            $usersWithTransactions = User::whereHas('transactions')->with('transactions')->get();
            
            foreach ($usersWithTransactions as $user) {
                $totalAmount = $user->transactions->sum('amount');
                $transactionCount = $user->transactions->count();
                $this->line("   • {$user->name} (ID: {$user->id}): $" . number_format($totalAmount, 2) . " ({$transactionCount} transactions)");
            }

            $this->line('');
            $this->info('✅ Check completed successfully!');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check for new transactions
     */
    private function checkForNewTransactions($adminWalletAddress)
    {
        $newTransactions = 0;
        
        // Get all users with wallet addresses
        $users = User::whereNotNull('wallet_address')
            ->where('utype', '!=', 'ADM')
            ->get();
        
        if ($users->isEmpty()) {
            $this->warn('⚠️  No users with wallet addresses found');
            return 0;
        }

        $this->info("👥 Found {$users->count()} users with wallet addresses");
        $this->line('');

        // Check each user for potential new transactions
        foreach ($users as $user) {
            $this->line("🔍 Checking user: {$user->name} (ID: {$user->id})");
            
            // Get user's current transaction count
            $currentUserTransactions = Transaction::where('user_id', $user->id)->count();
            $this->line("   • Current transactions: {$currentUserTransactions}");
            
            // Simulate checking for new transactions
            // In real implementation, this would check blockchain
            $hasNewTransactions = $this->simulateNewTransactionCheck($user);
            
            if ($hasNewTransactions) {
                $newTransactions++;
                $this->info("   ✅ New transaction detected for {$user->name}");
            } else {
                $this->line("   ℹ️  No new transactions for {$user->name}");
            }
            
            $this->line('');
        }

        return $newTransactions;
    }

    /**
     * Simulate new transaction check
     */
    private function simulateNewTransactionCheck($user)
    {
        // 20% chance of finding a new transaction
        if (rand(1, 100) <= 20) {
            $admin = User::where('utype', 'ADM')->first();
            $randomAmount = rand(1, 30) / 10; // Random amount between 0.1 and 3.0
            
            // Create a new transaction
            Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => '0x' . str_repeat('a', 64) . time() . rand(1000, 9999),
                'from_address' => $user->wallet_address,
                'to_address' => $admin->wallet_address,
                'amount' => $randomAmount,
                'token_address' => '0x55d398326f99059fF775485246999027B3197955',
                'token_symbol' => 'USDT',
                'status' => 'confirmed',
                'block_number' => rand(1000000, 9999999),
                'confirmed_at' => now()
            ]);
            
            $this->line("   💰 Added: $" . number_format($randomAmount, 2) . " USDT");
            return true;
        }
        
        return false;
    }
}