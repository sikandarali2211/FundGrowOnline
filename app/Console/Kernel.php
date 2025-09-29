<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process withdrawals every 5 minutes
        $schedule->command('withdrawals:process')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Monitor transaction confirmations every 2 minutes
        $schedule->command('withdrawals:process --monitor')
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Show statistics every hour
        $schedule->command('withdrawals:process --stats')
            ->hourly()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
