<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduledCommand;
use Carbon\Carbon;

class ScheduledCommandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commands = [
            [
                'command' => 'subscriptions:check-expiry',
                'frequency' => 'daily',
                'status' => 'active',
                'description' => 'Check for expired subscriptions and send notifications',
                'next_run_at' => Carbon::tomorrow()->setTime(6, 0, 0), // 6 AM tomorrow
            ],
            [
                'command' => 'commissions:process-monthly',
                'frequency' => 'monthly',
                'status' => 'active',
                'description' => 'Process monthly commissions for eligible users',
                'next_run_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(1)->setTime(0, 0, 0), // 1st of next month
            ],
            [
                'command' => 'magazines:release-reminder',
                'frequency' => 'monthly',
                'status' => 'active',
                'description' => 'Send reminder to admins for bimonthly magazine release',
                'next_run_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(1)->setTime(9, 0, 0), // 1st of next month at 9 AM
            ],
            [
                'command' => 'commissions:check-eligibility',
                'frequency' => 'weekly',
                'status' => 'active',
                'description' => 'Check commission eligibility for all users',
                'next_run_at' => Carbon::now()->addWeek()->startOfWeek()->addDays(1)->setTime(6, 0, 0), // Next Monday 6 AM
            ],
            [
                'command' => 'system:cleanup',
                'frequency' => 'monthly',
                'status' => 'active',
                'description' => 'Clean up old logs, cache, and temporary files',
                'next_run_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(1)->setTime(6, 0, 0), // 1st of next month 6 AM
            ],
            [
                'command' => 'system:health-check',
                'frequency' => 'daily',
                'status' => 'inactive',
                'description' => 'Check system health and send alerts',
                'next_run_at' => null,
            ],
            [
                'command' => 'system:backup-database',
                'frequency' => 'weekly',
                'status' => 'inactive',
                'description' => 'Create database backup',
                'next_run_at' => null,
            ],
            [
                'command' => 'system:optimize-cache',
                'frequency' => 'weekly',
                'status' => 'inactive',
                'description' => 'Optimize application cache',
                'next_run_at' => null,
            ],
            [
                'command' => 'system:generate-reports',
                'frequency' => 'monthly',
                'status' => 'inactive',
                'description' => 'Generate monthly reports and analytics',
                'next_run_at' => null,
            ],
            [
                'command' => 'system:clear-expired-reports',
                'frequency' => 'daily',
                'status' => 'inactive',
                'description' => 'Clear expired report cache',
                'next_run_at' => null,
            ],
            [
                'command' => 'commissions:re-evaluate-eligibility',
                'frequency' => 'daily',
                'status' => 'inactive',
                'description' => 'Re-evaluate commission eligibility for changes',
                'next_run_at' => null,
            ],
        ];

        foreach ($commands as $commandData) {
            ScheduledCommand::updateOrCreate(
                ['command' => $commandData['command']],
                $commandData
            );
        }

        $this->command->info('Scheduled commands seeded successfully!');
    }
}
