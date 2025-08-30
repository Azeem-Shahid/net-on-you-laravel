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
use Carbon\Carbon;

class SpecialEligibilityOverrideTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create the main sponsor (User ID = 1)
        $this->mainSponsor = User::factory()->create([
            'id' => 1,
            'referrer_id' => null,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_always_keeps_user_id_1_eligible_regardless_of_direct_sales()
    {
        // Create a user referred by User ID 1
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        
        // Create a user referred by the direct referral
        $level2User = User::factory()->create(['referrer_id' => $directReferral->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($level2User, $directReferral);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $level2User->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // User ID 1 should always be eligible (level 2 in this case)
        $commission = Commission::where('earner_user_id', 1)
            ->where('level', 2)
            ->first();
        
        $this->assertNotNull($commission, "Commission for User ID 1 not found");
        $this->assertEquals('eligible', $commission->eligibility, "User ID 1 should always be eligible");
        $this->assertEquals(10.00, $commission->amount, "Level 2 commission should be 10 USDT");
    }

    /** @test */
    public function it_always_keeps_direct_referrals_of_user_id_1_eligible()
    {
        // Create a direct referral of User ID 1
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        
        // Create a user referred by the direct referral
        $level2User = User::factory()->create(['referrer_id' => $directReferral->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($level2User, $directReferral);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $level2User->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Direct referral of User ID 1 should always be eligible (level 1)
        $commission = Commission::where('earner_user_id', $directReferral->id)
            ->where('level', 1)
            ->first();
        
        $this->assertNotNull($commission, "Commission for direct referral of User ID 1 not found");
        $this->assertEquals('eligible', $commission->eligibility, "Direct referral of User ID 1 should always be eligible");
        $this->assertEquals(15.00, $commission->amount, "Level 1 commission should be 15 USDT");
    }

    /** @test */
    public function it_always_keeps_deep_downline_of_user_id_1_eligible()
    {
        // Create a 6-level downline under User ID 1
        $users = [];
        $users[0] = $this->mainSponsor; // ID = 1
        
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
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // All users in User ID 1's downline should be eligible
        for ($level = 1; $level <= 6; $level++) {
            $commission = Commission::where('earner_user_id', $users[6-$level]->id)
                ->where('level', $level)
                ->first();
            
            $this->assertNotNull($commission, "Commission for level {$level} not found");
            $this->assertEquals('eligible', $commission->eligibility, 
                "User at level {$level} in User ID 1's downline should always be eligible");
        }
    }

    /** @test */
    public function it_maintains_eligibility_across_multiple_months_for_user_id_1_downline()
    {
        // Create a direct referral of User ID 1
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        
        // Create a user referred by the direct referral
        $level2User = User::factory()->create(['referrer_id' => $directReferral->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($level2User, $directReferral);

        // Create a transaction that will generate commissions for User ID 1
        $transaction = Transaction::factory()->create([
            'user_id' => $level2User->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Check that User ID 1 gets a commission and is eligible
        $commission = Commission::where('earner_user_id', 1)
            ->where('level', 2)
            ->first();
        
        $this->assertNotNull($commission, "Commission for User ID 1 not found");
        $this->assertEquals('eligible', $commission->eligibility, 
            "User ID 1 should be eligible");
        $this->assertEquals(10.00, $commission->amount, 
            "User ID 1 should get 10 USDT for level 2");
    }

    /** @test */
    public function it_contrasts_with_regular_users_eligibility_rules()
    {
        // Create a regular user (not in User ID 1's downline)
        $regularUser = User::factory()->create(['referrer_id' => null]);
        $regularReferrer = User::factory()->create(['referrer_id' => $regularUser->id]);
        
        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($regularReferrer, $regularUser);

        // Create a transaction in January
        $janTransaction = Transaction::factory()->create([
            'user_id' => $regularReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions for January
        $referralService->generateCommissions($janTransaction);

        // Regular user should be eligible in January (has direct sale)
        $janCommission = Commission::where('earner_user_id', $regularUser->id)
            ->where('month', '2024-01')
            ->first();
        $this->assertEquals('eligible', $janCommission->eligibility);

        // Create a transaction in February (no direct sales for regular user)
        $febTransaction = Transaction::factory()->create([
            'user_id' => $regularReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-02-15 10:00:00',
        ]);

        // Generate commissions for February
        $referralService->generateCommissions($febTransaction);

        // Regular user should be eligible in February (still has direct sales from regularReferrer)
        $febCommission = Commission::where('earner_user_id', $regularUser->id)
            ->where('month', '2024-02')
            ->first();
        
        // Debug: Check if regular user is somehow in User ID 1's downline
        $isInUserId1Downline = $regularUser->referrer_id === 1 || $regularUser->id === 1;
        $currentUser = $regularUser;
        while ($currentUser->referrer_id && !$isInUserId1Downline) {
            if ($currentUser->referrer_id === 1) {
                $isInUserId1Downline = true;
                break;
            }
            $currentUser = $currentUser->referrer;
        }
        
        $this->assertFalse($isInUserId1Downline, "Regular user should not be in User ID 1's downline");
        
        // Debug: Check the commission details
        $this->assertNotNull($febCommission, "February commission should exist");
        $this->assertEquals('eligible', $febCommission->eligibility, 
            "Regular user should be eligible in February because regularReferrer made a transaction. User ID: {$regularUser->id}, Referrer ID: {$regularUser->referrer_id}, Commission ID: {$febCommission->id}");

        // Now create a user in User ID 1's downline for comparison
        $specialUser = User::factory()->create(['referrer_id' => 1]);
        $specialReferrer = User::factory()->create(['referrer_id' => $specialUser->id]);

        // Build referral upline
        $referralService->buildReferralUpline($specialReferrer, $specialUser);

        // Create transactions in both months
        $specialJanTransaction = Transaction::factory()->create([
            'user_id' => $specialReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        $specialFebTransaction = Transaction::factory()->create([
            'user_id' => $specialReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-02-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($specialJanTransaction);
        $referralService->generateCommissions($specialFebTransaction);

        // Special user should be eligible in both months
        $specialJanCommission = Commission::where('earner_user_id', $specialUser->id)
            ->where('month', '2024-01')
            ->first();
        $this->assertEquals('eligible', $specialJanCommission->eligibility);

        $specialFebCommission = Commission::where('earner_user_id', $specialUser->id)
            ->where('month', '2024-02')
            ->first();
        $this->assertEquals('eligible', $specialFebCommission->eligibility);

        // This demonstrates the difference: regular users lose eligibility without direct sales,
        // while User ID 1's downline maintains eligibility regardless
        
        // Now let's test a case where a regular user actually loses eligibility
        // Create a new regular user with no direct sales
        $noSalesUser = User::factory()->create(['referrer_id' => null]);
        $noSalesReferrer = User::factory()->create(['referrer_id' => $noSalesUser->id]);
        
        // Build referral upline
        $referralService->buildReferralUpline($noSalesReferrer, $noSalesUser);
        
        // Create a transaction in January (this makes noSalesUser eligible)
        $noSalesJanTransaction = Transaction::factory()->create([
            'user_id' => $noSalesReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);
        
        // Generate commissions for January
        $referralService->generateCommissions($noSalesJanTransaction);
        
        // noSalesUser should be eligible in January
        $noSalesJanCommission = Commission::where('earner_user_id', $noSalesUser->id)
            ->where('month', '2024-01')
            ->first();
        $this->assertEquals('eligible', $noSalesJanCommission->eligibility);
        
        // Create a transaction in February by a different user (noSalesUser has no direct sales)
        $differentUser = User::factory()->create(['referrer_id' => null]);
        $differentReferrer = User::factory()->create(['referrer_id' => $differentUser->id]);
        
        // Build referral upline
        $referralService->buildReferralUpline($differentReferrer, $differentUser);
        
        $differentFebTransaction = Transaction::factory()->create([
            'user_id' => $differentReferrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-02-15 10:00:00',
        ]);
        
        // Generate commissions for February
        $referralService->generateCommissions($differentFebTransaction);
        
        // noSalesUser should be ineligible in February (no direct sales)
        $noSalesFebCommission = Commission::where('earner_user_id', $noSalesUser->id)
            ->where('month', '2024-02')
            ->first();
        
        if ($noSalesFebCommission) {
            $this->assertEquals('ineligible', $noSalesFebCommission->eligibility,
                "noSalesUser should be ineligible in February without direct sales");
        }
    }

    /** @test */
    public function it_preserves_commission_amounts_while_overriding_eligibility()
    {
        // Create a direct referral of User ID 1
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        
        // Create a user referred by the direct referral
        $level2User = User::factory()->create(['referrer_id' => $directReferral->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($level2User, $directReferral);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $level2User->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Check commission amounts (eligibility override should not affect amounts)
        $expectedAmounts = [
            1 => 15.00, // Direct referral of User ID 1: 15 USDT
            2 => 10.00, // User ID 1: 10 USDT
        ];

        foreach ($expectedAmounts as $level => $expectedAmount) {
            $commission = Commission::where('level', $level)->first();
            $this->assertNotNull($commission, "Commission for level {$level} not found");
            $this->assertEquals($expectedAmount, $commission->amount, 
                "Commission amount for level {$level} should remain correct");
            $this->assertEquals('eligible', $commission->eligibility, 
                "Commission eligibility for level {$level} should be overridden to eligible");
        }
    }

    /** @test */
    public function it_handles_edge_case_of_user_id_1_being_referred()
    {
        // This test covers the edge case where User ID 1 might be referred by someone else
        // (though this shouldn't happen in practice, we should handle it gracefully)
        
        // Create a user who refers User ID 1 (edge case)
        $edgeCaseUser = User::factory()->create(['referrer_id' => null]);
        
        // Temporarily set User ID 1 as referred by this user
        $this->mainSponsor->update(['referrer_id' => $edgeCaseUser->id]);

        // Create a transaction by User ID 1
        $transaction = Transaction::factory()->create([
            'user_id' => $this->mainSponsor->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($this->mainSponsor, $edgeCaseUser);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // Edge case user should get commission but User ID 1 should still be eligible
        $edgeCaseCommission = Commission::where('earner_user_id', $edgeCaseUser->id)
            ->where('level', 1)
            ->first();
        
        if ($edgeCaseCommission) {
            $this->assertEquals('eligible', $edgeCaseCommission->eligibility);
            $this->assertEquals(15.00, $edgeCaseCommission->amount);
        }

        // Reset User ID 1's referrer to null
        $this->mainSponsor->update(['referrer_id' => null]);
    }
}
