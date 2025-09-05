# 🚀 Net On You - Installation Commands v2

## 📦 After Uploading ZIP File

### 1. Extract Files
```bash
# Extract the ZIP file
unzip net_on_you_deploy_v2_*.zip

# Move to your web directory (usually public_html)
mv net_on_you_deploy_v2_*/* /path/to/your/web/directory/
cd /path/to/your/web/directory/
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# If composer is not available, download it first:
# curl -sS https://getcomposer.org/installer | php
# php composer.phar install --optimize-autoloader --no-dev
```

### 3. Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public
```

### 4. Generate Application Key
```bash
# Generate Laravel application key
php artisan key:generate
```

### 5. Clear and Cache Configurations
```bash
# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Run Database Migrations
```bash
# Run database migrations
php artisan migrate --force
```

### 7. Update Database Settings
Edit `.env` file and update these REQUIRED settings:
```bash
# Database settings (REQUIRED)
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Email settings (REQUIRED)
MAIL_PASSWORD=your_email_password
```

### 8. Test Installation
```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Run complete test suite
php test_coinpayments_complete.php
```

## 🔧 cPanel Specific Commands

If using cPanel, run these commands in cPanel Terminal:

```bash
# Navigate to your project directory
cd public_html/your-project-folder

# Run the cPanel installation script
chmod +x cpanel_install.sh
./cpanel_install.sh
```

## 📧 Email Configuration

The email is pre-configured for user.netonyou.com:

1. **Create email account in cPanel**: `noreply@user.netonyou.com`
2. **Update password in .env**: `MAIL_PASSWORD=your_email_password`

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://user.netonyou.com/payments/coinpayments/ipn`

2. **Test with Small Amounts**:
   - Create test transaction with $1.00
   - Monitor logs for IPN notifications

## 🧪 Testing Commands

```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Test payment flow
./test_payment_flow.sh

# Test complete functionality
php test_coinpayments_complete.php
```

## 📊 Monitoring Commands

```bash
# Watch logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# Check database
php artisan tinker --execute="DB::connection()->getPdo();"
```

## ✅ Production Checklist

Before going live:
- [ ] All files uploaded and extracted
- [ ] Dependencies installed
- [ ] Permissions set correctly
- [ ] Application key generated
- [ ] Caches cleared and rebuilt
- [ ] Database migrations run
- [ ] Database settings updated in .env
- [ ] Email password updated in .env
- [ ] CoinPayments IPN URL updated
- [ ] Email configuration tested
- [ ] All tests passing
- [ ] Test with small amounts

## 🎉 Ready to Go Live!

Your Net On You application with CoinPayments integration is ready for production!

**Features Included:**
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email notifications
- ✅ User management
- ✅ Payment processing
- ✅ Database integration
- ✅ Production optimizations
- ✅ Pre-configured for user.netonyou.com
