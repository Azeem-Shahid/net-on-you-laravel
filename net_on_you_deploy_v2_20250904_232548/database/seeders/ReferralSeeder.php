<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\Subscription;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users who can be referrers (have active subscriptions)
        $potentialReferrers = User::whereHas('subscriptions', function($query) {
            $query->where('status', 'active');
        })->get();

        if ($potentialReferrers->isEmpty()) {
            $this->command->info('No users with active subscriptions found. Skipping referral seeding...');
            return;
        }

        // Create referral relationships
        foreach ($potentialReferrers->take(5) as $referrer) {
            // Create 2-3 referred users for each referrer
            $referredCount = rand(2, 3);
            
            for ($i = 0; $i < $referredCount; $i++) {
                $referredUser = User::create([
                    'name' => 'Referred User ' . $referrer->id . '-' . ($i + 1),
                    'email' => 'referred' . $referrer->id . '_' . ($i + 1) . '@example.com',
                    'password' => bcrypt('password123'),
                    'role' => 'user',
                    'status' => 'active',
                    'language' => 'en',
                    'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                    'email_verified_at' => now(),
                    'referrer_id' => $referrer->id,
                ]);

                // Create subscription for referred user
                $subscription = Subscription::create([
                    'user_id' => $referredUser->id,
                    'plan_name' => rand(0, 1) ? 'Basic' : 'Premium',
                    'start_date' => now()->subDays(rand(1, 90)),
                    'end_date' => now()->addDays(rand(1, 300)),
                    'status' => 'active',
                ]);

                // Create transaction for the subscription
                $transaction = Transaction::create([
                    'user_id' => $referredUser->id,
                    'amount' => 19.99, // Fixed amount since no price field
                    'currency' => 'USD',
                    'gateway' => 'credit_card',
                    'status' => 'completed',
                    'transaction_hash' => '0x' . strtoupper(uniqid()),
                    'notes' => 'Subscription payment for ' . $subscription->plan_name,
                ]);

                // Create commission for the referrer (using fixed amount since no price field)
                $commissionAmount = 1.99; // Fixed commission amount
                
                Commission::create([
                    'earner_user_id' => $referrer->id,
                    'source_user_id' => $referredUser->id,
                    'transaction_id' => $transaction->id,
                    'level' => 1, // Level 1 referral
                    'amount' => $commissionAmount,
                    'month' => $subscription->start_date->format('Y-m'),
                    'eligibility' => 'eligible',
                    'payout_status' => 'pending',
                ]);

                // Create referral record
                Referral::create([
                    'user_id' => $referrer->id,
                    'referred_user_id' => $referredUser->id,
                    'level' => 1, // Level 1 referral
                ]);
            }
        }

        // Create some paid commissions
        $pendingCommissions = Commission::where('payout_status', 'pending')->take(3)->get();
        foreach ($pendingCommissions as $commission) {
            $commission->update([
                'payout_status' => 'paid',
            ]);
        }

        // Create some void commissions
        $ineligibleCommissions = Commission::where('payout_status', 'pending')->take(2)->get();
        foreach ($ineligibleCommissions as $commission) {
            $commission->update([
                'eligibility' => 'ineligible',
                'payout_status' => 'void',
            ]);
        }

        $this->command->info('Referral system seeded successfully!');
        $this->command->info('Created referral relationships and commissions for ' . $potentialReferrers->count() . ' referrers.');
        $this->command->info('Commission rate: 10% of subscription amount');
        $this->command->info('Commission statuses: Pending, Paid, Void');
    }
}
