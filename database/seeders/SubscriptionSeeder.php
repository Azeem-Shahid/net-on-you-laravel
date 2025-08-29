<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users who don't have subscriptions yet
        $users = User::whereDoesntHave('subscriptions')->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found without subscriptions. Creating sample subscriptions...');
            return;
        }

        $subscriptionPlans = [
            'Basic' => [
                'duration' => 30,
            ],
            'Premium' => [
                'duration' => 30,
            ],
            'Pro' => [
                'duration' => 30,
            ],
            'Annual Basic' => [
                'duration' => 365,
            ],
            'Annual Premium' => [
                'duration' => 365,
            ],
        ];

        $subscriptionStatuses = ['active', 'expired', 'cancelled'];

        foreach ($users as $index => $user) {
            // Skip users that already have subscriptions
            if ($user->subscriptions()->exists()) {
                continue;
            }

            // Select a random plan
            $planName = array_keys($subscriptionPlans)[$index % count($subscriptionPlans)];
            $plan = $subscriptionPlans[$planName];
            
            // Select a random status
            $status = $subscriptionStatuses[$index % count($subscriptionStatuses)];
            
            // Calculate dates based on status
            $startDate = now();
            $endDate = $startDate->copy()->addDays($plan['duration']);
            
            if ($status === 'expired') {
                $startDate = now()->subDays($plan['duration'] + 30);
                $endDate = now()->subDays(30);
            } elseif ($status === 'cancelled') {
                $endDate = $startDate->copy()->addDays(rand(5, $plan['duration'] - 5));
            } elseif ($status === 'cancelled') {
                $endDate = $startDate->copy()->addDays(rand(5, $plan['duration'] - 5));
            }

            Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $planName,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ]);
        }

        // Create some additional subscriptions for users who already have them
        $usersWithSubs = User::whereHas('subscriptions')->take(3)->get();
        
        foreach ($usersWithSubs as $user) {
            // Create a second subscription (maybe an upgrade)
            $planName = 'Premium';
            $plan = $subscriptionPlans[$planName];
            
            Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $planName,
                'start_date' => now()->addDays(rand(1, 30)),
                'end_date' => now()->addDays(rand(31, 395)),
                'status' => 'active',
            ]);
        }

        $this->command->info('Subscriptions seeded successfully!');
        $this->command->info('Created subscriptions for ' . $users->count() . ' users.');
        $this->command->info('Plans: Basic, Premium, Pro, Annual Basic, Annual Premium');
        $this->command->info('Statuses: Active, Expired, Cancelled');
    }
}
