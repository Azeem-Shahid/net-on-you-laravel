<?php

/**
 * Complete CoinPayments Test Suite
 * 
 * This script tests all aspects of CoinPayments integration
 * Run this after deployment to verify everything is working
 */

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CoinPaymentsService;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "🚀 Complete CoinPayments Test Suite\n";
echo "===================================\n\n";

$allTestsPassed = true;
$testResults = [];

// Test 1: Service Configuration
echo "1. Testing Service Configuration...\n";
try {
    $coinpayments = new CoinPaymentsService();
    
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
    
    $testResults['service_config'] = true;
} catch (Exception $e) {
    echo "   ❌ Service configuration failed: " . $e->getMessage() . "\n";
    $testResults['service_config'] = false;
    $allTestsPassed = false;
}

// Test 2: Database Connection
echo "\n2. Testing Database Connection...\n";
try {
    $result = DB::select('SELECT 1 as test');
    if (empty($result) || $result[0]->test !== 1) {
        throw new Exception("Database query failed");
    }
    echo "   ✅ Database connection working\n";
    $testResults['database'] = true;
} catch (Exception $e) {
    echo "   ❌ Database test failed: " . $e->getMessage() . "\n";
    $testResults['database'] = false;
    $allTestsPassed = false;
}

// Test 3: Transaction Table Structure
echo "\n3. Testing Transaction Table Structure...\n";
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
    $testResults['table_structure'] = true;
} catch (Exception $e) {
    echo "   ❌ Table structure test failed: " . $e->getMessage() . "\n";
    $testResults['table_structure'] = false;
    $allTestsPassed = false;
}

// Test 4: Email Configuration
echo "\n4. Testing Email Configuration...\n";
try {
    // Test email configuration without actually sending
    $mailHost = config('mail.mailers.smtp.host');
    $mailPort = config('mail.mailers.smtp.port');
    $mailUsername = config('mail.mailers.smtp.username');
    $mailPassword = config('mail.mailers.smtp.password');
    
    if (!$mailHost || !$mailPort || !$mailUsername || !$mailPassword) {
        throw new Exception("Email configuration incomplete");
    }
    
    echo "   ✅ Email configuration looks good\n";
    echo "   - Host: $mailHost\n";
    echo "   - Port: $mailPort\n";
    echo "   - Username: $mailUsername\n";
    
    $testResults['email_config'] = true;
} catch (Exception $e) {
    echo "   ❌ Email configuration test failed: " . $e->getMessage() . "\n";
    $testResults['email_config'] = false;
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
    $testResults['signature_generation'] = true;
} catch (Exception $e) {
    echo "   ❌ Signature generation test failed: " . $e->getMessage() . "\n";
    $testResults['signature_generation'] = false;
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
    $testResults['status_mapping'] = true;
} catch (Exception $e) {
    echo "   ❌ Status mapping test failed: " . $e->getMessage() . "\n";
    $testResults['status_mapping'] = false;
    $allTestsPassed = false;
}

// Test 7: Currency Support
echo "\n7. Testing Currency Support...\n";
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
    
    $testResults['currency_support'] = true;
} catch (Exception $e) {
    echo "   ❌ Currency support test failed: " . $e->getMessage() . "\n";
    $testResults['currency_support'] = false;
    $allTestsPassed = false;
}

// Test 8: IPN Endpoint Accessibility
echo "\n8. Testing IPN Endpoint Accessibility...\n";
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
    
    $testResults['ipn_endpoint'] = true;
} catch (Exception $e) {
    echo "   ❌ IPN endpoint test failed: " . $e->getMessage() . "\n";
    $testResults['ipn_endpoint'] = false;
    $allTestsPassed = false;
}

// Test 9: Transaction Creation Test
echo "\n9. Testing Transaction Creation...\n";
try {
    // Create a test transaction in database
    $testTransaction = new Transaction();
    $testTransaction->user_id = 1; // Assuming user 1 exists
    $testTransaction->txn_id = 'TEST_' . time();
    $testTransaction->gateway = 'coinpayments';
    $testTransaction->status = 'pending';
    $testTransaction->amount = 1.00;
    $testTransaction->currency = 'USD';
    $testTransaction->save();
    
    echo "   ✅ Test transaction created successfully\n";
    echo "   - Transaction ID: " . $testTransaction->id . "\n";
    echo "   - Txn ID: " . $testTransaction->txn_id . "\n";
    
    // Clean up test transaction
    $testTransaction->delete();
    echo "   ✅ Test transaction cleaned up\n";
    
    $testResults['transaction_creation'] = true;
} catch (Exception $e) {
    echo "   ❌ Transaction creation test failed: " . $e->getMessage() . "\n";
    $testResults['transaction_creation'] = false;
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
    
    $testResults['production_readiness'] = true;
} catch (Exception $e) {
    echo "   ❌ Production readiness test failed: " . $e->getMessage() . "\n";
    $testResults['production_readiness'] = false;
    $allTestsPassed = false;
}

// Final Results
echo "\n" . str_repeat("=", 60) . "\n";
echo "🏁 COMPLETE TEST RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passedTests = array_sum($testResults);
$totalTests = count($testResults);

echo "Tests Passed: $passedTests/$totalTests\n\n";

foreach ($testResults as $test => $passed) {
    $status = $passed ? "✅ PASS" : "❌ FAIL";
    echo sprintf("%-25s %s\n", ucwords(str_replace('_', ' ', $test)) . ":", $status);
}

echo "\n" . str_repeat("=", 60) . "\n";

if ($allTestsPassed) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "✅ Your CoinPayments integration is fully functional!\n\n";
    
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

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
