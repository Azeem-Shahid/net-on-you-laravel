# CoinPayments Integration Guide

## 🚀 REAL INTEGRATION STEPS

This guide covers the complete implementation of CoinPayments integration for USDT payments, replacing NowPayments.

### 1. Environment Configuration

Add these variables to your `.env` file:

```bash
# CoinPayments Configuration
COINPAYMENTS_MERCHANT_ID="your_merchant_id_here"
COINPAYMENTS_PUBLIC_KEY="your_public_key_here"
COINPAYMENTS_PRIVATE_KEY="your_private_key_here"
COINPAYMENTS_IPN_SECRET="your_ipn_secret_here"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="${APP_URL}/payments/coinpayments/ipn"
COINPAYMENTS_ENABLED=true
COINPAYMENTS_SANDBOX=false
```

### 2. Get Your Real CoinPayments Credentials

#### Step 1: Create CoinPayments Account
1. Visit [CoinPayments.net](https://www.coinpayments.net)
2. Sign up for a merchant account
3. Complete verification process

#### Step 2: Generate API Keys
1. Login to your CoinPayments dashboard
2. Go to **Account Settings** → **API Keys**
3. Create new API key pair (Public/Private)
4. Save both keys securely

#### Step 3: Configure IPN Settings
1. Go to **Account Settings** → **Merchant Settings**
2. Set **IPN URL** to: `https://yourdomain.com/payments/coinpayments/ipn`
3. Generate and save **IPN Secret**
4. Copy your **Merchant ID** from account overview

#### Step 4: Test vs Production
- For testing: Use sandbox mode if available, or small amounts
- For production: Disable sandbox mode and use real transactions

### 3. Database Setup

Run the migration to add CoinPayments columns:

```bash
php artisan migrate
```

Seed the default settings:

```bash
php artisan db:seed --class=SettingsSeeder
```

### 4. Admin Configuration

1. Login to your admin panel
2. Go to **Settings**
3. Enable **CoinPayments** in the payment gateway dropdown
4. Configure **CoinPayments Settings**:
   - Enable CoinPayments: Yes
   - Default Crypto Currency: USDT.TRC20 (or USDT.ERC20)

### 5. Real Testing

#### Test Payment Flow:
```bash
# Use ngrok or similar for local testing
ngrok http 8000

# Update your .env with ngrok URL
COINPAYMENTS_IPN_URL="https://your-ngrok-url.ngrok.io/payments/coinpayments/ipn"
```

#### Test IPN Manually:
```bash
curl -X POST https://yourdomain.com/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "HMAC: calculated_hmac_signature" \
  -d "merchant=YOUR_MERCHANT_ID&status=100&txn_id=TEST123&amount1=10.00&amount2=10.00&currency1=USD&currency2=USDT.TRC20&confirms=3"
```

### 6. Security Configuration

#### CSRF Exception
Ensure IPN endpoint is excluded from CSRF in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'payments/coinpayments/ipn',
];
```

#### Rate Limiting (Optional)
Add to `routes/web.php`:
```php
Route::post('/payments/coinpayments/ipn', [PaymentController::class, 'coinPaymentsIPN'])
    ->name('coinpayments.ipn')
    ->middleware('throttle:60,1');
```

### 7. Monitoring & Logs

#### Enable Logging
Monitor these log entries:
- `CoinPayments IPN verified successfully` - IPN processed
- `CoinPayments payment completed` - Payment successful
- `CoinPayments IPN failed verify` - Verification failed

#### Check Transaction Status
```bash
# View recent transactions
php artisan tinker
>>> App\Models\Transaction::where('gateway', 'coinpayments')->latest()->take(5)->get()
```

### 8. Frontend Integration

The frontend is already updated to:
- Show "Pay with crypto via CoinPayments" option
- Display CoinPayments-specific payment instructions
- Handle payment status updates automatically

### 9. Production Checklist

Before going live:

- [ ] Real CoinPayments account created and verified
- [ ] API keys generated and securely stored
- [ ] IPN URL configured in CoinPayments dashboard
- [ ] HTTPS enabled on production server
- [ ] Database migration completed
- [ ] Settings configured in admin panel
- [ ] CSRF exception added for IPN endpoint
- [ ] Payment flow tested with small amounts
- [ ] IPN webhook tested and verified
- [ ] Logging enabled and monitored
- [ ] Error handling tested

### 10. Supported Cryptocurrencies

Current implementation supports:
- **USDT (TRC20)** - Default, lower fees
- **USDT (ERC20)** - Higher fees but more widely supported  
- **Bitcoin (BTC)** - Can be enabled
- **Ethereum (ETH)** - Can be enabled

### 11. Migration from NowPayments

All NowPayments code has been removed:
- ✅ NowPayments service class removed
- ✅ NowPayments routes removed
- ✅ Frontend updated to show CoinPayments
- ✅ Legacy webhook handler maintained for old transactions
- ✅ Database supports both old and new payment gateways

### 12. Troubleshooting

#### Common Issues:

**IPN not received:**
- Check IPN URL in CoinPayments dashboard
- Verify HTTPS is working
- Check server logs for blocked requests

**HMAC verification fails:**
- Ensure IPN secret matches exactly
- Check for trailing spaces in configuration
- Verify server can receive raw POST body

**Transactions not updating:**
- Check database `txn_id` column exists
- Verify transaction exists in database
- Check logs for database errors

**Payment not completing:**
- Verify confirmations threshold
- Check CoinPayments transaction status
- Ensure webhook is receiving valid data

#### Debug Commands:
```bash
# Check configuration
php artisan tinker
>>> config('services.coinpayments')

# Test service
>>> app(\App\Services\CoinPaymentsService::class)->isEnabled()

# Check recent logs
tail -f storage/logs/laravel.log | grep CoinPayments
```

### 13. Support & Documentation

- **CoinPayments API Docs**: https://www.coinpayments.net/apidoc
- **Laravel Documentation**: https://laravel.com/docs
- **This Implementation**: All files modified and tested

---

## 🎯 Implementation Complete

The CoinPayments integration is **production-ready** with:
- ✅ Secure HMAC verification
- ✅ Comprehensive error handling  
- ✅ Database transaction tracking
- ✅ Admin configuration panel
- ✅ Real-time IPN processing
- ✅ Legacy payment support
- ✅ Extensive testing suite

**Ready for production use!** 🚀
