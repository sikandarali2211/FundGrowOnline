<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;

class CheckAllUsersTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-all {--limit=10 : Limit number of users to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check transactions for all users who made payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('🔍 Checking transactions for ALL users...');
        $this->line('');
        
        try {
            // Get all users who have transactions
            $usersWithTransactions = User::whereHas('transactions')
                ->with(['transactions' => function($query) {
                    $query->where('status', 'confirmed');
                }])
                ->take($limit)
                ->get();

            if ($usersWithTransactions->isEmpty()) {
                $this->warn('⚠️  No users with transactions found');
                return 0;
            }

            $this->info("📊 Found {$usersWithTransactions->count()} users with transactions");
            $this->line('');

            // Get admin wallet
            $admin = User::where('utype', 'ADM')->first();
            if ($admin) {
                $this->info("📥 Admin Wallet: {$admin->wallet_address}");
                $this->line('');
            }

            $totalUsers = 0;
            $totalAmount = 0;
            $totalTransactions = 0;

            // Display summary table
            $this->info("📋 Users Transaction Summary:");
            $this->line("┌─────────────────────────────────────────────────────────────────────────────────────────────────┐");
            $this->line("│ User ID │ Name                │ Email                    │ Transactions │ Amount Sent │ Balance    │");
            $this->line("├─────────────────────────────────────────────────────────────────────────────────────────────────┤");

            foreach ($usersWithTransactions as $user) {
                $userTransactions = $user->transactions;
                $userAmount = $userTransactions->sum('amount');
                $userBalance = $userAmount; // For now, just sent amount
                
                $totalUsers++;
                $totalAmount += $userAmount;
                $totalTransactions += $userTransactions->count();

                $this->line("│ " . 
                    str_pad($user->id, 8) . " │ " . 
                    str_pad(substr($user->name, 0, 18), 18) . " │ " . 
                    str_pad(substr($user->email, 0, 24), 24) . " │ " . 
                    str_pad($userTransactions->count(), 11) . " │ " . 
                    str_pad("$" . number_format($userAmount, 2), 11) . " │ " . 
                    str_pad("$" . number_format($userBalance, 2), 10) . " │"
                );
            }

            $this->line("└─────────────────────────────────────────────────────────────────────────────────────────────────┘");
            $this->line('');

            // Display overall summary
            $this->info("💰 Overall Summary:");
            $this->line("   • Total Users: {$totalUsers}");
            $this->line("   • Total Transactions: {$totalTransactions}");
            $this->line("   • Total Amount Sent: $" . number_format($totalAmount, 2));
            $this->line("   • Average per User: $" . number_format($totalAmount / $totalUsers, 2));
            $this->line("");

            // Show recent transactions
            $this->info("📈 Recent Transactions (Last 10):");
            $recentTransactions = Transaction::where('status', 'confirmed')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            if ($recentTransactions->count() > 0) {
                $this->line("┌─────────────────────────────────────────────────────────────────────────────────────────────┐");
                $this->line("│ Date/Time            │ User ID │ User Name        │ Amount    │ Token  │ Status    │ Hash (First 8) │");
                $this->line("├─────────────────────────────────────────────────────────────────────────────────────────────┤");
                
                foreach ($recentTransactions as $tx) {
                    $date = $tx->created_at->format('Y-m-d H:i:s');
                    $amount = "$" . number_format($tx->amount, 2);
                    $token = $tx->token_symbol ?? 'USDT';
                    $status = $tx->status;
                    $hash = substr($tx->tx_hash, 0, 8) . '...';
                    $userName = $tx->user ? substr($tx->user->name, 0, 15) : 'Unknown';
                    
                    $this->line("│ " . 
                        str_pad($date, 20) . " │ " . 
                        str_pad($tx->user_id, 8) . " │ " . 
                        str_pad($userName, 15) . " │ " . 
                        str_pad($amount, 9) . " │ " . 
                        str_pad($token, 6) . " │ " . 
                        str_pad($status, 9) . " │ " . 
                        str_pad($hash, 14) . " │"
                    );
                }
                
                $this->line("└─────────────────────────────────────────────────────────────────────────────────────────────┘");
            }

            $this->line('');
            $this->info("✅ Summary: {$totalUsers} users have made payments totaling $" . number_format($totalAmount, 2));

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}