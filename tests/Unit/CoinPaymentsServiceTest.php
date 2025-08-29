<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CoinPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CoinPaymentsServiceTest extends TestCase
{
    protected CoinPaymentsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock configuration
        Config::set('services.coinpayments.merchant_id', 'TESTMERCHANT123');
        Config::set('services.coinpayments.ipn_secret', 'test_secret_123');
        Config::set('services.coinpayments.currency2', 'USDT.TRC20');
        Config::set('services.coinpayments.ipn_url', 'https://example.com/ipn');
        
        $this->service = new CoinPaymentsService();
    }

    /** @test */
    public function it_maps_status_correctly()
    {
        $this->assertEquals('completed', CoinPaymentsService::mapStatus(100));
        $this->assertEquals('completed', CoinPaymentsService::mapStatus(2));
        $this->assertEquals('processing', CoinPaymentsService::mapStatus(1));
        $this->assertEquals('pending', CoinPaymentsService::mapStatus(0));
        $this->assertEquals('failed', CoinPaymentsService::mapStatus(-1));
        $this->assertEquals('failed', CoinPaymentsService::mapStatus(-2));
    }

    /** @test */
    public function it_verifies_ipn_with_valid_hmac()
    {
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '100',
            'txn_id' => 'TEST123',
            'amount1' => '10.00',
            'amount2' => '10.00',
            'currency1' => 'USD',
            'currency2' => 'USDT.TRC20',
        ];

        $rawBody = http_build_query($payload);
        $hmac = hash_hmac('sha512', $rawBody, 'test_secret_123');

        $request = Request::create('/ipn', 'POST', $payload, [], [], [], $rawBody);
        $request->headers->set('HMAC', $hmac);

        $result = $this->service->verifyIPN($request);

        $this->assertTrue($result['ok']);
        $this->assertNull($result['error']);
        $this->assertEquals($payload, $result['payload']);
    }

    /** @test */
    public function it_rejects_ipn_with_invalid_hmac()
    {
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '100',
            'txn_id' => 'TEST123',
        ];

        $rawBody = http_build_query($payload);
        $invalidHmac = 'invalid_hmac';

        $request = Request::create('/ipn', 'POST', $payload, [], [], [], $rawBody);
        $request->headers->set('HMAC', $invalidHmac);

        $result = $this->service->verifyIPN($request);

        $this->assertFalse($result['ok']);
        $this->assertEquals('Bad HMAC', $result['error']);
    }

    /** @test */
    public function it_rejects_ipn_with_invalid_merchant()
    {
        $payload = [
            'merchant' => 'WRONGMERCHANT',
            'status' => '100',
            'txn_id' => 'TEST123',
        ];

        $rawBody = http_build_query($payload);
        $hmac = hash_hmac('sha512', $rawBody, 'test_secret_123');

        $request = Request::create('/ipn', 'POST', $payload, [], [], [], $rawBody);
        $request->headers->set('HMAC', $hmac);

        $result = $this->service->verifyIPN($request);

        $this->assertFalse($result['ok']);
        $this->assertEquals('Invalid merchant', $result['error']);
    }

    /** @test */
    public function it_rejects_ipn_without_hmac_header()
    {
        $payload = [
            'merchant' => 'TESTMERCHANT123',
            'status' => '100',
            'txn_id' => 'TEST123',
        ];

        $request = Request::create('/ipn', 'POST', $payload);

        $result = $this->service->verifyIPN($request);

        $this->assertFalse($result['ok']);
        $this->assertEquals('Missing HMAC header', $result['error']);
    }

    /** @test */
    public function it_returns_supported_currencies()
    {
        $currencies = $this->service->getSupportedCurrencies();

        $this->assertArrayHasKey('USDT.TRC20', $currencies);
        $this->assertArrayHasKey('USDT.ERC20', $currencies);
        $this->assertArrayHasKey('BTC', $currencies);
        $this->assertArrayHasKey('ETH', $currencies);
    }

    /** @test */
    public function it_checks_if_enabled()
    {
        Config::set('services.coinpayments.enabled', true);
        $this->assertTrue($this->service->isEnabled());

        Config::set('services.coinpayments.enabled', false);
        $this->assertFalse($this->service->isEnabled());
    }
}
