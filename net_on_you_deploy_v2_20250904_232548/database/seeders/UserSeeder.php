<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create verified users with active subscriptions
        $verifiedUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x1234567890abcdef1234567890abcdef12345678',
                'email_verified_at' => now(),
                'subscription_plan' => 'Basic',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'es',
                'wallet_address' => '0xabcdef1234567890abcdef1234567890abcdef12',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
        ];

        // Create verified users with expired subscriptions
        $expiredUsers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x9876543210fedcba9876543210fedcba98765432',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'expired',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'fr',
                'wallet_address' => '0xfedcba0987654321fedcba0987654321fedcba09',
                'email_verified_at' => now(),
                'subscription_plan' => 'Basic',
                'subscription_status' => 'expired',
            ],
        ];

        // Create unverified users
        $unverifiedUsers = [
            [
                'name' => 'David Lee',
                'email' => 'david@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x1111111111111111111111111111111111111111',
                'email_verified_at' => null,
                'subscription_plan' => null,
                'subscription_status' => null,
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'de',
                'wallet_address' => '0x2222222222222222222222222222222222222222',
                'email_verified_at' => null,
                'subscription_plan' => null,
                'subscription_status' => null,
            ],
        ];

        // Create blocked users
        $blockedUsers = [
            [
                'name' => 'Frank Miller',
                'email' => 'frank@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'blocked',
                'language' => 'en',
                'wallet_address' => '0x3333333333333333333333333333333333333333',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'cancelled',
            ],
        ];

        // Create premium users with different languages
        $premiumUsers = [
            [
                'name' => 'Maria Garcia',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'es',
                'wallet_address' => '0x4444444444444444444444444444444444444444',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Pierre Dubois',
                'email' => 'pierre@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'fr',
                'wallet_address' => '0x5555555555555555555555555555555555555555',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Hans Mueller',
                'email' => 'hans@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'de',
                'wallet_address' => '0x6666666666666666666666666666666666666666',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
        ];

        // Create users with referral relationships
        $referralUsers = [
            [
                'name' => 'Referrer User',
                'email' => 'referrer@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x7777777777777777777777777777777777777777',
                'email_verified_at' => now(),
                'subscription_plan' => 'Premium',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Referred User 1',
                'email' => 'referred1@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x8888888888888888888888888888888888888888',
                'email_verified_at' => now(),
                'subscription_plan' => 'Basic',
                'subscription_status' => 'active',
            ],
            [
                'name' => 'Referred User 2',
                'email' => 'referred2@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x9999999999999999999999999999999999999999',
                'email_verified_at' => now(),
                'subscription_plan' => 'Basic',
                'subscription_status' => 'active',
            ],
        ];

        // Combine all users
        $allUsers = array_merge(
            $verifiedUsers,
            $expiredUsers,
            $unverifiedUsers,
            $blockedUsers,
            $premiumUsers,
            $referralUsers
        );

        // Create users and their subscriptions
        foreach ($allUsers as $userData) {
            $subscriptionPlan = $userData['subscription_plan'];
            $subscriptionStatus = $userData['subscription_status'];
            
            // Remove subscription data from user creation
            unset($userData['subscription_plan'], $userData['subscription_status']);
            
            $user = User::create($userData);
            
            // Create subscription if plan is specified
            if ($subscriptionPlan) {
                $startDate = now()->toDateString();
                $endDate = $subscriptionStatus === 'expired' 
                    ? now()->subDays(30)->toDateString()
                    : now()->addYear()->toDateString();
                
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_name' => $subscriptionPlan,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $subscriptionStatus === 'expired' ? 'expired' : $subscriptionStatus,
                ]);
            }
        }

        // Set up referral relationships
        $referrer = User::where('email', 'referrer@example.com')->first();
        $referred1 = User::where('email', 'referred1@example.com')->first();
        $referred2 = User::where('email', 'referred2@example.com')->first();
        
        if ($referrer && $referred1 && $referred2) {
            $referred1->update(['referrer_id' => $referrer->id]);
            $referred2->update(['referrer_id' => $referrer->id]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Created ' . count($allUsers) . ' users with various statuses and subscription types.');
    }
}
