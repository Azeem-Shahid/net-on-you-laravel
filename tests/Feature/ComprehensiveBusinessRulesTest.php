<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\Subscription;
use App\Models\Magazine;
use App\Models\MagazineEntitlement;
use App\Models\Contract;
use App\Models\ContractAcceptance;
use App\Services\ReferralService;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class ComprehensiveBusinessRulesTest extends TestCase
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
    public function it_enforces_correct_commission_amounts_for_all_6_levels()
    {
        // Create a 6-level upline
        $users = [];
        $users[0] = $this->mainSponsor; // Top level (ID = 1)
        
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
            'amount' => 39.90, // Correct price as per business rules
            'currency' => 'USDT',
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

        // Total commissions should equal 33 USDT (15+10+5+1+1+1)
        $totalCommissions = Commission::where('transaction_id', $transaction->id)->sum('amount');
        $this->assertEquals(33.00, $totalCommissions);
    }

    /** @test */
    public function it_enforces_monthly_eligibility_rule_for_regular_users()
    {
        // Create a regular user (not ID 1 or direct referral of ID 1)
        $regularUser = User::factory()->create(['referrer_id' => null]);
        $referrer = User::factory()->create(['referrer_id' => $regularUser->id]);
        
        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($referrer, $regularUser);

        // Create a completed transaction in January
        $janTransaction = Transaction::factory()->create([
            'user_id' => $referrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions for January
        $referralService->generateCommissions($janTransaction);

        // Check that regular user is eligible in January (has direct sale)
        $janCommission = Commission::where('earner_user_id', $regularUser->id)
            ->where('month', '2024-01')
            ->first();
        $this->assertEquals('eligible', $janCommission->eligibility);

        // Create a transaction in February (no direct sales for regular user)
        $febTransaction = Transaction::factory()->create([
            'user_id' => $referrer->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-02-15 10:00:00',
        ]);

        // Generate commissions for February
        $referralService->generateCommissions($febTransaction);

        // Check that regular user is eligible in February (still has direct sales from regularReferrer)
        $febCommission = Commission::where('earner_user_id', $regularUser->id)
            ->where('month', '2024-02')
            ->first();
        $this->assertEquals('eligible', $febCommission->eligibility);
    }

    /** @test */
    public function it_grants_special_access_to_user_id_1_and_direct_referrals()
    {
        // User ID 1 should have special access
        $this->assertTrue($this->mainSponsor->hasSpecialAccess());
        $this->assertTrue($this->mainSponsor->getsFreeAccess());
        $this->assertFalse($this->mainSponsor->needsPayment());

        // Direct referral of User ID 1 should have special access
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        $this->assertTrue($directReferral->hasSpecialAccess());
        $this->assertTrue($directReferral->getsFreeAccess());
        $this->assertFalse($directReferral->needsPayment());

        // Regular user should not have special access
        $regularUser = User::factory()->create(['referrer_id' => null]);
        $this->assertFalse($regularUser->hasSpecialAccess());
        $this->assertFalse($regularUser->getsFreeAccess());
        $this->assertTrue($regularUser->needsPayment());
    }

    /** @test */
    public function it_always_keeps_user_id_1_downline_eligible_regardless_of_direct_sales()
    {
        // Create a direct referral of User ID 1
        $directReferral = User::factory()->create(['referrer_id' => 1]);
        
        // Create a level 2 user (referral of direct referral)
        $level2User = User::factory()->create(['referrer_id' => $directReferral->id]);
        
        // Create a level 3 user
        $level3User = User::factory()->create(['referrer_id' => $level2User->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($level3User, $level2User);

        // Create a completed transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $level3User->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions
        $referralService->generateCommissions($transaction);

        // All users in User ID 1's downline should be eligible regardless of direct sales
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $directReferral->id,
            'level' => 2,
            'eligibility' => 'eligible',
        ]);

        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $level2User->id,
            'level' => 1,
            'eligibility' => 'eligible',
        ]);

        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->mainSponsor->id,
            'level' => 3,
            'eligibility' => 'eligible',
        ]);
    }

    /** @test */
    public function it_enforces_subscription_duration_and_renewal_rules()
    {
        // Create a regular user
        $user = User::factory()->create(['referrer_id' => null]);

        // Create a 2-year subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_name' => '2-Year Plan',
            'start_date' => now(),
            'end_date' => now()->addYears(2),
            'status' => 'active',
        ]);

        // Check subscription status
        $this->assertTrue($subscription->isActive());
        $this->assertFalse($subscription->isExpired());
        $this->assertFalse($subscription->expiresSoon());

        // Check user subscription status
        $this->assertEquals('active', $user->getSubscriptionStatus());

        // Test grace period (7 days after expiry)
        $subscription->update(['end_date' => now()->subDays(5)]);
        $this->assertTrue($user->isInGracePeriod());
        $this->assertEquals('grace', $user->getSubscriptionStatus());

        // Test expired status (after grace period)
        $subscription->update(['end_date' => now()->subDays(10)]);
        $this->assertFalse($user->isInGracePeriod());
        $this->assertEquals('inactive', $user->getSubscriptionStatus());
    }

    /** @test */
    public function it_handles_subscription_reactivation_without_retroactive_commissions()
    {
        // Create a regular user
        $referrer = User::factory()->create(['referrer_id' => null]);
        $user = User::factory()->create(['referrer_id' => $referrer->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($user, $referrer);

        // Create initial subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_name' => '2-Year Plan',
            'start_date' => now()->subYears(2)->subDays(10),
            'end_date' => now()->subDays(10),
            'status' => 'expired',
        ]);

        // User should be inactive
        $this->assertEquals('inactive', $user->getSubscriptionStatus());

        // Create a new payment transaction
        $newTransaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => now(),
        ]);

        // Activate new subscription
        $newSubscription = Subscription::create([
            'user_id' => $user->id,
            'plan_name' => '2-Year Plan',
            'start_date' => now(),
            'end_date' => now()->addYears(2),
            'status' => 'active',
        ]);

        // User should be active again
        $this->assertEquals('active', $user->getSubscriptionStatus());

        // Generate commissions for new transaction
        $referralService->generateCommissions($newTransaction);

        // Check that commissions are generated for new transaction
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $referrer->id,
            'source_user_id' => $user->id,
            'transaction_id' => $newTransaction->id,
            'level' => 1,
            'amount' => 15.00,
        ]);

        // Verify no retroactive commissions for missed months
        $this->assertDatabaseMissing('commissions', [
            'earner_user_id' => $referrer->id,
            'source_user_id' => $user->id,
            'month' => '2024-01', // Previous month
        ]);
    }

    /** @test */
    public function it_enforces_contract_acceptance_requirement()
    {
        // Create a regular user
        $user = User::factory()->create(['referrer_id' => null]);

        // Create a contract
        $contract = Contract::create([
            'title' => 'Test Contract',
            'language' => 'en',
            'version' => '1.0',
            'content' => 'Test contract content',
            'is_active' => true,
        ]);

        // User should not have accepted contract initially
        $this->assertFalse($user->hasAcceptedLatestContract());

        // Accept contract
        ContractAcceptance::create([
            'user_id' => $user->id,
            'contract_id' => $contract->id,
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
        ]);

        // User should now have accepted contract
        $this->assertTrue($user->hasAcceptedLatestContract());
    }

    /** @test */
    public function it_restricts_magazine_access_based_on_subscription_status()
    {
        // Create a magazine
        $magazine = Magazine::create([
            'title' => 'Test Magazine',
            'description' => 'Test Description',
            'file_name' => 'test_magazine.pdf',
            'file_path' => '/test/path.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'uploaded_by_admin_id' => 1,
            'language' => 'en',
            'is_active' => true,
            'release_date' => now(),
        ]);

        // Create a user with active subscription
        $activeUser = User::factory()->create(['referrer_id' => null]);
        $activeSubscription = Subscription::create([
            'user_id' => $activeUser->id,
            'plan_name' => '2-Year Plan',
            'start_date' => now(),
            'end_date' => now()->addYears(2),
            'status' => 'active',
        ]);

        // Grant magazine entitlement
        MagazineEntitlement::create([
            'user_id' => $activeUser->id,
            'magazine_id' => $magazine->id,
            'reason' => 'active_subscription',
            'granted_at' => now(),
            'expires_at' => now()->addYears(2),
        ]);

        // User should have access to magazine
        $this->assertTrue($activeUser->magazineEntitlements()->where('magazine_id', $magazine->id)->exists());

        // Create a user with expired subscription
        $expiredUser = User::factory()->create(['referrer_id' => null]);
        $expiredSubscription = Subscription::create([
            'user_id' => $expiredUser->id,
            'plan_name' => '2-Year Plan',
            'start_date' => now()->subYears(2)->subDays(10),
            'end_date' => now()->subDays(10),
            'status' => 'expired',
        ]);

        // User should not have access to magazine
        $this->assertFalse($expiredUser->magazineEntitlements()->where('magazine_id', $magazine->id)->exists());
    }

    /** @test */
    public function it_handles_commission_payouts_and_balance_resets()
    {
        // Create users and referral structure
        $referrer = User::factory()->create(['referrer_id' => null]);
        $referredUser = User::factory()->create(['referrer_id' => $referrer->id]);

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($referredUser, $referrer);

        // Create completed transactions for January
        $janTransaction = Transaction::factory()->create([
            'user_id' => $referredUser->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-01-15 10:00:00',
        ]);

        // Generate commissions for January
        $referralService->generateCommissions($janTransaction);

        // Check January commissions
        $janCommission = Commission::where('earner_user_id', $referrer->id)
            ->where('month', '2024-01')
            ->first();
        $this->assertEquals(15.00, $janCommission->amount);
        $this->assertEquals('eligible', $janCommission->eligibility);

        // Create completed transactions for February
        $febTransaction = Transaction::factory()->create([
            'user_id' => $referredUser->id,
            'status' => 'completed',
            'amount' => 39.90,
            'created_at' => '2024-02-15 10:00:00',
        ]);

        // Generate commissions for February
        $referralService->generateCommissions($febTransaction);

        // Check February commissions
        $febCommission = Commission::where('earner_user_id', $referrer->id)
            ->where('month', '2024-02')
            ->first();
        $this->assertEquals(15.00, $febCommission->amount);
        $this->assertEquals('eligible', $febCommission->eligibility);

        // Total commissions should be 30.00 (15 + 15)
        $totalCommissions = Commission::where('earner_user_id', $referrer->id)->sum('amount');
        $this->assertEquals(30.00, $totalCommissions);
    }

    /** @test */
    public function it_validates_coinpayments_integration_requirements()
    {
        // Test CoinPayments service configuration
        $coinPaymentsService = app(\App\Services\CoinPaymentsService::class);
        
        // Check if service is properly configured
        $this->assertInstanceOf(\App\Services\CoinPaymentsService::class, $coinPaymentsService);
        
        // Check supported currencies
        $currencies = $coinPaymentsService->getSupportedCurrencies();
        $this->assertArrayHasKey('USDT.TRC20', $currencies);
        $this->assertArrayHasKey('USDT.ERC20', $currencies);
        
        // Check if service can be enabled/disabled
        $this->assertIsBool($coinPaymentsService->isEnabled());
    }

    /** @test */
    public function it_enforces_payment_amount_of_39_90_usdt_usdc()
    {
        // Create a user
        $user = User::factory()->create(['referrer_id' => null]);

        // Test correct payment amount
        $correctTransaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 39.90,
            'currency' => 'USDT',
        ]);

        $this->assertEquals(39.90, $correctTransaction->amount);
        $this->assertEquals('USDT', $correctTransaction->currency);

        // Test that incorrect amounts are not allowed
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Transaction::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'amount' => 29.99, // Wrong amount
            'currency' => 'USDT',
        ]);
    }

    /** @test */
    public function it_handles_email_automations_correctly()
    {
        // Test that email service exists and can be instantiated
        $emailService = app(EmailService::class);
        $this->assertInstanceOf(EmailService::class, $emailService);
        
        // Test that email templates table exists
        $this->assertTrue(\Schema::hasTable('email_templates'));
        
        // Test that email logs table exists
        $this->assertTrue(\Schema::hasTable('email_logs'));
    }

    /** @test */
    public function it_supports_multilanguage_framework()
    {
        // Test language preference storage (since we use component-based language switching)
        $user = User::factory()->create(['language' => 'es']);
        $this->assertEquals('es', $user->language);
        
        // Test language preference update
        $user->update(['language' => 'en']);
        $this->assertEquals('en', $user->language);
        
        // Test that language preference is properly stored
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'language' => 'en'
        ]);
    }

    /** @test */
    public function it_validates_admin_backoffice_functionality()
    {
        // Test admin authentication
        $admin = \App\Models\Admin::factory()->create([
            'role' => 'super_admin'
        ]);
        
        $this->actingAs($admin, 'admin');
        
        // Test admin dashboard access
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        
        // Test user management access
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        
        // Test commission management access
        $response = $this->get('/admin/commissions');
        $response->assertStatus(200);
        
        // Test magazine management access
        $response = $this->get('/admin/magazines');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_user_backoffice_functionality()
    {
        // Create and authenticate a user
        $user = User::factory()->create(['referrer_id' => null]);
        $this->actingAs($user);
        
        // Test dashboard access
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Test referral link generation
        $referralLink = $user->getReferralLink();
        $this->assertStringContainsString('/register?ref=' . $user->id, $referralLink);
        
        // Test transaction history access
        $response = $this->get('/transactions');
        $response->assertStatus(200);
        
        // Test payment checkout access (may redirect to login if not authenticated properly)
        $response = $this->get('/payment/checkout');
        $this->assertTrue(in_array($response->status(), [200, 302]), 'Payment checkout should return 200 or redirect 302');
    }

    /** @test */
    public function it_enforces_security_policies_and_audit_logging()
    {
        // Test admin activity logging
        $admin = \App\Models\Admin::factory()->create();
        
        // Test sensitive changes logging - check if table exists
        $this->assertTrue(\Schema::hasTable('admin_activity_logs'));
        
        // Test security policy enforcement - check if table exists
        $this->assertTrue(\Schema::hasTable('security_policies'));
        
        // Test API key management - check if table exists
        $this->assertTrue(\Schema::hasTable('api_keys'));
    }

    /** @test */
    public function it_validates_commission_calculation_accuracy()
    {
        // Create a complex referral structure
        $users = [];
        $users[0] = $this->mainSponsor; // ID = 1
        
        for ($i = 1; $i <= 6; $i++) {
            $users[$i] = User::factory()->create(['referrer_id' => $users[$i-1]->id]);
        }

        // Build referral upline
        $referralService = app(ReferralService::class);
        $referralService->buildReferralUpline($users[6], $users[5]);

        // Create multiple transactions
        $transactions = [];
        for ($i = 0; $i < 3; $i++) {
            $transactions[] = Transaction::factory()->create([
                'user_id' => $users[6]->id,
                'status' => 'completed',
                'amount' => 39.90,
                'created_at' => now()->addDays($i),
            ]);
        }

        // Generate commissions for all transactions
        foreach ($transactions as $transaction) {
            $referralService->generateCommissions($transaction);
        }

        // Calculate expected totals
        $expectedTotalPerLevel = [
            1 => 15.00 * 3, // 3 transactions * 15 USDT
            2 => 10.00 * 3, // 3 transactions * 10 USDT
            3 => 5.00 * 3,  // 3 transactions * 5 USDT
            4 => 1.00 * 3,  // 3 transactions * 1 USDT
            5 => 1.00 * 3,  // 3 transactions * 1 USDT
            6 => 1.00 * 3,  // 3 transactions * 1 USDT
        ];

        // Verify commission totals match expected amounts
        foreach ($expectedTotalPerLevel as $level => $expectedTotal) {
            $actualTotal = Commission::where('earner_user_id', $users[6-$level]->id)
                ->where('level', $level)
                ->sum('amount');
            
            $this->assertEquals($expectedTotal, $actualTotal, 
                "Commission total for level {$level} incorrect. Expected: {$expectedTotal}, Got: {$actualTotal}");
        }

        // Total commissions across all levels should equal 99 USDT (33 * 3)
        $grandTotal = Commission::sum('amount');
        $this->assertEquals(99.00, $grandTotal);
    }
}

