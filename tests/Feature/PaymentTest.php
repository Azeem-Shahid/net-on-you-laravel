<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create API key setting
        Setting::create([
            'key' => 'nowpayments_api_key',
            'value' => 'test_api_key_123',
            'type' => 'string'
        ]);
    }

    /** @test */
    public function user_can_view_payment_checkout()
    {
        $response = $this->actingAs($this->user)
            ->get(route('payment.checkout'));

        $response->assertStatus(200);
        $response->assertViewIs('payment.checkout');
        $response->assertSee('Choose Your Plan');
        $response->assertSee('Monthly Plan');
        $response->assertSee('Annual Plan');
    }

    /** @test */
    public function user_can_initiate_crypto_payment()
    {
        $response = $this->actingAs($this->user)
            ->post(route('payment.initiate'), [
                'plan' => 'monthly',
                'payment_method' => 'crypto'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 29.99,
            'currency' => 'USDT',
            'gateway' => 'nowpayments',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function user_can_initiate_manual_payment()
    {
        $response = $this->actingAs($this->user)
            ->post(route('payment.initiate'), [
                'plan' => 'annual',
                'payment_method' => 'manual'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 299.99,
            'currency' => 'USDT',
            'gateway' => 'manual',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function user_cannot_initiate_payment_without_authentication()
    {
        $response = $this->post(route('payment.initiate'), [
            'plan' => 'monthly',
            'payment_method' => 'crypto'
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_cannot_initiate_payment_with_invalid_plan()
    {
        $response = $this->actingAs($this->user)
            ->post(route('payment.initiate'), [
                'plan' => 'invalid_plan',
                'payment_method' => 'crypto'
            ]);

        $response->assertSessionHasErrors('plan');
    }

    /** @test */
    public function user_cannot_initiate_payment_with_invalid_method()
    {
        $response = $this->actingAs($this->user)
            ->post(route('payment.initiate'), [
                'plan' => 'monthly',
                'payment_method' => 'invalid_method'
            ]);

        $response->assertSessionHasErrors('payment_method');
    }

    /** @test */
    public function user_can_view_payment_status()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('payment.status', $transaction));

        $response->assertStatus(200);
        $response->assertViewIs('payment.status');
        $response->assertSee('Payment Status');
    }

    /** @test */
    public function user_cannot_view_another_users_payment_status()
    {
        $otherUser = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('payment.status', $transaction));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_upload_manual_payment_proof()
    {
        Storage::fake('public');
        
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'gateway' => 'manual',
            'status' => 'pending',
            'notes' => 'Initial transaction notes'
        ]);

        $file = UploadedFile::fake()->create('payment_proof.pdf', 100);

        $response = $this->actingAs($this->user)
            ->post(route('payment.upload-proof', $transaction), [
                'proof_file' => $file,
                'notes' => 'Payment proof uploaded'
            ]);

        $response->assertRedirect();
        

        
        // Refresh the transaction to get the latest data
        $transaction->refresh();
        
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'notes' => 'Payment proof uploaded'
        ]);
    }

    /** @test */
    public function user_can_view_payment_history()
    {
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('payment.history'));

        $response->assertStatus(200);
        $response->assertViewIs('payment.history');
        $response->assertSee('Payment History');
    }

    /** @test */
    public function subscription_is_created_when_payment_completed()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'meta' => [
                'plan' => 'monthly',
                'duration_days' => 30,
                'payment_id' => 'test_123'
            ]
        ]);

        $this->post(route('payment.webhook'), [
            'payment_id' => 'test_123',
            'payment_status' => 'finished',
            'pay_address' => 'test_address'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_name' => 'monthly',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function subscription_extends_existing_subscription()
    {
        // Create existing subscription
        $existingSubscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_name' => 'monthly',
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(15),
            'status' => 'active'
        ]);

        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'meta' => [
                'plan' => 'annual',
                'duration_days' => 365,
                'payment_id' => 'test_123'
            ]
        ]);

        $this->post(route('payment.webhook'), [
            'payment_id' => 'test_123',
            'payment_status' => 'finished',
            'pay_address' => 'test_address'
        ]);

        // Check that old subscription is cancelled
        $this->assertDatabaseHas('subscriptions', [
            'id' => $existingSubscription->id,
            'status' => 'cancelled'
        ]);

        // Check that new subscription is created
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_name' => 'annual',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function webhook_creates_payment_notification()
    {
        $this->post(route('payment.webhook'), [
            'payment_id' => 'test_123',
            'payment_status' => 'waiting'
        ]);

        $this->assertDatabaseHas('payment_notifications', [
            'payload->payment_id' => 'test_123',
            'processed' => false
        ]);
    }

    /** @test */
    public function user_cannot_access_payment_routes_without_verification()
    {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->actingAs($unverifiedUser)
            ->get(route('payment.checkout'));

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function payment_webhook_handles_invalid_transaction()
    {
        $response = $this->post(route('payment.webhook'), [
            'payment_id' => 'invalid_id',
            'payment_status' => 'finished'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function subscription_status_methods_work_correctly()
    {
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_name' => 'monthly',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active'
        ]);

        $this->assertTrue($subscription->isActive());
        $this->assertFalse($subscription->isExpired());
        $this->assertFalse($subscription->expiresSoon());
        $this->assertGreaterThanOrEqual(29, $subscription->daysUntilExpiry());
        $this->assertLessThanOrEqual(31, $subscription->daysUntilExpiry());
    }

    /** @test */
    public function subscription_can_be_extended()
    {
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_name' => 'monthly',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active'
        ]);

        $originalEndDate = $subscription->end_date;
        $subscription->extend(7);

        $this->assertEquals($originalEndDate->addDays(7)->format('Y-m-d'), $subscription->fresh()->end_date->format('Y-m-d'));
    }

    /** @test */
    public function subscription_can_be_cancelled()
    {
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_name' => 'monthly',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active'
        ]);

        $subscription->cancel();

        $this->assertEquals('cancelled', $subscription->fresh()->status);
    }
}
