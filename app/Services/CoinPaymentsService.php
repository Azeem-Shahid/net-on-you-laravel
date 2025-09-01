<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CoinPaymentsService
{
    protected ?string $merchantId;
    protected ?string $publicKey;
    protected ?string $privateKey;
    protected ?string $ipnSecret;
    protected string $currency2;
    protected ?string $ipnUrl;
    protected bool $sandbox;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.coinpayments.merchant_id');
        $this->publicKey = config('services.coinpayments.public_key');
        $this->privateKey = config('services.coinpayments.private_key');
        $this->ipnSecret = config('services.coinpayments.ipn_secret');
        $this->currency2 = config('services.coinpayments.currency2', 'USDT.TRC20');
        $this->ipnUrl = config('services.coinpayments.ipn_url');
        $this->sandbox = config('services.coinpayments.sandbox', false);
        $this->baseUrl = $this->sandbox ? 'https://www.coinpayments.net' : 'https://www.coinpayments.net';
    }

    /**
     * Create a new transaction with CoinPayments
     */
    public function createTransaction(float $amountUSD = null, string $currency1 = 'USD', ?string $buyerEmail = null, array $meta = []): array
    {
        // Use configured subscription price if no amount provided
        if ($amountUSD === null) {
            $amountUSD = (float) config('services.coinpayments.subscription_price', 39.90);
        }

        $params = [
            'cmd' => 'create_transaction',
            'version' => '1',
            'key' => $this->publicKey,
            'amount' => $amountUSD,
            'currency1' => $currency1,          // price currency
            'currency2' => $this->currency2,    // coin to pay with e.g. USDT.TRC20
            'buyer_email' => $buyerEmail,
            'item_name' => $meta['item_name'] ?? 'Subscription',
            'invoice' => $meta['invoice'] ?? str()->uuid()->toString(),
            'ipn_url' => $this->ipnUrl,
            'success_url' => route('dashboard'),
            'cancel_url' => route('payment.checkout'),
        ];

        // Add custom fields if provided
        if (!empty($meta['custom'])) {
            $params['custom'] = $meta['custom'];
        }

        // Sign the request
        $params['signature'] = $this->generateSignature($params);

        try {
            $response = Http::asForm()->post($this->baseUrl . '/api.php', $params);
            
            if (!$response->successful()) {
                throw new \RuntimeException('CoinPayments API error: HTTP ' . $response->status());
            }

            $result = $response->json();
            
            if (($result['error'] ?? '') !== 'ok') {
                throw new \RuntimeException('CoinPayments error: ' . ($result['error'] ?? 'unknown'));
            }

            $data = $result['result'];
            return [
                'txn_id' => $data['txn_id'] ?? null,
                'amount' => $data['amount'] ?? null,
                'address' => $data['address'] ?? null,
                'qrcode_url' => $data['qrcode_url'] ?? null,
                'status_url' => $data['status_url'] ?? null,
                'checkout_url' => $data['checkout_url'] ?? ($data['status_url'] ?? null),
                'raw' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('CoinPayments create transaction error: ' . $e->getMessage(), [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify IPN request from CoinPayments
     */
    public function verifyIPN(Request $request): array
    {
        try {
            // 1) Must include HMAC header
            $hmacHeader = $request->header('HMAC') ?? $request->header('Hmac') ?? '';
            if (empty($hmacHeader)) {
                Log::warning('CoinPayments IPN: Missing HMAC header');
                return ['ok' => false, 'error' => 'Missing HMAC header', 'payload' => []];
            }

            // 2) Merchant check
            $merchant = $request->input('merchant');
            if ($merchant !== $this->merchantId) {
                Log::warning('CoinPayments IPN: Invalid merchant', ['received' => $merchant, 'expected' => $this->merchantId]);
                return ['ok' => false, 'error' => 'Invalid merchant', 'payload' => []];
            }

            // 3) Compute HMAC over raw POST body
            $raw = $request->getContent(); // raw body
            $calc = hash_hmac('sha512', $raw, $this->ipnSecret);
            
            if (!hash_equals($calc, $hmacHeader)) {
                Log::warning('CoinPayments IPN: Bad HMAC signature', [
                    'received' => $hmacHeader,
                    'calculated' => $calc,
                    'body_length' => strlen($raw)
                ]);
                return ['ok' => false, 'error' => 'Bad HMAC', 'payload' => []];
            }

            $payload = $request->all();
            
            // Log successful verification
            Log::info('CoinPayments IPN verified successfully', [
                'txn_id' => $payload['txn_id'] ?? 'unknown',
                'status' => $payload['status'] ?? 'unknown',
                'merchant' => $merchant
            ]);
            
            return ['ok' => true, 'error' => null, 'payload' => $payload];
        } catch (\Throwable $e) {
            Log::error('CoinPayments IPN verify error: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['ok' => false, 'error' => 'Exception: ' . $e->getMessage(), 'payload' => []];
        }
    }

    /**
     * Map CoinPayments status to our internal status
     */
    public static function mapStatus(int $status): string
    {
        if ($status >= 100 || $status === 2) return 'completed';
        if ($status === 1) return 'processing';
        if ($status === 0) return 'pending';
        return 'failed';
    }

    /**
     * Generate signature for API requests
     */
    private function generateSignature(array $params): string
    {
        // Remove signature if exists
        unset($params['signature']);
        
        // Sort parameters alphabetically
        ksort($params);
        
        // Create query string
        $queryString = http_build_query($params);
        
        // Generate HMAC
        return hash_hmac('sha512', $queryString, $this->privateKey);
    }

    /**
     * Check if CoinPayments is enabled
     */
    public function isEnabled(): bool
    {
        return config('services.coinpayments.enabled', false);
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USDT.TRC20' => 'USDT (TRC20 Network)',
            'USDT.ERC20' => 'USDT (ERC20 Network)',
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
        ];
    }
}
