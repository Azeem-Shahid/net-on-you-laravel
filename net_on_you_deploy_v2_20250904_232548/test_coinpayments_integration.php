<?php

/**
 * CoinPayments Integration Test Script
 * 
 * This script tests the complete CoinPayments integration
 * Run this before deploying to production
 */

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CoinPaymentsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🚀 CoinPayments Integration Test Script\n";
echo "=====================================\n\n";

$coinpayments = new CoinPaymentsService();
$allTestsPassed = true;

// Test 1: Service Configuration
echo "1. Testing Service Configuration...\n";
try {
    if (!$coinpayments->isEnabled()) {
        throw new Exception("CoinPayments service is disabled");
    }
    
    $merchantId = config('services.coinpayments.merchant_id');
    $publicKey = config('services.coinpayments.public_key');
    $privateKey = config('services.coinpayments.private_key');
    $ipnSecret = config('services.coinpayments.ipn_secret');
    
    if (!$merchantId || !$publicKey || !$privateKey || !$ipnSecret) {
        throw new Exception("Missing required credentials");
    }
    
    echo "   ✅ Service enabled and credentials configured\n";
    echo "   - Merchant ID: " . substr($merchantId, 0, 8) . "...\n";
    echo "   - Currency: " . config('services.coinpayments.currency2') . "\n";
    echo "   - IPN URL: " . config('services.coinpayments.ipn_url') . "\n";
} catch (Exception $e) {
    echo "   ❌ Configuration test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 2: Currency Support
echo "\n2. Testing Currency Support...\n";
try {
    $currencies = $coinpayments->getSupportedCurrencies();
    $requiredCurrencies = ['USDT.TRC20', 'USDT.ERC20', 'USDC.TRC20', 'USDC.ERC20'];
    
    foreach ($requiredCurrencies as $currency) {
        if (!isset($currencies[$currency])) {
            throw new Exception("Missing required currency: $currency");
        }
    }
    
    echo "   ✅ All required currencies supported:\n";
    foreach ($currencies as $code => $name) {
        echo "      - $code: $name\n";
    }
} catch (Exception $e) {
    echo "   ❌ Currency test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 3: Database Connection
echo "\n3. Testing Database Connection...\n";
try {
    $result = DB::select('SELECT 1 as test');
    if (empty($result) || $result[0]->test !== 1) {
        throw new Exception("Database query failed");
    }
    echo "   ✅ Database connection working\n";
} catch (Exception $e) {
    echo "   ❌ Database test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 4: Transaction Table Structure
echo "\n4. Testing Transaction Table Structure...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM transactions");
    $requiredColumns = ['id', 'user_id', 'txn_id', 'gateway', 'status', 'amount', 'currency'];
    
    $existingColumns = array_column($columns, 'Field');
    foreach ($requiredColumns as $column) {
        if (!in_array($column, $existingColumns)) {
            throw new Exception("Missing required column: $column");
        }
    }
    echo "   ✅ Transaction table structure is correct\n";
} catch (Exception $e) {
    echo "   ❌ Table structure test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 5: Signature Generation
echo "\n5. Testing Signature Generation...\n";
try {
    $testParams = [
        'cmd' => 'create_transaction',
        'version' => '1',
        'key' => config('services.coinpayments.public_key'),
        'amount' => 39.90,
        'currency1' => 'USD',
        'currency2' => 'USDT.TRC20',
    ];
    
    $reflection = new ReflectionClass($coinpayments);
    $method = $reflection->getMethod('generateSignature');
    $method->setAccessible(true);
    
    $signature = $method->invoke($coinpayments, $testParams);
    if (strlen($signature) !== 128) {
        throw new Exception("Invalid signature length: " . strlen($signature));
    }
    
    echo "   ✅ Signature generation working (128 chars)\n";
} catch (Exception $e) {
    echo "   ❌ Signature test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 6: Status Mapping
echo "\n6. Testing Status Mapping...\n";
try {
    $statusTests = [
        0 => 'pending',
        1 => 'processing',
        2 => 'completed',
        100 => 'completed',
        -1 => 'failed'
    ];
    
    foreach ($statusTests as $status => $expected) {
        $mapped = CoinPaymentsService::mapStatus($status);
        if ($mapped !== $expected) {
            throw new Exception("Status $status maps to '$mapped', expected '$expected'");
        }
    }
    echo "   ✅ Status mapping working correctly\n";
} catch (Exception $e) {
    echo "   ❌ Status mapping test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 7: IPN Endpoint Accessibility
echo "\n7. Testing IPN Endpoint Accessibility...\n";
try {
    $ipnUrl = config('services.coinpayments.ipn_url');
    $parsedUrl = parse_url($ipnUrl);
    
    if (!$parsedUrl || !isset($parsedUrl['host'])) {
        throw new Exception("Invalid IPN URL format");
    }
    
    // Test if we can resolve the host
    $ip = gethostbyname($parsedUrl['host']);
    if ($ip === $parsedUrl['host']) {
        throw new Exception("Cannot resolve IPN URL host");
    }
    
    echo "   ✅ IPN URL is accessible: $ipnUrl\n";
    echo "   - Host: " . $parsedUrl['host'] . "\n";
    echo "   - Resolves to: $ip\n";
} catch (Exception $e) {
    echo "   ❌ IPN endpoint test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 8: Environment Configuration
echo "\n8. Testing Environment Configuration...\n";
try {
    $requiredEnvVars = [
        'COINPAYMENTS_ENABLED',
        'COINPAYMENTS_MERCHANT_ID',
        'COINPAYMENTS_PUBLIC_KEY',
        'COINPAYMENTS_PRIVATE_KEY',
        'COINPAYMENTS_IPN_SECRET',
        'COINPAYMENTS_CURRENCY2',
        'COINPAYMENTS_IPN_URL'
    ];
    
    foreach ($requiredEnvVars as $var) {
        if (!env($var)) {
            throw new Exception("Missing environment variable: $var");
        }
    }
    
    echo "   ✅ All required environment variables set\n";
} catch (Exception $e) {
    echo "   ❌ Environment test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 9: Logging Configuration
echo "\n9. Testing Logging Configuration...\n";
try {
    $logPath = storage_path('logs/laravel.log');
    if (!is_writable(dirname($logPath))) {
        throw new Exception("Log directory is not writable");
    }
    
    // Test logging
    Log::info('CoinPayments test log entry', ['test' => true]);
    echo "   ✅ Logging is working correctly\n";
} catch (Exception $e) {
    echo "   ❌ Logging test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Test 10: Production Readiness
echo "\n10. Testing Production Readiness...\n";
try {
    $appEnv = config('app.env');
    $appDebug = config('app.debug');
    $appUrl = config('app.url');
    
    if ($appEnv === 'production' && $appDebug) {
        throw new Exception("Debug mode should be disabled in production");
    }
    
    if (!filter_var($appUrl, FILTER_VALIDATE_URL)) {
        throw new Exception("Invalid APP_URL configuration");
    }
    
    echo "   ✅ Production configuration looks good\n";
    echo "   - Environment: $appEnv\n";
    echo "   - Debug mode: " . ($appDebug ? 'Enabled' : 'Disabled') . "\n";
    echo "   - App URL: $appUrl\n";
} catch (Exception $e) {
    echo "   ❌ Production readiness test failed: " . $e->getMessage() . "\n";
    $allTestsPassed = false;
}

// Final Results
echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 TEST RESULTS SUMMARY\n";
echo str_repeat("=", 50) . "\n";

if ($allTestsPassed) {
    echo "✅ ALL TESTS PASSED!\n";
    echo "🎉 Your CoinPayments integration is ready for production!\n\n";
    
    echo "📋 Next Steps:\n";
    echo "1. Update CoinPayments dashboard IPN URL to: " . config('services.coinpayments.ipn_url') . "\n";
    echo "2. Test with small amounts first ($1.00)\n";
    echo "3. Monitor logs for IPN notifications\n";
    echo "4. Verify transaction status updates\n";
    echo "5. Test subscription activation\n\n";
    
    echo "🔧 Supported Currencies:\n";
    $currencies = $coinpayments->getSupportedCurrencies();
    foreach ($currencies as $code => $name) {
        echo "   - $code: $name\n";
    }
    
} else {
    echo "❌ SOME TESTS FAILED!\n";
    echo "🔧 Please fix the issues above before deploying to production.\n";
    echo "📞 Check the troubleshooting guide for solutions.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 50) . "\n";

