<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        
        // Call individual seeders in order
        $this->call([
            CoreLanguageSeeder::class, // Core languages first
            AdminSeeder::class,
            UserSeeder::class,
            MagazineSeeder::class,
            SubscriptionSeeder::class,
            ReferralSeeder::class,
            SettingsSeeder::class,
            SecurityPolicySeeder::class,
            ContractSeeder::class,
            ScheduledCommandsSeeder::class,
            EnhancedDummyDataSeeder::class, // Enhanced dummy data with User 1 referrals
        ]);
        
        $this->command->info('Database seeding completed successfully!');
    }
}

