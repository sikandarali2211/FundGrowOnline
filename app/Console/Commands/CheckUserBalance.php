<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;

class CheckUserBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:balance {user_id : User ID to check balance for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user balance and transaction details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        try {
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return 1;
            }

            $this->info("👤 User: {$user->name} ({$user->email})");
            $this->info("🔗 Wallet: {$user->wallet_address}");
            $this->line("");

            // Get transactions
            $transactions = Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->orderBy('created_at', 'desc')
                ->get();

            $this->info("📊 Transaction Details:");
            $this->line("   • Total Transactions: {$transactions->count()}");
            $this->line("   • Total Amount: $" . number_format($transactions->sum('amount'), 2));
            $this->line("");

            if ($transactions->count() > 0) {
                $this->info("📋 Recent Transactions:");
                foreach ($transactions as $tx) {
                    $this->line("   • {$tx->created_at->format('Y-m-d H:i:s')} - $" . number_format($tx->amount, 2) . " {$tx->token_symbol} - {$tx->status}");
                }
                $this->line("");
            }

            // Get investments
            $investments = UserInvestment::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('amount');

            $this->info("💰 Balance Breakdown:");
            $this->line("   • Investments: $" . number_format($investments, 2));
            $this->line("   • Sent Amount: $" . number_format($transactions->sum('amount'), 2));
            $this->line("   • Total Balance: $" . number_format($investments + $transactions->sum('amount'), 2));

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}