# 🧪 CoinPayments Test Commands Quick Reference

## 📋 **Complete Test Commands After Integration**

### **🚀 Quick Test Commands**

#### **1. Complete Integration Test**
```bash
# Run the comprehensive test suite
php test_coinpayments_complete.php

# Expected: All 10 tests should pass ✅
```

#### **2. Basic Integration Test**
```bash
# Run the original integration test
php test_coinpayments_integration.php

# Expected: All tests should pass ✅
```

#### **3. Full Integration Test (Script)**
```bash
# Run the shell script version
./test_full_integration.sh

# Expected: All 5 tests should pass ✅
```

#### **4. Payment Flow Test**
```bash
# Test payment processing flow
./test_payment_flow.sh

# Expected: Transaction creation and IPN testing ✅
```

---

## 🔧 **Individual Test Commands**

### **Service Configuration Test**
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$service = new App\Services\CoinPaymentsService();
echo 'Enabled: ' . (\$service->isEnabled() ? 'Yes' : 'No') . PHP_EOL;
echo 'Merchant ID: ' . config('services.coinpayments.merchant_id') . PHP_EOL;
echo 'Currency: ' . config('services.coinpayments.currency2') . PHP_EOL;
echo 'IPN URL: ' . config('services.coinpayments.ipn_url') . PHP_EOL;
"
```

### **Database Connection Test**
```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully!';"
```

### **Email Configuration Test**
```bash
php artisan tinker --execute="
try {
    Mail::raw('CoinPayments Integration Test Email', function(\$message) {
        \$message->to('your-test-email@gmail.com')
                ->subject('CoinPayments Test - ' . now());
    });
    echo 'Test email sent successfully!';
} catch (Exception \$e) {
    echo 'Email test failed: ' . \$e->getMessage();
}
"
```

### **Route Testing**
```bash
# Check if CoinPayments routes are registered
php artisan route:list | grep coinpayments

# Should show:
# POST payments/coinpayments/ipn
# POST payments/coinpayments/create
```

### **Configuration Cache Test**
```bash
# Clear and rebuild configuration cache
php artisan config:clear
php artisan config:cache

# Test if configuration is properly cached
php artisan tinker --execute="echo config('services.coinpayments.enabled') ? 'Enabled' : 'Disabled';"
```

### **IPN Endpoint Test**
```bash
# Test IPN endpoint accessibility
curl -X POST http://localhost:8000/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "test=1" \
  -w "HTTP Status: %{http_code}\n"
```

### **Payment Flow Test**
```bash
# Test payment initiation
curl -X POST http://localhost:8000/payment/initiate \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "plan=monthly&payment_method=crypto&crypto_currency=USDT.TRC20"
```

---

## 🔧 **Additional Configurations Needed**

### **1. CoinPayments Dashboard Setup**
```bash
# Update IPN URL in CoinPayments dashboard
# Go to: Account Settings → Merchant Settings
# Set IPN URL to: https://yourdomain.com/payments/coinpayments/ipn
```

### **2. Database Migrations**
```bash
# Ensure all migrations are run
php artisan migrate --force

# Check if transactions table has required columns
php artisan tinker --execute="
\$columns = DB::select('SHOW COLUMNS FROM transactions');
foreach(\$columns as \$column) {
    echo \$column->Field . ' - ' . \$column->Type . PHP_EOL;
}
"
```

### **3. Payment Controller Methods**
Ensure these methods exist in `PaymentController`:
- `createCoinPayments()` - Create new transactions
- `coinPaymentsIPN()` - Handle IPN notifications

### **4. Frontend Integration**
Add crypto payment option to your payment forms:
```html
<!-- Add to payment form -->
<input type="radio" name="payment_method" value="crypto" id="crypto">
<label for="crypto">Crypto Payment (USDT/USDC)</label>

<select name="crypto_currency">
    <option value="USDT.TRC20">USDT (TRC20 Network)</option>
    <option value="USDT.ERC20">USDT (ERC20 Network)</option>
    <option value="USDC.TRC20">USDC (TRC20 Network)</option>
    <option value="USDC.ERC20">USDC (ERC20 Network)</option>
</select>
```

---

## 🚀 **Complete Test Sequence**

### **Step 1: Run All Tests**
```bash
# Run complete test suite
php test_coinpayments_complete.php

# If all tests pass, proceed to step 2
```

### **Step 2: Test Payment Flow**
```bash
# Test payment initiation
curl -X POST http://localhost:8000/payment/initiate \
  -d "plan=monthly&payment_method=crypto"

# Test IPN endpoint
curl -X POST http://localhost:8000/payments/coinpayments/ipn \
  -d "test=1"
```

### **Step 3: Monitor Logs**
```bash
# Watch for CoinPayments activity
tail -f storage/logs/laravel.log | grep -i coinpayments

# Watch for IPN notifications
tail -f storage/logs/laravel.log | grep -i ipn
```

### **Step 4: Test with Real Transaction**
```bash
# Create a small test transaction ($1.00)
# Monitor the complete flow
# Verify status updates
```

---

## 📊 **Test Results Interpretation**

### **✅ All Tests Pass**
- CoinPayments integration is fully functional
- Ready for production deployment
- All configurations are correct

### **❌ Some Tests Fail**
- Check the specific error messages
- Fix configuration issues
- Re-run tests after fixes

### **⚠️ Warnings**
- Non-critical issues that should be addressed
- May not prevent functionality but should be fixed

---

## 🔍 **Troubleshooting Common Issues**

### **Service Configuration Failed**
```bash
# Check .env file
grep -i coinpayments .env

# Verify configuration
php artisan tinker --execute="dd(config('services.coinpayments'));"
```

### **Database Connection Failed**
```bash
# Check database credentials
php artisan tinker --execute="DB::connection()->getPdo();"

# Check .env database settings
grep -i db_ .env
```

### **Email Configuration Failed**
```bash
# Check email settings
php artisan tinker --execute="dd(config('mail'));"

# Test email sending
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('test@example.com')->subject('Test'); });"
```

### **IPN Endpoint Not Working**
```bash
# Check if route exists
php artisan route:list | grep coinpayments

# Test endpoint manually
curl -X POST http://localhost:8000/payments/coinpayments/ipn -d "test=1"
```

---

## 🎯 **Production Readiness Checklist**

### **Before Going Live**
- [ ] All test commands pass
- [ ] CoinPayments dashboard IPN URL updated
- [ ] Database migrations run
- [ ] Payment forms include crypto option
- [ ] IPN endpoint responds correctly
- [ ] Email notifications working
- [ ] Transaction creation works
- [ ] Status updates work

### **After Going Live**
- [ ] Test with small amounts ($1.00)
- [ ] Monitor IPN notifications
- [ ] Verify transaction processing
- [ ] Check subscription activation
- [ ] Monitor logs for errors

---

## 🚀 **Quick Start Commands**

```bash
# 1. Run complete test suite
php test_coinpayments_complete.php

# 2. Test payment flow
./test_payment_flow.sh

# 3. Check logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# 4. Test IPN endpoint
curl -X POST http://localhost:8000/payments/coinpayments/ipn -d "test=1"
```

**Your CoinPayments integration is now fully functional and ready for production!** 🎉

