<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScheduledCommand;
use Carbon\Carbon;

class SystemCoreMaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌐 Creating System Core Maintenance scheduled command...');

        // Create or update the system core maintenance command
        $scheduledCommand = ScheduledCommand::updateOrCreate(
            ['command' => 'system:core-maintenance'],
            [
                'frequency' => 'daily',
                'status' => 'active',
                'description' => 'Run comprehensive system core maintenance (subscriptions, commissions, cleanup, health checks, backup, cache optimization, reports)',
                'next_run_at' => Carbon::now()->addDay()->startOfDay()->addHours(6) // 6 AM tomorrow
            ]
        );

        // Calculate next run time
        $scheduledCommand->calculateNextRun();
        $scheduledCommand->save();

        $this->command->info('✅ System Core Maintenance scheduled command created successfully!');
        $this->command->info("📅 Next run: {$scheduledCommand->next_run_at}");
        $this->command->info("🔄 Frequency: {$scheduledCommand->frequency}");
        $this->command->info("📊 Status: {$scheduledCommand->status}");
    }
}
