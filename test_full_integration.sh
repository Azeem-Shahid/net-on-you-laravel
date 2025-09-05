#!/bin/bash

echo "🚀 CoinPayments Full Integration Test"
echo "====================================="

# Test 1: Service Configuration
echo "1. Testing Service Configuration..."
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$service = new App\Services\CoinPaymentsService();
if (\$service->isEnabled()) {
    echo '   ✅ CoinPayments service enabled\n';
} else {
    echo '   ❌ CoinPayments service disabled\n';
    exit(1);
}
"

# Test 2: Database Connection
echo "2. Testing Database Connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo '   ✅ Database connected\n';"

# Test 3: Email Configuration
echo "3. Testing Email Configuration..."
php artisan tinker --execute="
try {
    Mail::raw('Test email', function(\$m) { \$m->to('test@example.com')->subject('Test'); });
    echo '   ✅ Email configuration working\n';
} catch (Exception \$e) {
    echo '   ⚠️  Email test failed: ' . \$e->getMessage() . '\n';
}
"

# Test 4: Routes
echo "4. Testing Routes..."
php artisan route:list | grep -q "coinpayments" && echo "   ✅ CoinPayments routes found" || echo "   ❌ CoinPayments routes missing"

# Test 5: Configuration
echo "5. Testing Configuration..."
php artisan tinker --execute="
echo '   Merchant ID: ' . config('services.coinpayments.merchant_id') . '\n';
echo '   Currency: ' . config('services.coinpayments.currency2') . '\n';
echo '   IPN URL: ' . config('services.coinpayments.ipn_url') . '\n';
"

echo "✅ Full integration test completed!"

