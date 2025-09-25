<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;

class CheckUserTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check {user_id : User ID to check transactions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user transactions and balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info('🔍 Checking transactions for User ID: ' . $userId);
        $this->line('');
        
        try {
            // Get user details
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return 1;
            }

            // Display user info
            $this->info("👤 User Details:");
            $this->line("   • Name: {$user->name}");
            $this->line("   • Email: {$user->email}");
            $this->line("   • Wallet: {$user->wallet_address}");
            $this->line("");

            // Get admin wallet
            $admin = User::where('utype', 'ADM')->first();
            if ($admin) {
                $this->info("📊 Admin Wallet: {$admin->wallet_address}");
                $this->line("");
            }

            // Get all transactions for this user
            $transactions = Transaction::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->orderBy('created_at', 'desc')
                ->get();

            // Display transaction summary
            $this->info("💰 Transaction Summary:");
            $this->line("   • Total Transactions: " . $transactions->count());
            $this->line("   • Total Amount Sent: $" . number_format($transactions->sum('amount'), 2));
            $this->line("");

            // Display individual transactions
            if ($transactions->count() > 0) {
                $this->info("📋 Transaction History:");
                $this->line("┌─────────────────────────────────────────────────────────────────────────────────┐");
                $this->line("│ Date/Time                │ Amount    │ Token  │ Status    │ Hash (First 10)      │");
                $this->line("├─────────────────────────────────────────────────────────────────────────────────┤");
                
                foreach ($transactions as $tx) {
                    $date = $tx->created_at->format('Y-m-d H:i:s');
                    $amount = "$" . number_format($tx->amount, 2);
                    $token = $tx->token_symbol ?? 'USDT';
                    $status = $tx->status;
                    $hash = substr($tx->tx_hash, 0, 10) . '...';
                    
                    $this->line("│ " . str_pad($date, 25) . " │ " . str_pad($amount, 9) . " │ " . str_pad($token, 6) . " │ " . str_pad($status, 9) . " │ " . str_pad($hash, 20) . " │");
                }
                
                $this->line("└─────────────────────────────────────────────────────────────────────────────────┘");
                $this->line("");
            }

            // Get investments
            $investments = UserInvestment::where('user_id', $userId)
                ->where('status', 'active')
                ->sum('amount');

            // Calculate total balance
            $totalSent = $transactions->sum('amount');
            $totalBalance = $investments + $totalSent;

            // Display balance breakdown
            $this->info("💳 Balance Breakdown:");
            $this->line("   • Investments: $" . number_format($investments, 2));
            $this->line("   • Sent Amount: $" . number_format($totalSent, 2));
            $this->line("   • Total Balance: $" . number_format($totalBalance, 2));
            $this->line("");

            // Display admin received summary
            if ($admin) {
                $this->info("📥 Admin Received Summary:");
                $this->line("   • From User: $" . number_format($totalSent, 2));
                $this->line("   • Admin Wallet: {$admin->wallet_address}");
                $this->line("");
            }

            // Status summary
            if ($totalSent > 0) {
                $this->info("✅ Status: User has sent $" . number_format($totalSent, 2) . " to admin wallet");
                $this->info("✅ Balance: $" . number_format($totalBalance, 2) . " (including sent amount)");
            } else {
                $this->warn("⚠️  Status: No transactions found for this user");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}