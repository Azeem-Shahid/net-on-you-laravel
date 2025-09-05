#!/bin/bash

echo "💰 CoinPayments Payment Flow Test"
echo "================================="

# Test 1: Create Test Transaction
echo "1. Testing Transaction Creation..."
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$service = new App\Services\CoinPaymentsService();
try {
    // Test parameter preparation (without API call)
    \$params = [
        'cmd' => 'create_transaction',
        'version' => '1',
        'key' => config('services.coinpayments.public_key'),
        'amount' => 1.00, // Test with \$1
        'currency1' => 'USD',
        'currency2' => 'USDT.TRC20',
        'buyer_email' => 'test@example.com',
        'item_name' => 'Test Payment',
        'invoice' => 'TEST-' . time(),
        'ipn_url' => config('services.coinpayments.ipn_url'),
    ];
    
    \$reflection = new ReflectionClass(\$service);
    \$method = \$reflection->getMethod('generateSignature');
    \$method->setAccessible(true);
    \$signature = \$method->invoke(\$service, \$params);
    
    echo '   ✅ Transaction parameters prepared successfully\n';
    echo '   ✅ Signature generated: ' . substr(\$signature, 0, 20) . '...\n';
} catch (Exception \$e) {
    echo '   ❌ Transaction creation failed: ' . \$e->getMessage() . '\n';
}
"

# Test 2: IPN Endpoint Test
echo "2. Testing IPN Endpoint..."
curl -X POST http://localhost:8000/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "test=1" \
  -w "HTTP Status: %{http_code}\n" \
  -s -o /dev/null

# Test 3: Database Transaction
echo "3. Testing Database Transaction..."
php artisan tinker --execute="
try {
    \$transaction = new App\Models\Transaction();
    \$transaction->user_id = 1;
    \$transaction->txn_id = 'TEST_' . time();
    \$transaction->gateway = 'coinpayments';
    \$transaction->status = 'pending';
    \$transaction->amount = 1.00;
    \$transaction->currency = 'USD';
    \$transaction->save();
    echo '   ✅ Test transaction created in database\n';
    \$transaction->delete(); // Clean up
} catch (Exception \$e) {
    echo '   ❌ Database transaction failed: ' . \$e->getMessage() . '\n';
}
"

echo "✅ Payment flow test completed!"
