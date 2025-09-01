# 🚀 Complete CoinPayments Setup Guide for Net On You

## 📋 Table of Contents
1. [Account Setup](#account-setup)
2. [Environment Configuration](#environment-configuration)
3. [Database Setup](#database-setup)
4. [Admin Panel Configuration](#admin-panel-configuration)
5. [Testing & Verification](#testing--verification)
6. [Production Deployment](#production-deployment)
7. [Troubleshooting](#troubleshooting)
8. [Security Best Practices](#security-best-practices)

---

## 🏗️ Account Setup

### Step 1: Create CoinPayments Account
1. **Visit**: [https://www.coinpayments.net](https://www.coinpayments.net)
2. **Click**: "Sign Up" or "Create Account"
3. **Fill in**:
   - Email address
   - Strong password
   - Company name (if applicable)
   - Country
4. **Verify email** by clicking the confirmation link

### Step 2: Complete Verification
1. **Login** to your CoinPayments account
2. **Go to**: Account Settings → Verification
3. **Complete**:
   - Personal information
   - Business information (if applicable)
   - Identity verification (ID upload)
   - Address verification
4. **Wait** for approval (usually 24-48 hours)

### Step 3: Generate API Keys
1. **Navigate to**: Account Settings → API Keys
2. **Click**: "Create New API Key"
3. **Configure**:
   - **Key Name**: "Net On You Production" (or "Test" for development)
   - **Permissions**: 
     - ✅ Create Transaction
     - ✅ Get Transaction Info
     - ✅ Get Account Info
     - ✅ Get Withdrawal Info
   - **IP Restrictions**: Leave empty for now (add your server IP later)
4. **Save** both Public Key and Private Key securely

### Step 4: Configure IPN Settings
1. **Go to**: Account Settings → Merchant Settings
2. **Set IPN URL**: `https://yourdomain.com/payments/coinpayments/ipn`
3. **Generate IPN Secret**: Click "Generate New Secret"
4. **Save** the IPN Secret securely
5. **Copy** your Merchant ID from the account overview

---

## ⚙️ Environment Configuration

### Step 1: Update .env File
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

# For testing (optional)
COINPAYMENTS_TEST_MODE=false
COINPAYMENTS_MIN_CONFIRMATIONS=3
```

### Step 2: Update config/services.php
Ensure your `config/services.php` includes:

```php
'coinpayments' => [
    'merchant_id' => env('COINPAYMENTS_MERCHANT_ID'),
    'public_key' => env('COINPAYMENTS_PUBLIC_KEY'),
    'private_key' => env('COINPAYMENTS_PRIVATE_KEY'),
    'ipn_secret' => env('COINPAYMENTS_IPN_SECRET'),
    'currency2' => env('COINPAYMENTS_CURRENCY2', 'USDT.TRC20'),
    'ipn_url' => env('COINPAYMENTS_IPN_URL'),
    'enabled' => env('COINPAYMENTS_ENABLED', false),
    'sandbox' => env('COINPAYMENTS_SANDBOX', false),
    'min_confirmations' => env('COINPAYMENTS_MIN_CONFIRMATIONS', 3),
],
```

---

## 🗄️ Database Setup

### Step 1: Run Migrations
```bash
# Navigate to your project directory
cd /path/to/your/project

# Run migrations
php artisan migrate

# If you need to rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

### Step 2: Seed Default Settings
```bash
# Seed the database with default settings
php artisan db:seed --class=SettingsSeeder

# Or run all seeders
php artisan db:seed
```

### Step 3: Verify Database Tables
Check that these tables exist:
- `transactions` (with CoinPayments columns)
- `settings` (with CoinPayments configuration)
- `payment_gateways` (if applicable)

---

## 🎛️ Admin Panel Configuration

### Step 1: Access Admin Panel
1. **Login** to: `https://yourdomain.com/admin`
2. **Use** your admin credentials

### Step 2: Enable CoinPayments
1. **Navigate to**: Settings → Payment Gateways
2. **Find**: CoinPayments section
3. **Enable**: Toggle "Enable CoinPayments" to Yes
4. **Configure**:
   - **Default Crypto Currency**: USDT.TRC20
   - **Minimum Confirmations**: 3
   - **IPN URL**: Verify it matches your .env
   - **Merchant ID**: Verify it matches your .env

### Step 3: Test Configuration
1. **Click**: "Test Connection" button
2. **Verify**: Success message appears
3. **Check**: No error messages in logs

---

## 🧪 Testing & Verification

### Step 1: Local Testing with ngrok
```bash
# Install ngrok (if not already installed)
# Download from: https://ngrok.com/download

# Start ngrok on your Laravel port
ngrok http 8000

# Copy the HTTPS URL (e.g., https://abc123.ngrok.io)
# Update your .env file
COINPAYMENTS_IPN_URL="https://abc123.ngrok.io/payments/coinpayments/ipn"
```

### Step 2: Test Payment Flow
1. **Create** a test user account
2. **Navigate** to payment page
3. **Select** "Pay with Crypto via CoinPayments"
4. **Complete** the payment process
5. **Verify** payment appears in CoinPayments dashboard

### Step 3: Test IPN Webhook
```bash
# Test IPN manually (replace with your actual values)
curl -X POST https://yourdomain.com/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "HMAC: calculated_hmac_signature" \
  -d "merchant=YOUR_MERCHANT_ID&status=100&txn_id=TEST123&amount1=10.00&amount2=10.00&currency1=USD&currency2=USDT.TRC20&confirms=3"
```

### Step 4: Monitor Logs
```bash
# Watch Laravel logs for CoinPayments activity
tail -f storage/logs/laravel.log | grep CoinPayments

# Check for these log entries:
# - "CoinPayments IPN verified successfully"
# - "CoinPayments payment completed"
# - "CoinPayments IPN failed verify"
```

---

## 🚀 Production Deployment

### Step 1: Pre-Deployment Checklist
- [ ] CoinPayments account verified and approved
- [ ] Real API keys generated (not test keys)
- [ ] IPN URL updated to production domain
- [ ] HTTPS enabled on production server
- [ ] Database migration completed
- [ ] Admin panel configured
- [ ] CSRF exception added for IPN endpoint

### Step 2: Update Production .env
```bash
# Production environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# CoinPayments Production Settings
COINPAYMENTS_MERCHANT_ID="your_real_merchant_id"
COINPAYMENTS_PUBLIC_KEY="your_real_public_key"
COINPAYMENTS_PRIVATE_KEY="your_real_private_key"
COINPAYMENTS_IPN_SECRET="your_real_ipn_secret"
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_ENABLED=true
COINPAYMENTS_SANDBOX=false
```

### Step 3: Security Configuration
1. **Add IPN endpoint to CSRF exceptions** in `app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    'payments/coinpayments/ipn',
];
```

2. **Configure rate limiting** (optional):
```php
// In routes/web.php
Route::post('/payments/coinpayments/ipn', [PaymentController::class, 'coinPaymentsIPN'])
    ->name('coinpayments.ipn')
    ->middleware('throttle:60,1');
```

### Step 4: Final Verification
1. **Test** payment flow with small amount ($1-5)
2. **Verify** IPN webhook receives data
3. **Check** transaction appears in database
4. **Confirm** user access granted correctly
5. **Monitor** logs for any errors

---

## 🔧 Troubleshooting

### Common Issues & Solutions

#### Issue 1: IPN Not Received
**Symptoms**: Payment shows as pending, no webhook data
**Solutions**:
- Verify IPN URL in CoinPayments dashboard
- Check HTTPS is working on production
- Ensure server can receive POST requests
- Check firewall settings

#### Issue 2: HMAC Verification Fails
**Symptoms**: IPN received but verification fails
**Solutions**:
- Verify IPN secret matches exactly
- Check for trailing spaces in configuration
- Ensure server receives raw POST body
- Verify HMAC calculation method

#### Issue 3: Transactions Not Updating
**Symptoms**: Payment confirmed but user access not granted
**Solutions**:
- Check database `txn_id` column exists
- Verify transaction exists in database
- Check logs for database errors
- Verify webhook processing logic

#### Issue 4: Payment Not Completing
**Symptoms**: Payment stuck in pending status
**Solutions**:
- Check confirmations threshold
- Verify CoinPayments transaction status
- Ensure webhook receives valid data
- Check for database constraints

### Debug Commands
```bash
# Check CoinPayments configuration
php artisan tinker
>>> config('services.coinpayments')

# Test CoinPayments service
>>> app(\App\Services\CoinPaymentsService::class)->isEnabled()

# Check recent transactions
>>> App\Models\Transaction::where('gateway', 'coinpayments')->latest()->take(5)->get()

# View recent logs
tail -f storage/logs/laravel.log | grep CoinPayments
```

---

## 🔒 Security Best Practices

### 1. API Key Security
- **Never** commit API keys to version control
- **Use** environment variables for all sensitive data
- **Rotate** API keys regularly
- **Limit** API key permissions to minimum required

### 2. IPN Security
- **Verify** HMAC signature on every IPN
- **Use** HTTPS for all IPN communications
- **Implement** rate limiting on IPN endpoint
- **Log** all IPN attempts for monitoring

### 3. Server Security
- **Keep** server software updated
- **Use** strong firewall rules
- **Monitor** server logs regularly
- **Implement** intrusion detection

### 4. Database Security
- **Use** strong database passwords
- **Limit** database user permissions
- **Encrypt** sensitive data at rest
- **Regular** database backups

---

## 📊 Monitoring & Maintenance

### Daily Tasks
- [ ] Check CoinPayments dashboard for new transactions
- [ ] Review Laravel logs for errors
- [ ] Monitor IPN webhook success rate
- [ ] Verify payment completion rates

### Weekly Tasks
- [ ] Review failed transactions
- [ ] Check CoinPayments account balance
- [ ] Monitor system performance
- [ ] Review security logs

### Monthly Tasks
- [ ] Update CoinPayments API keys
- [ ] Review and update security policies
- [ ] Analyze payment trends
- [ ] Update documentation

---

## 🆘 Support & Resources

### CoinPayments Support
- **Documentation**: [https://www.coinpayments.net/apidoc](https://www.coinpayments.net/apidoc)
- **Support Email**: support@coinpayments.net
- **Community Forum**: [https://support.coinpayments.net](https://support.coinpayments.net)

### Laravel Support
- **Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Community**: [https://laracasts.com](https://laracasts.com)

### Emergency Contacts
- **Technical Issues**: Your IT team
- **CoinPayments Issues**: support@coinpayments.net
- **System Admin**: Your system administrator

---

## ✅ Success Checklist

Before going live with CoinPayments:

- [ ] Account created and verified
- [ ] API keys generated and configured
- [ ] IPN webhook configured and tested
- [ ] Database migration completed
- [ ] Admin panel configured
- [ ] Security settings applied
- [ ] Test payment completed successfully
- [ ] IPN webhook verified working
- [ ] Production environment configured
- [ ] Monitoring and logging enabled
- [ ] Support contacts documented
- [ ] Team trained on system

---

## 🎉 Congratulations!

You've successfully configured CoinPayments for Net On You! 

**Next Steps**:
1. **Test** thoroughly with small amounts
2. **Monitor** system performance
3. **Train** your team on the new system
4. **Go live** with confidence!

**Remember**: Always test in a staging environment first, and never use real money for testing unless absolutely necessary.

---

**Document Version**: 1.0  
**Last Updated**: January 2025  
**For Support**: Contact your system administrator or CoinPayments support

