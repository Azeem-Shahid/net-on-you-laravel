<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class CoinPaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Mock CoinPayments configuration
        Config::set('services.coinpayments.enabled', true);
        Config::set('services.coinpayments.merchant_id', 'TESTMERCHANT123');
        Config::set('services.coinpayments.public_key', 'test_public_key');
        Config::set('services.coinpayments.private_key', 'test_private_key');
        Config::set('services.coinpayments.ipn_secret', 'test_secret_123');
        Config::set('services.coinpayments.currency2', 'USDT.TRC20');
        Config::set('services.coinpayments.ipn_url', 'https://example.com/ipn');
        
        // Clear config cache to ensure settings are loaded
        $this->app['config']->set('services.coinpayments.enabled', true);
    }

    /** @test */
    public function user_can_initiate_coinpayments_payment()
    {
        $this->actingAs($this->user);

        $response = $this->post('/payment/initiate', [
            'plan' => 'monthly',
            'payment_method' => 'crypto'
        ]);

        $response->assertStatus(302); // Redirect to CoinPayments
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'gateway' => 'coinpayments',
            'status' => 'pending',
            'currency' => 'USD'
        ]);
    }

    /** @test */
    public function coinpayments_ipn_updates_transaction_status()
    {
        // Create a test transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'gateway' => 'coinpayments',
            'txn_id' => 'TESTTXN123',
            'status' => 'pending',
            'currency' => 'USD',
            'amount' => 29.99
        ]);

        // Ensure the transaction exists in the database
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'txn_id' => 'TESTTXN123'
        ]);

        // Simulate IPN from CoinPayments
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '100', // Completed
            'txn_id' => 'TESTTXN123',
            'amount1' => '29.99',
            'amount2' => '29.99',
            'currency1' => 'USD',
            'currency2' => 'USDT.TRC20',
            'confirms' => '3',
            'invoice' => 'INV-TEST-001'
        ];

        $rawBody = http_build_query($payload);
        $hmac = hash_hmac('sha512', $rawBody, 'test_secret_123');

        $response = $this->call('POST', '/payments/coinpayments/ipn', [], [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'HTTP_HMAC' => $hmac
        ], $rawBody);

        $response->assertStatus(200);
        $response->assertSee('OK');

        // Check that transaction was updated
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
            'confirmations' => 3,
            'received_amount' => 29.99
        ]);
    }

    /** @test */
    public function coinpayments_ipn_rejects_invalid_hmac()
    {
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '100',
            'txn_id' => 'TESTTXN123',
        ];

        $response = $this->post('/payments/coinpayments/ipn', $payload, [
            'HMAC' => 'invalid_hmac'
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function coinpayments_ipn_rejects_invalid_merchant()
    {
        $payload = [
            'merchant' => 'WRONGMERCHANT',
            'status' => '100',
            'txn_id' => 'TESTTXN123',
        ];

        $rawBody = http_build_query($payload);
        $hmac = hash_hmac('sha512', $rawBody, 'test_secret_123');

        $response = $this->post('/payments/coinpayments/ipn', $payload, [
            'HMAC' => $hmac
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function coinpayments_ipn_handles_different_statuses()
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'gateway' => 'coinpayments',
            'txn_id' => 'TESTTXN456',
            'status' => 'pending',
            'currency' => 'USD',
            'amount' => 29.99
        ]);

        // Ensure the transaction exists in the database
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'txn_id' => 'TESTTXN456'
        ]);

        // Test processing status
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '1', // Processing
            'txn_id' => 'TESTTXN456',
            'amount1' => '29.99',
            'amount2' => '29.99',
            'currency1' => 'USD',
            'currency2' => 'USDT.TRC20',
            'confirms' => '1'
        ];

        $rawBody = http_build_query($payload);
        $hmac = hash_hmac('sha512', $rawBody, 'test_secret_123');

        $response = $this->call('POST', '/payments/coinpayments/ipn', [], [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'HTTP_HMAC' => $hmac
        ], $rawBody);

        $response->assertStatus(200);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'processing'
        ]);
    }

    /** @test */
    public function coinpayments_is_disabled_when_not_configured()
    {
        Config::set('services.coinpayments.enabled', false);
        
        $this->actingAs($this->user);

        $response = $this->post('/payment/initiate', [
            'plan' => 'monthly',
            'payment_method' => 'crypto'
        ]);

        $response->assertSessionHas('error', 'Crypto payments are temporarily unavailable.');
    }
}
