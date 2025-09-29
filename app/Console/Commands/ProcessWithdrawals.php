<?php

namespace App\Console\Commands;

use App\Services\AutomatedWithdrawalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'withdrawals:process 
                            {--monitor : Monitor transaction confirmations}
                            {--stats : Show withdrawal statistics}
                            {--force : Force process even if system is in maintenance mode}';

    /**
     * The console command description.
     */
    protected $description = 'Process pending withdrawal requests automatically';

    protected AutomatedWithdrawalService $withdrawalService;

    public function __construct(AutomatedWithdrawalService $withdrawalService)
    {
        parent::__construct();
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated withdrawal processing...');

        try {
            // Check if system is in maintenance mode
            if (app()->isDownForMaintenance() && !$this->option('force')) {
                $this->warn('System is in maintenance mode. Use --force to override.');
                return 1;
            }

            // Show statistics if requested
            if ($this->option('stats')) {
                $this->showStatistics();
                return 0;
            }

            // Monitor transaction confirmations if requested
            if ($this->option('monitor')) {
                $this->monitorConfirmations();
                return 0;
            }

            // Process pending withdrawals
            $this->processWithdrawals();

        } catch (\Exception $e) {
            $this->error('Command failed: ' . $e->getMessage());
            Log::error('ProcessWithdrawals command failed: ' . $e->getMessage());
            return 1;
        }

        $this->info('Automated withdrawal processing completed.');
        return 0;
    }

    /**
     * Process pending withdrawals
     */
    private function processWithdrawals(): void
    {
        $this->info('Processing pending withdrawal requests...');

        $results = $this->withdrawalService->processPendingWithdrawals();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $results['processed']],
                ['Successful', $results['successful']],
                ['Failed', $results['failed']],
            ]
        );

        if (!empty($results['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->error('  - ' . json_encode($error));
            }
        }

        if ($results['processed'] > 0) {
            $this->info("Processed {$results['processed']} withdrawal requests.");
        } else {
            $this->info('No pending withdrawal requests found.');
        }
    }

    /**
     * Monitor transaction confirmations
     */
    private function monitorConfirmations(): void
    {
        $this->info('Monitoring transaction confirmations...');

        $results = $this->withdrawalService->monitorTransactionConfirmations();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Checked', $results['checked']],
                ['Confirmed', $results['confirmed']],
                ['Failed', $results['failed']],
            ]
        );

        if (!empty($results['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->error('  - ' . json_encode($error));
            }
        }

        if ($results['checked'] > 0) {
            $this->info("Checked {$results['checked']} transactions.");
        } else {
            $this->info('No pending transactions found.');
        }
    }

    /**
     * Show withdrawal statistics
     */
    private function showStatistics(): void
    {
        $this->info('Withdrawal Statistics:');
        $this->newLine();

        $stats = $this->withdrawalService->getWithdrawalStats();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Pending', $stats['total_pending']],
                ['Total Approved', $stats['total_approved']],
                ['Total Completed', $stats['total_completed']],
                ['Total Rejected', $stats['total_rejected']],
                ['Total Amount Pending', '$' . number_format($stats['total_amount_pending'], 2)],
                ['Total Amount Completed', '$' . number_format($stats['total_amount_completed'], 2)],
                ['Admin Balance', $stats['admin_balance'] . ' USDT'],
                ['Network Status', $stats['network_status']['success'] ? 'Connected' : 'Disconnected'],
            ]
        );

        if (!$stats['network_status']['success']) {
            $this->warn('Network Status: ' . $stats['network_status']['error']);
        }
    }
}
