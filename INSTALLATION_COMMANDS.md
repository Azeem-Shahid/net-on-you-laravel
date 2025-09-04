# 🚀 Net On You - Installation Commands

## 📦 After Uploading ZIP File

### 1. Extract Files
```bash
# Extract the ZIP file
unzip net_on_you_deploy_*.zip

# Move to your web directory (usually public_html)
mv net_on_you_deploy_*/* /path/to/your/web/directory/
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

### 4. Configure Environment
```bash
# Copy production template
cp .env.production .env

# Edit .env file with your settings
nano .env
# or use cPanel File Manager to edit .env
```

### 5. Generate Application Key
```bash
# Generate Laravel application key
php artisan key:generate
```

### 6. Clear and Cache Configurations
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

### 7. Run Database Migrations
```bash
# Run database migrations
php artisan migrate --force
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

Add these settings to your .env file:

```bash
# cPanel Email (Recommended)
MAIL_MAILER=smtp
MAIL_HOST=yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"
```

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://yourdomain.com/payments/coinpayments/ipn`

2. **Test with Small Amounts**:
   - Create test transaction with $1.00
   - Monitor logs for IPN notifications

## 🧪 Testing Commands

```bash
# Test service configuration
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$service = new App\Services\CoinPaymentsService();
echo 'Enabled: ' . (\$service->isEnabled() ? 'Yes' : 'No') . PHP_EOL;
"

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"

# Test email configuration
php artisan tinker --execute="
try {
    Mail::raw('Test email', function(\$m) { \$m->to('test@example.com')->subject('Test'); });
    echo 'Email working!';
} catch (Exception \$e) {
    echo 'Email failed: ' . \$e->getMessage();
}
"

# Test IPN endpoint
curl -X POST https://yourdomain.com/payments/coinpayments/ipn -d "test=1"
```

## 📊 Monitoring Commands

```bash
# Watch CoinPayments logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# Watch all logs
tail -f storage/logs/laravel.log

# Check database
php artisan tinker --execute="DB::connection()->getPdo();"
```

## ✅ Production Checklist

Before going live:
- [ ] All files uploaded and extracted
- [ ] Dependencies installed
- [ ] Permissions set correctly
- [ ] .env file configured
- [ ] Application key generated
- [ ] Caches cleared and rebuilt
- [ ] Database migrations run
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
