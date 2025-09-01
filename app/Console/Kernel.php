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
        // Monthly commission processing (1st-10th of each month)
        $schedule->command('commissions:process-monthly')
                ->monthlyOn(1, '00:00')
                ->description('Process monthly commissions for eligible users');
        
        // Bimonthly magazine release reminder (every 2 months)
        $schedule->command('magazines:release-reminder')
                ->cron('0 9 1 */2 *') // Every 1st of every 2nd month at 9 AM
                ->description('Send reminder to admins for bimonthly magazine release');
        
        // Daily subscription expiry checks
        $schedule->command('subscriptions:check-expiry')
                ->dailyAt('06:00')
                ->description('Check for expired subscriptions and send notifications');
        
        // Weekly commission eligibility checks
        $schedule->command('commissions:check-eligibility')
                ->weekly()
                ->description('Check commission eligibility for all users');
        
        // Monthly cleanup of old logs and cache
        $schedule->command('system:cleanup')
                ->monthly()
                ->description('Clean up old logs, cache, and temporary files');
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
