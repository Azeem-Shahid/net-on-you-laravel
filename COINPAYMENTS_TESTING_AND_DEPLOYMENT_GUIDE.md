# 🚀 CoinPayments Testing & Production Deployment Guide

## 📋 Table of Contents
1. [Currency Configuration](#currency-configuration)
2. [Local Testing](#local-testing)
3. [Staging Testing](#staging-testing)
4. [Production Deployment](#production-deployment)
5. [Testing Procedures](#testing-procedures)
6. [Monitoring & Troubleshooting](#monitoring--troubleshooting)

---

## 💰 Currency Configuration

### Supported Currencies
Your system now supports:
- **USDT.TRC20**: Tether (TRON Network) - Primary
- **USDT.ERC20**: Tether (Ethereum Network)
- **USDC.TRC20**: USD Coin (TRON Network)
- **USDC.ERC20**: USD Coin (Ethereum Network)
- **BTC**: Bitcoin
- **ETH**: Ethereum

### Default Currency Setting
Current default: `USDT.TRC20`
To change the default currency, update your `.env` file:
```bash
COINPAYMENTS_CURRENCY2="USDC.TRC20"  # or any other supported currency
```

---

## 🧪 Local Testing

### Step 1: Environment Setup
```bash
# Ensure your local environment is running
php artisan serve

# Check if CoinPayments is enabled
php artisan tinker
>>> config('services.coinpayments.enabled')
>>> exit
```

### Step 2: Test Configuration
```bash
# Run configuration test
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$service = new App\Services\CoinPaymentsService();
echo 'Enabled: ' . (\$service->isEnabled() ? 'Yes' : 'No') . PHP_EOL;
echo 'Merchant ID: ' . config('services.coinpayments.merchant_id') . PHP_EOL;
echo 'Currency: ' . config('services.coinpayments.currency2') . PHP_EOL;
"
```

### Step 3: Test Payment Flow (Local)
1. **Access Payment Page**:
   - Go to `http://localhost:8000/payment/checkout`
   - Select "Crypto Payment" option
   - Choose your preferred currency (USDT/USDC)

2. **Initiate Transaction**:
   - Fill in payment details
   - Submit the form
   - Check if transaction is created in database

3. **Monitor Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Step 4: Test IPN Endpoint (Local)
```bash
# Test IPN endpoint accessibility
curl -X POST http://localhost:8000/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "test=1"
```

---

## 🏗️ Staging Testing

### Step 1: Deploy to Staging Server
```bash
# Update .env for staging
APP_ENV=staging
APP_URL=https://staging.yourdomain.com
COINPAYMENTS_IPN_URL="https://staging.yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=true  # Use sandbox if available
```

### Step 2: Configure CoinPayments for Staging
1. **In CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://staging.yourdomain.com/payments/coinpayments/ipn`
   - Enable sandbox mode if available

### Step 3: Test with Real CoinPayments (Small Amounts)
1. **Create Test Transaction**:
   - Use staging environment
   - Test with $1.00 amount
   - Use USDT.TRC20 or USDC.TRC20

2. **Monitor Transaction**:
   - Check transaction status in database
   - Verify IPN notifications received
   - Confirm status updates

---

## 🚀 Production Deployment

### Step 1: Production Environment Setup
```bash
# Update .env for production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=false
```

### Step 2: Server Requirements
- **PHP 8.1+** with required extensions
- **SSL Certificate** (HTTPS required for IPN)
- **Database** (MySQL/PostgreSQL)
- **Web Server** (Apache/Nginx)

### Step 3: Deployment Commands
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Clear and cache configurations
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 4: Configure CoinPayments for Production
1. **In CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://yourdomain.com/payments/coinpayments/ipn`
   - Disable sandbox mode
   - Verify all API keys are production keys

### Step 5: SSL and Security
```bash
# Ensure SSL is properly configured
# Test SSL certificate
curl -I https://yourdomain.com

# Test IPN endpoint with SSL
curl -X POST https://yourdomain.com/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "test=1"
```

---

## 🧪 Testing Procedures

### 1. Pre-Production Testing Checklist

#### Configuration Tests
- [ ] All environment variables set correctly
- [ ] CoinPayments service enabled
- [ ] IPN URL accessible via HTTPS
- [ ] Database connections working
- [ ] Log files writable

#### Payment Flow Tests
- [ ] Payment page loads correctly
- [ ] Currency selection works (USDT/USDC)
- [ ] Transaction creation successful
- [ ] Database records created
- [ ] IPN endpoint responds correctly

#### Security Tests
- [ ] HMAC signature verification working
- [ ] Invalid requests rejected
- [ ] SSL certificate valid
- [ ] API keys properly configured

### 2. Live Testing (Small Amounts)

#### Test Transaction Process
1. **Create Test Transaction**:
   ```bash
   # Use your application to create a $1.00 transaction
   # Choose USDT.TRC20 or USDC.TRC20
   ```

2. **Monitor Database**:
   ```sql
   SELECT * FROM transactions 
   WHERE gateway = 'coinpayments' 
   ORDER BY created_at DESC 
   LIMIT 5;
   ```

3. **Check Logs**:
   ```bash
   tail -f storage/logs/laravel.log | grep -i coinpayments
   ```

4. **Verify IPN**:
   - Check if IPN notifications are received
   - Verify transaction status updates
   - Confirm subscription activation

### 3. Currency Testing

#### Test Each Supported Currency
```bash
# Test USDT.TRC20
COINPAYMENTS_CURRENCY2="USDT.TRC20"

# Test USDT.ERC20
COINPAYMENTS_CURRENCY2="USDT.ERC20"

# Test USDC.TRC20
COINPAYMENTS_CURRENCY2="USDC.TRC20"

# Test USDC.ERC20
COINPAYMENTS_CURRENCY2="USDC.ERC20"
```

---

## 📊 Monitoring & Troubleshooting

### 1. Log Monitoring
```bash
# Monitor all CoinPayments activity
tail -f storage/logs/laravel.log | grep -i coinpayments

# Monitor IPN notifications
tail -f storage/logs/laravel.log | grep -i ipn

# Monitor transaction updates
tail -f storage/logs/laravel.log | grep -i transaction
```

### 2. Database Monitoring
```sql
-- Check recent transactions
SELECT id, txn_id, status, amount, currency, created_at 
FROM transactions 
WHERE gateway = 'coinpayments' 
ORDER BY created_at DESC 
LIMIT 10;

-- Check transaction status distribution
SELECT status, COUNT(*) as count 
FROM transactions 
WHERE gateway = 'coinpayments' 
GROUP BY status;
```

### 3. Common Issues & Solutions

#### Issue: IPN Not Received
**Solution**:
- Check IPN URL in CoinPayments dashboard
- Verify SSL certificate is valid
- Check server firewall settings
- Test IPN endpoint manually

#### Issue: Invalid HMAC
**Solution**:
- Verify IPN secret matches configuration
- Check if IPN secret has special characters
- Ensure proper encoding

#### Issue: Transaction Not Found
**Solution**:
- Check if txn_id is properly stored
- Verify database connection
- Check transaction table structure

#### Issue: Status Not Updated
**Solution**:
- Check IPN processing logic
- Verify database transaction handling
- Check for PHP errors in logs

### 4. Performance Monitoring
```bash
# Monitor server resources
htop

# Check database performance
mysql -e "SHOW PROCESSLIST;"

# Monitor web server logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

---

## 🎯 Production Readiness Checklist

### Before Going Live
- [ ] All tests passed in staging
- [ ] SSL certificate installed and valid
- [ ] IPN URL configured in CoinPayments
- [ ] Database backups configured
- [ ] Monitoring systems in place
- [ ] Error logging configured
- [ ] Performance optimized

### Post-Deployment
- [ ] Test with small amounts first
- [ ] Monitor logs for 24 hours
- [ ] Verify all payment flows work
- [ ] Check transaction processing
- [ ] Confirm subscription activation
- [ ] Test customer support procedures

---

## 🚀 Quick Deployment Commands

### For cPanel/Shared Hosting
```bash
# Upload files via cPanel File Manager or FTP
# Update .env file with production values
# Run via cPanel Terminal or SSH

php artisan config:clear
php artisan config:cache
php artisan migrate --force
```

### For VPS/Dedicated Server
```bash
# Clone repository
git clone your-repo-url
cd your-project

# Install dependencies
composer install --optimize-autoloader --no-dev

# Configure environment
cp .env.example .env
# Edit .env with production values

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

---

## 📞 Support & Maintenance

### Regular Maintenance
- Monitor transaction success rates
- Check for failed IPN notifications
- Review error logs weekly
- Update dependencies monthly
- Backup database daily

### Emergency Procedures
- Keep CoinPayments support contact handy
- Have rollback plan ready
- Monitor server resources
- Test payment flow regularly

---

## ✅ Final Checklist

Before declaring production ready:
- [ ] All currencies tested (USDT/USDC)
- [ ] IPN endpoint working
- [ ] Transaction processing verified
- [ ] Security measures in place
- [ ] Monitoring configured
- [ ] Backup procedures ready
- [ ] Support procedures documented

**Your CoinPayments integration is now ready for production deployment!** 🎉

