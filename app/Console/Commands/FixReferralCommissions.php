<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommissionTransaction;
use Illuminate\Support\Facades\DB;

class FixReferralCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:fix-referral {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing referral chain commissions to go to pool wallet instead of profit wallet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔧 Fixing Referral Chain Commissions');
        $this->newLine();

        // Get all referral chain commissions that have profit_commission > 0
        $referralCommissions = CommissionTransaction::where('commission_type', 'referral_chain')
            ->where('profit_commission', '>', 0)
            ->get();

        $this->info("📊 Found {$referralCommissions->count()} referral chain commissions to fix");
        $this->newLine();

        if ($referralCommissions->count() == 0) {
            $this->info('✅ No referral chain commissions need fixing');
            return 0;
        }

        $fixed = 0;
        $totalAmount = 0;

        foreach ($referralCommissions as $commission) {
            $user = User::find($commission->user_id);
            if (!$user) {
                $this->warn("⚠️  User not found for commission ID {$commission->id}");
                continue;
            }

            $amount = $commission->profit_commission;
            $totalAmount += $amount;

            $this->info("👤 Fixing commission for: {$user->name} (ID: {$user->id})");
            $this->line("   Amount: \${$amount}");
            $this->line("   Current Pool Commission: \${$user->referral_commission_balance}");
            $this->line("   Current Pool Wallet: \${$user->pool_wallet_amount}");

            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($user, $commission, $amount) {
                        // Move from profit wallet to pool commission and pool wallet
                        $user->profit_wallet = max(0, ($user->profit_wallet ?? 0) - $amount);
                        $user->referral_commission_balance = ($user->referral_commission_balance ?? 0) + $amount;
                        $user->referral_commission_pool = ($user->referral_commission_pool ?? 0) + $amount;
                        $user->pool_wallet_amount = ($user->pool_wallet_amount ?? 0) + $amount;
                        $user->save();

                        // Update commission transaction record
                        $commission->pool_commission = $amount;
                        $commission->profit_commission = 0;
                        $commission->save();
                    });

                    $this->info("   ✅ Fixed successfully");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("   ❌ Error: " . $e->getMessage());
                }
            } else {
                $this->info("   🔍 [DRY RUN] Would move \${$amount} from profit to pool");
            }

            $this->newLine();
        }

        if ($dryRun) {
            $this->info("🔍 DRY RUN COMPLETED");
            $this->line("Would fix {$referralCommissions->count()} commissions");
            $this->line("Total amount to move: \${$totalAmount}");
            $this->newLine();
            $this->info("Run without --dry-run to apply changes");
        } else {
            $this->info("📊 Summary:");
            $this->line("   ✅ Fixed: {$fixed}");
            $this->line("   💰 Total amount moved: \${$totalAmount}");
        }

        return 0;
    }
}
