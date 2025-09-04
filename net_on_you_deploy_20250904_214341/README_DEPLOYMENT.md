# 🚀 Net On You - Deployment Package

## 📋 What's Included

This package contains everything needed to deploy Net On You with CoinPayments integration:

- ✅ Complete Laravel application
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email configuration
- ✅ Database migrations
- ✅ Installation scripts
- ✅ Test scripts
- ✅ Production configuration

## 🚀 Quick Installation

### 1. Upload Files
Upload all files to your hosting directory (usually `public_html/` or subdirectory)

### 2. Run Installation
```bash
# Make installation script executable
chmod +x install.sh

# Run installation
./install.sh
```

### 3. Configure Environment
```bash
# Copy production template
cp .env.production .env

# Edit .env file with your settings
nano .env
```

### 4. Update Required Settings
Replace these placeholders in .env:
- `YOUR_DOMAIN.com` → Your actual domain
- `YOUR_CPANEL_DATABASE_NAME` → Your cPanel database name
- `YOUR_CPANEL_DATABASE_USER` → Your cPanel database user
- `YOUR_CPANEL_DATABASE_PASSWORD` → Your database password
- `YOUR_EMAIL_PASSWORD` → Your email password

### 5. Test Installation
```bash
# Run integration test
php test_coinpayments_integration.php

# Run complete test
php test_coinpayments_complete.php
```

## 🔧 Manual Installation Commands

If the installation script doesn't work, run these commands manually:

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 755 storage bootstrap/cache

# Generate app key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

## 📧 Email Configuration

Choose one email option in .env:

### Option A: cPanel Email (Recommended)
```bash
MAIL_MAILER=smtp
MAIL_HOST=yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
```

### Option B: Gmail SMTP
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://yourdomain.com/payments/coinpayments/ipn`

2. **Test with Small Amounts**:
   - Create test transaction with $1.00
   - Monitor logs for IPN notifications

## 🧪 Testing

```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Test payment flow
./test_payment_flow.sh

# Test complete functionality
php test_coinpayments_complete.php
```

## 📊 Monitoring

```bash
# Watch logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# Check database
php artisan tinker --execute="DB::connection()->getPdo();"
```

## 🆘 Troubleshooting

### Common Issues:
1. **500 Error**: Check file permissions
2. **Database Error**: Verify .env database settings
3. **Email Not Sending**: Check SMTP configuration
4. **CoinPayments Not Working**: Check IPN URL

### Debug Commands:
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test database
php artisan tinker --execute="DB::connection()->getPdo();"

# Test email
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('test@example.com')->subject('Test'); });"
```

## ✅ Production Checklist

Before going live:
- [ ] All files uploaded
- [ ] Installation completed
- [ ] .env file configured
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
