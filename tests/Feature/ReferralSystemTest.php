<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Commission;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_builds_referral_upline_on_user_registration()
    {
        // Create referrer (level 1)
        $referrer = User::factory()->create(['referrer_id' => null]);
        
        // Create level 2 user
        $level2User = User::factory()->create(['referrer_id' => $referrer->id]);
        
        // Create level 3 user
        $level3User = User::factory()->create(['referrer_id' => $level2User->id]);
        
        // Create new user with referrer
        $newUser = User::factory()->create(['referrer_id' => $level3User->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($newUser, $level3User);

        // Check that referrals were created for all 6 levels
        $this->assertDatabaseHas('referrals', [
            'user_id' => $level3User->id,
            'referred_user_id' => $newUser->id,
            'level' => 1,
        ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $level2User->id,
            'referred_user_id' => $newUser->id,
            'level' => 2,
        ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $referrer->id,
            'referred_user_id' => $newUser->id,
            'level' => 3,
        ]);

        // Level 4-6 should not exist since we only have 3 levels in the upline
        $this->assertDatabaseMissing('referrals', [
            'referred_user_id' => $newUser->id,
            'level' => 4,
        ]);
    }

    /** @test */
    public function it_generates_commissions_on_completed_transaction()
    {
        // Create referrer (level 1)
        $referrer = User::factory()->create(['referrer_id' => null]);
        
        // Create referred user
        $referredUser = User::factory()->create(['referrer_id' => $referrer->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($referredUser, $referrer);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $referredUser->id,
            'status' => 'completed',
            'amount' => 29.99,
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Check that commission was created for the referrer
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $referrer->id,
            'source_user_id' => $referredUser->id,
            'transaction_id' => $transaction->id,
            'level' => 1,
            'amount' => 15.00, // L1 commission amount
            'month' => $transaction->created_at->format('Y-m'),
        ]);
    }

    /** @test */
    public function it_enforces_monthly_eligibility_rule()
    {
        // Create referrer (level 1)
        $referrer = User::factory()->create(['referrer_id' => null]);
        
        // Create referred user
        $referredUser = User::factory()->create(['referrer_id' => $referrer->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($referredUser, $referrer);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $referredUser->id,
            'status' => 'completed',
            'amount' => 29.99,
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Check that commission was created with correct eligibility
        $commission = Commission::where('earner_user_id', $referrer->id)->first();
        
        // Since the referrer has a direct L1 sale in the same month, they should be eligible
        $this->assertEquals('eligible', $commission->eligibility);
    }

    /** @test */
    public function it_handles_commission_amounts_by_level()
    {
        // Create a 6-level upline
        $users = [];
        $users[0] = User::factory()->create(['referrer_id' => null]); // Top level
        
        for ($i = 1; $i <= 6; $i++) {
            $users[$i] = User::factory()->create(['referrer_id' => $users[$i-1]->id]);
        }

        // Build referral upline for the last user
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($users[6], $users[5]);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $users[6]->id,
            'status' => 'completed',
            'amount' => 29.99,
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Check commission amounts for each level
        $expectedAmounts = [
            1 => 15.00, // L1: 15 USDT
            2 => 10.00, // L2: 10 USDT
            3 => 5.00,  // L3: 5 USDT
            4 => 1.00,  // L4: 1 USDT
            5 => 1.00,  // L5: 1 USDT
            6 => 1.00,  // L6: 1 USDT
        ];

        foreach ($expectedAmounts as $level => $expectedAmount) {
            $commission = Commission::where('earner_user_id', $users[6-$level]->id)
                ->where('level', $level)
                ->first();
            
            $this->assertNotNull($commission, "Commission for level {$level} not found");
            $this->assertEquals($expectedAmount, $commission->amount, "Commission amount for level {$level} incorrect");
        }
    }
}
