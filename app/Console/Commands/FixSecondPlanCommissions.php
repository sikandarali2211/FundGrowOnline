<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CommissionTransaction;
use Illuminate\Support\Facades\DB;

class FixSecondPlanCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:fix-second-plan {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix second plan commissions to go to pool wallet instead of profit wallet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔧 Fixing Second Plan Commissions');
        $this->newLine();

        // Get all second plan commissions that have profit_commission > 0
        $secondPlanCommissions = CommissionTransaction::where('commission_type', 'second_plan')
            ->where('profit_commission', '>', 0)
            ->get();

        $this->info("📊 Found {$secondPlanCommissions->count()} second plan commissions to fix");
        $this->newLine();

        if ($secondPlanCommissions->count() == 0) {
            $this->info('✅ No second plan commissions need fixing');
            return 0;
        }

        $fixed = 0;
        $totalAmount = 0;

        foreach ($secondPlanCommissions as $commission) {
            $user = User::find($commission->user_id);
            if (!$user) {
                $this->warn("⚠️  User not found for commission ID {$commission->id}");
                continue;
            }

            $amount = $commission->profit_commission;
            $totalAmount += $amount;

            $this->info("👤 Fixing commission for: {$user->name} (ID: {$user->id})");
            $this->line("   Amount to move: \${$amount}");
            $this->line("   Current Pool Commission: \${$user->referral_commission_balance}");
            $this->line("   Current Pool Wallet: \${$user->pool_wallet_amount}");
            $this->line("   Current Profit Wallet: \${$user->profit_wallet}");

            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($user, $commission, $amount) {
                        // Move from profit wallet to pool wallet
                        $user->profit_wallet = max(0, ($user->profit_wallet ?? 0) - $amount);
                        $user->pool_wallet_amount = ($user->pool_wallet_amount ?? 0) + $amount;
                        $user->save();

                        // Update commission transaction record
                        $commission->pool_commission = $commission->pool_commission + $amount;
                        $commission->profit_commission = 0;
                        $commission->save();
                    });

                    $this->info("   ✅ Fixed successfully");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("   ❌ Error: " . $e->getMessage());
                }
            } else {
                $this->info("   🔍 [DRY RUN] Would move \${$amount} from profit to pool wallet");
            }

            $this->newLine();
        }

        if ($dryRun) {
            $this->info("🔍 DRY RUN COMPLETED");
            $this->line("Would fix {$secondPlanCommissions->count()} commissions");
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
