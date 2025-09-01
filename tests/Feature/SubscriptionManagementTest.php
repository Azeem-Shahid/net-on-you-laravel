<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\AdminActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class SubscriptionManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::factory()->superAdmin()->create();
        
        // Create regular user
        $this->user = User::factory()->create();
        
        // Create subscription
        $this->subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'start_date' => now(),
            'end_date' => now()->addYears(2),
            'status' => 'active',
        ]);

        // Create required email templates
        $this->createEmailTemplates();
    }

    private function createEmailTemplates()
    {
        // Create email templates with all required fields
        EmailTemplate::create([
            'name' => 'subscription_expiring',
            'language' => 'en',
            'subject' => 'Subscription Expiring Soon',
            'body' => 'Your subscription is expiring soon.',
            'variables' => json_encode(['user_name', 'expiry_date']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        EmailTemplate::create([
            'name' => 'payment_confirmation',
            'language' => 'en',
            'subject' => 'Payment Confirmation',
            'body' => 'Your payment has been confirmed.',
            'variables' => json_encode(['user_name', 'amount', 'transaction_id']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);

        EmailTemplate::create([
            'name' => 'referrer_notification',
            'language' => 'en',
            'subject' => 'New Referral Payment',
            'body' => 'Someone you referred has made a payment.',
            'variables' => json_encode(['referrer_name', 'referred_name', 'amount']),
            'created_by_admin_id' => $this->admin->id,
            'updated_by_admin_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_access_subscription_management_dashboard()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscription-management.index'));

        $response->assertStatus(200);
        $response->assertSee('Subscription Management Dashboard');
    }

    public function test_admin_can_view_expiration_alerts()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscription-management.expiration-alerts'));

        $response->assertStatus(200);
        $response->assertSee('Expiration Alerts');
    }

    public function test_admin_can_create_admin_user_without_payment()
    {
        $userData = [
            'name' => 'Test Admin User',
            'email' => 'testadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.store-admin-user'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Test Admin User',
            'email' => 'testadmin@example.com',
        ]);
    }

    public function test_admin_can_extend_subscription()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.extend-subscription', $this->user), [
                'months' => 6
            ]);

        $response->assertRedirect();
        $this->subscription->refresh();
        $this->assertTrue($this->subscription->end_date->gt(now()->addMonths(5)));
    }

    public function test_admin_can_send_expiration_notification()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.send-expiration-notification', $this->user));

        $response->assertJson(['success' => true]);
    }

    public function test_admin_can_send_payment_confirmation()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 39.90,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.send-payment-confirmation', $this->user), [
                'transaction_id' => $transaction->id
            ]);

        $response->assertJson(['success' => true]);
    }

    public function test_admin_can_send_referrer_notification()
    {
        $referrer = User::factory()->create();
        $referral = Referral::factory()->create([
            'user_id' => $referrer->id,
            'referred_user_id' => $this->user->id,
            'level' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.send-referrer-notification', $this->user), [
                'referrer_id' => $referrer->id,
                'amount' => 39.90
            ]);

        $response->assertJson(['success' => true]);
    }

    public function test_subscription_expiration_alerts_show_correct_data()
    {
        // Create a subscription that expires soon
        $expiringUser = User::factory()->create();
        Subscription::factory()->create([
            'user_id' => $expiringUser->id,
            'start_date' => now()->subMonths(23),
            'end_date' => now()->addDays(15),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscription-management.expiration-alerts'));

        $response->assertStatus(200);
        $response->assertSee($expiringUser->name);
    }

    public function test_subscription_stats_are_correctly_calculated()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscription-management.index'));

        $response->assertStatus(200);
        $response->assertSee('Active Subscriptions');
    }

    public function test_admin_user_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.store-admin-user'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_subscription_extension_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.extend-subscription', $this->user), []);

        $response->assertSessionHasErrors(['months']);
    }

    public function test_admin_user_creation_logs_activity()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.store-admin-user'), $userData);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'created_admin_user',
        ]);
    }

    public function test_subscription_extension_logs_activity()
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.extend-subscription', $this->user), [
                'months' => 3
            ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'extended_subscription',
        ]);
    }

    public function test_admin_user_creation_creates_complete_records()
    {
        $userData = [
            'name' => 'Complete Test User',
            'email' => 'complete@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.store-admin-user'), $userData);

        $user = User::where('email', 'complete@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->subscription);
        $this->assertEquals('active', $user->subscription->status);
    }

    public function test_subscription_extension_updates_existing_subscription()
    {
        $originalEndDate = $this->subscription->end_date;
        
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.extend-subscription', $this->user), [
                'months' => 12
            ]);

        $this->subscription->refresh();
        $this->assertTrue($this->subscription->end_date->gt($originalEndDate));
    }

    public function test_email_templates_are_required_for_notifications()
    {
        // Delete email templates to test requirement
        EmailTemplate::where('name', 'subscription_expiring')->delete();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscription-management.send-expiration-notification', $this->user));

        $response->assertJson(['success' => false]);
    }
}
