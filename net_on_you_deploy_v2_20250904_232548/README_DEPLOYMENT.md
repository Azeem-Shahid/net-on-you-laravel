# 🚀 Net On You - Deployment Package v2

## 📋 What's Included

This package contains everything needed to deploy Net On You with CoinPayments integration:

- ✅ Complete Laravel application
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email configuration
- ✅ Database migrations
- ✅ Installation scripts
- ✅ Test scripts
- ✅ Production configuration
- ✅ Pre-configured for user.netonyou.com

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

### 3. Update Database Settings
Edit `.env` file and update these settings:
```bash
# Database settings (REQUIRED)
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Email settings (REQUIRED)
MAIL_PASSWORD=your_email_password
```

### 4. Test Installation
```bash
# Run integration test
php test_coinpayments_integration.php

# Run complete test suite
php test_coinpayments_complete.php
```

## 🔧 Pre-Configured Settings

The following settings are already configured for user.netonyou.com:

- ✅ APP_URL=https://user.netonyou.com
- ✅ MAIL_HOST=user.netonyou.com
- ✅ MAIL_FROM_ADDRESS=noreply@user.netonyou.com
- ✅ COINPAYMENTS_IPN_URL=https://user.netonyou.com/payments/coinpayments/ipn
- ✅ All CoinPayments credentials

## 📧 Email Configuration

The email is pre-configured for user.netonyou.com. You only need to:

1. Create email account in cPanel: `noreply@user.netonyou.com`
2. Update `MAIL_PASSWORD` in .env file

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://user.netonyou.com/payments/coinpayments/ipn`

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
- [ ] Database settings updated in .env
- [ ] Email password updated in .env
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
- ✅ Pre-configured for user.netonyou.com
