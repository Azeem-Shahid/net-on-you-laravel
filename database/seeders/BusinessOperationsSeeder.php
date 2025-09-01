<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScheduledCommand;
use Carbon\Carbon;

class BusinessOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💼 Creating Business Operations scheduled commands...');

        // Business Operations Commands
        $businessCommands = [
            [
                'command' => 'subscriptions:check-expiry',
                'frequency' => 'daily',
                'status' => 'active',
                'description' => 'Check for expired subscriptions and send notifications',
                'next_run_at' => Carbon::now()->addDay()->startOfDay()->addHours(6) // 6 AM tomorrow
            ],
            [
                'command' => 'commissions:check-eligibility',
                'frequency' => 'weekly',
                'status' => 'active',
                'description' => 'Check commission eligibility for all users',
                'next_run_at' => Carbon::now()->next(Carbon::MONDAY)->startOfDay()->addHours(6) // Next Monday 6 AM
            ],
            [
                'command' => 'commissions:re-evaluate-eligibility',
                'frequency' => 'daily',
                'status' => 'active',
                'description' => 'Re-evaluate commission eligibility for changes',
                'next_run_at' => Carbon::now()->addDay()->startOfDay()->addHours(4) // 4 AM tomorrow
            ]
        ];

        foreach ($businessCommands as $commandData) {
            $scheduledCommand = ScheduledCommand::updateOrCreate(
                ['command' => $commandData['command']],
                [
                    'frequency' => $commandData['frequency'],
                    'status' => $commandData['status'],
                    'description' => $commandData['description'],
                    'next_run_at' => $commandData['next_run_at']
                ]
            );

            // Calculate next run time
            $scheduledCommand->calculateNextRun();
            $scheduledCommand->save();

            $this->command->info("✅ {$scheduledCommand->command} scheduled successfully!");
            $this->command->info("📅 Next run: {$scheduledCommand->next_run_at}");
            $this->command->info("🔄 Frequency: {$scheduledCommand->frequency}");
            $this->command->info("📊 Status: {$scheduledCommand->status}");
            $this->command->newLine();
        }

        $this->command->info('🎉 Business Operations commands created successfully!');
        $this->command->info('💡 These commands handle subscription management and commission processing');
        $this->command->info('🔗 You can manage them in Admin Panel → System Scheduler → Command Scheduler');
    }
}
