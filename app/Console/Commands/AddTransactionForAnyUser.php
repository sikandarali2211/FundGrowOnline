<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;

class AddTransactionForAnyUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:add {user_id} {amount} {--hash= : Transaction hash (optional)} {--email= : User email (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add transaction for any user by ID or email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = $this->argument('amount');
        $txHash = $this->option('hash');
        $email = $this->option('email');
        
        $this->info('➕ Adding transaction...');
        $this->line('');
        
        try {
            // Find user by ID or email
            $user = null;
            if (is_numeric($userId)) {
                $user = User::find($userId);
            } elseif ($email) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $this->error("❌ User not found");
                $this->line("   • Searched by ID: {$userId}");
                if ($email) {
                    $this->line("   • Searched by Email: {$email}");
                }
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
                $txHash = '0x' . str_repeat('a', 64) . time();
            }

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
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
            $this->line("   • User ID: {$user->id}");
            $this->line("   • Amount: $" . number_format($amount, 2) . " USDT");
            $this->line("   • Hash: {$transaction->tx_hash}");
            $this->line("   • Status: {$transaction->status}");
            $this->line("   • Date: {$transaction->created_at->format('Y-m-d H:i:s')}");
            $this->line("");

            // Show updated balance
            $totalAmount = Transaction::where('user_id', $user->id)->sum('amount');
            $this->info("💰 Updated Balance: $" . number_format($totalAmount, 2));

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}