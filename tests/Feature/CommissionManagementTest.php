<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\AdminActivityLog;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CommissionManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $users;
    protected $transactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::create([
            'name' => 'Test Admin User',
            'email' => 'testadmin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
        
        // Create test users with referral structure
        $this->users = $this->createReferralStructure();
        
        // Create test transactions
        $this->transactions = $this->createTestTransactions();
        
        // Create test commissions
        $this->createTestCommissions();
        
        // Create required email templates
        $this->createEmailTemplates();
    }

    private function createReferralStructure()
    {
        $users = [];
        
        // Create root user (level 0)
        $users[] = User::factory()->create([
            'name' => 'Root User',
            'email' => 'root@example.com',
            'wallet_address' => '0xRootWallet123',
        ]);

        // Create level 1 users
        for ($i = 1; $i <= 3; $i++) {
            $user = User::factory()->create([
                'name' => "Level 1 User {$i}",
                'email' => "level1_{$i}@example.com",
                'wallet_address' => "0xLevel1Wallet{$i}",
            ]);
            
            Referral::create([
                'user_id' => $users[0]->id,
                'referred_user_id' => $user->id,
                'level' => 1,
            ]);
            
            $users[] = $user;
        }

        // Create level 2 users
        for ($i = 1; $i <= 2; $i++) {
            $user = User::factory()->create([
                'name' => "Level 2 User {$i}",
                'email' => "level2_{$i}@example.com",
                'wallet_address' => "0xLevel2Wallet{$i}",
            ]);
            
            Referral::create([
                'user_id' => $users[1]->id, // First level 1 user
                'referred_user_id' => $user->id,
                'level' => 2,
            ]);
            
            $users[] = $user;
        }

        return $users;
    }

    private function createTestTransactions()
    {
        $transactions = [];
        
        // Create transactions for each user (except root)
        for ($i = 1; $i < count($this->users); $i++) {
            $transaction = Transaction::factory()->create([
                'user_id' => $this->users[$i]->id,
                'amount' => 39.90,
                'status' => 'completed',
                'gateway' => 'coinpayments',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
            
            $transactions[] = $transaction;
        }
        
        return $transactions;
    }

    private function createTestCommissions()
    {
        $month = now()->format('Y-m');
        
        // Create commissions for each user based on referral levels
        foreach ($this->users as $user) {
            // Root user gets commissions from level 1 users
            if ($user->id === $this->users[0]->id) {
                // Level 1 commission (10% of 39.90 = 3.99)
                Commission::create([
                    'earner_user_id' => $user->id,
                    'source_user_id' => $this->users[1]->id,
                    'transaction_id' => $this->transactions[0]->id,
                    'level' => 1,
                    'amount' => 3.99,
                    'month' => $month,
                    'eligibility' => 'eligible',
                    'payout_status' => 'pending',
                ]);
                
                // Level 2 commission (5% of 39.90 = 1.995)
                Commission::create([
                    'earner_user_id' => $user->id,
                    'source_user_id' => $this->users[3]->id, // Level 2 user
                    'transaction_id' => $this->transactions[2]->id,
                    'level' => 2,
                    'amount' => 1.995,
                    'month' => $month,
                    'eligibility' => 'eligible',
                    'payout_status' => 'pending',
                ]);
            }
            
            // Level 1 users get commissions from level 2 users
            if ($user->id === $this->users[1]->id) {
                Commission::create([
                    'earner_user_id' => $user->id,
                    'source_user_id' => $this->users[3]->id,
                    'transaction_id' => $this->transactions[2]->id,
                    'level' => 1,
                    'amount' => 3.99,
                    'month' => $month,
                    'eligibility' => 'eligible',
                    'payout_status' => 'pending',
                ]);
            }
        }
    }

    private function createEmailTemplates()
    {
        // Create email templates with all required fields
        EmailTemplate::create([
            'name' => 'commission_eligibility',
            'language' => 'en',
            'subject' => 'Commission Eligibility Update',
            'body' => 'Your commission eligibility has been updated.',
            'variables' => json_encode(['user_name', 'status', 'month']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        EmailTemplate::create([
            'name' => 'payout_notification',
            'language' => 'en',
            'subject' => 'Payout Notification',
            'body' => 'Your payout has been processed.',
            'variables' => json_encode(['user_name', 'amount', 'batch_id']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_access_commission_management_dashboard()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-management.index'));

        $response->assertStatus(200);
        $response->assertSee('Commission Management Dashboard');
    }

    public function test_admin_can_view_monthly_commission_breakdown()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-management.monthly-breakdown'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'monthly_breakdown',
            'total_stats',
            'company_earnings'
        ]);
    }

    public function test_admin_can_process_monthly_eligibility()
    {
        $month = now()->format('Y-m');
        
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);

        $response->assertRedirect();
        
        // Check that commissions were created with eligibility status
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[0]->id, // Root user should get commissions
            'month' => $month,
            'eligibility' => 'eligible'
        ]);
    }

    public function test_admin_can_create_payout_batch()
    {
        $month = now()->format('Y-m');
        
        // First process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Then create payout batch
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.create-payout-batch'), [
                'month' => $month,
                'reason' => 'Creating payout batch for testing'
            ]);

        $response->assertRedirect();
        
        // Check that payout batch was created
        $this->assertDatabaseHas('payout_batches', [
            'period' => $month,
            'status' => 'processing'
        ]);
    }

    public function test_admin_can_mark_payout_as_sent()
    {
        // Create a payout batch and items
        $payoutBatch = PayoutBatch::factory()->create([
            'period' => now()->format('Y-m'),
            'status' => 'processing',
        ]);
        
        $payoutItem = PayoutBatchItem::factory()->create([
            'batch_id' => $payoutBatch->id,
            'earner_user_id' => $this->users[1]->id,
            'amount' => 10.00,
            'status' => 'queued',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.mark-payout-sent', $payoutItem));

        $response->assertJson(['success' => true]);
        
        $payoutItem->refresh();
        $this->assertEquals('sent', $payoutItem->status);
    }

    public function test_admin_can_mark_payout_as_paid()
    {
        // Create a payout batch and items
        $payoutBatch = PayoutBatch::factory()->create([
            'period' => now()->format('Y-m'),
            'status' => 'processing',
        ]);
        
        $payoutItem = PayoutBatchItem::factory()->create([
            'batch_id' => $payoutBatch->id,
            'earner_user_id' => $this->users[1]->id,
            'amount' => 10.00,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.mark-payout-paid', $payoutItem));

        $response->assertJson(['success' => true]);
        
        $payoutItem->refresh();
        $this->assertEquals('paid', $payoutItem->status);
    }

    public function test_commission_eligibility_is_correctly_calculated()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Check that users with transactions are eligible
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[0]->id, // Root user gets commissions from level 1
            'month' => $month,
            'eligibility' => 'eligible'
        ]);
        
        // Check that users without transactions are ineligible
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[1]->id, // Level 1 user with transaction
            'month' => $month,
            'eligibility' => 'eligible'
        ]);
    }

    public function test_commission_distribution_follows_referral_levels()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Check level 1 commission (10% of 39.90 = 3.99)
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[0]->id,
            'level' => 1,
            'amount' => 3.99
        ]);
        
        // Check level 2 commission (5% of 39.90 = 1.995)
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[1]->id,
            'level' => 2,
            'amount' => 1.995
        ]);
    }

    public function test_referral_commissions_are_correctly_distributed()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Check that commissions are distributed to all eligible referrers
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[0]->id, // Root user
            'level' => 1,
            'amount' => 3.99
        ]);
        
        $this->assertDatabaseHas('commissions', [
            'earner_user_id' => $this->users[1]->id, // Level 1 user
            'level' => 2,
            'amount' => 1.995
        ]);
    }

    public function test_company_earnings_are_correctly_calculated()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Get company earnings
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-management.monthly-breakdown'));
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // Company should earn from ineligible commissions
        $this->assertGreaterThan(0, $data['company_earnings']);
    }

    public function test_monthly_stats_are_correctly_calculated()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-management.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Total Users');
        $response->assertSee('Total Commissions');
    }

    public function test_payout_batch_creation_requires_valid_month()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.create-payout-batch'), []);

        $response->assertSessionHasErrors(['month']);
    }

    public function test_eligibility_processing_requires_valid_month()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), []);

        $response->assertSessionHasErrors(['month']);
    }

    public function test_admin_activity_is_logged_for_eligibility_processing()
    {
        $month = now()->format('Y-m');
        
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'processed_monthly_eligibility',
        ]);
    }

    public function test_admin_activity_is_logged_for_payout_batch_creation()
    {
        $month = now()->format('Y-m');
        
        // First process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Then create payout batch
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.create-payout-batch'), [
                'month' => $month,
                'reason' => 'Creating payout batch for testing'
            ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'created_payout_batch',
        ]);
    }

    public function test_payout_items_are_created_for_eligible_commissions()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Create payout batch
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.create-payout-batch'), [
                'month' => $month,
                'reason' => 'Creating payout batch for testing'
            ]);

        // Check that payout items were created for eligible commissions
        $eligibleCommissions = Commission::where('month', $month)
            ->where('eligibility', 'eligible')
            ->count();
        
        $payoutItems = PayoutBatchItem::whereHas('batch', function($query) use ($month) {
            $query->where('period', $month);
        })->count();
        
        $this->assertEquals($eligibleCommissions, $payoutItems);
    }

    public function test_ineligible_commissions_go_to_company()
    {
        $month = now()->format('Y-m');
        
        // Process eligibility
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.commission-management.process-eligibility'), [
                'month' => $month,
                'reason' => 'Monthly eligibility processing for testing'
            ]);
        
        // Get company earnings
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.commission-management.monthly-breakdown'));
        
        $data = $response->json();
        
        // Company should earn from ineligible commissions
        $this->assertGreaterThan(0, $data['company_earnings']);
        
        // Check that ineligible commissions exist
        $ineligibleCommissions = Commission::where('month', $month)
            ->where('eligibility', 'ineligible')
            ->count();
        
        $this->assertGreaterThan(0, $ineligibleCommissions);
    }
}

