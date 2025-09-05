# 🚀 cPanel Deployment Guide with Email Integration

## 📋 Complete Production Setup for Net On You

This guide will help you deploy your Laravel application with CoinPayments and email integration to cPanel hosting.

---

## 🔧 **Step 1: cPanel Database Setup**

### Create Database in cPanel
1. **Login to cPanel**
2. **Go to MySQL Databases**
3. **Create New Database**:
   - Database Name: `yourdomain_netonyou` (or similar)
   - Note down the full database name (usually `cpanelusername_databasename`)

4. **Create Database User**:
   - Username: `yourdomain_user` (or similar)
   - Password: Generate strong password
   - Note down the full username (usually `cpanelusername_username`)

5. **Add User to Database**:
   - Select the user and database
   - Grant ALL PRIVILEGES
   - Click "Make Changes"

---

## 📧 **Step 2: Email Configuration in cPanel**

### Option A: Use cPanel Email (Recommended)
1. **Go to Email Accounts in cPanel**
2. **Create Email Account**:
   - Email: `noreply@yourdomain.com`
   - Password: Generate strong password
   - Mailbox Quota: 1GB (or as needed)

3. **Note down email credentials** for .env file

### Option B: Use Gmail SMTP (Alternative)
1. **Enable 2-Factor Authentication** on Gmail
2. **Generate App Password**:
   - Go to Google Account Settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"

### Option C: Use Mailgun (Professional)
1. **Sign up at Mailgun.com**
2. **Add your domain**
3. **Get API credentials**

---

## 📁 **Step 3: File Upload to cPanel**

### Upload Files
1. **Compress your project** (excluding vendor, node_modules)
2. **Upload via cPanel File Manager**:
   - Extract to `public_html/` or subdirectory
   - Ensure all files are uploaded

### Set Permissions
```bash
# Set these permissions via cPanel File Manager
storage/ - 755
bootstrap/cache/ - 755
public/ - 755
```

---

## ⚙️ **Step 4: Environment Configuration**

### Create .env File
1. **Copy the production.env file** to `.env`
2. **Update the following values**:

```bash
# Replace YOUR_DOMAIN.com with your actual domain
APP_URL=https://yourdomain.com

# Database credentials from Step 1
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Email configuration from Step 2
MAIL_HOST=yourdomain.com
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password

# CoinPayments IPN URL
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
```

---

## 🗄️ **Step 5: Database Migration**

### Via cPanel Terminal
1. **Open cPanel Terminal**
2. **Navigate to your project**:
   ```bash
   cd public_html/your-project-folder
   ```

3. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

4. **Seed database** (if needed):
   ```bash
   php artisan db:seed --force
   ```

---

## 🚀 **Step 6: Laravel Optimization**

### Run Optimization Commands
```bash
# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📧 **Step 7: Email Testing**

### Test Email Configuration
1. **Create test email**:
   ```bash
   php artisan tinker
   Mail::raw('Test email from Net On You', function($message) {
       $message->to('your-test-email@gmail.com')->subject('Test Email');
   });
   exit
   ```

2. **Check email delivery**

---

## 💰 **Step 8: CoinPayments Configuration**

### Update CoinPayments Dashboard
1. **Login to CoinPayments.net**
2. **Go to Account Settings → Merchant Settings**
3. **Set IPN URL**: `https://yourdomain.com/payments/coinpayments/ipn`
4. **Save settings**

### Test CoinPayments
1. **Create test transaction** with small amount
2. **Monitor logs** for IPN notifications
3. **Verify transaction processing**

---

## 🔍 **Step 9: Final Testing**

### Test All Features
1. **Website loads correctly**
2. **User registration works**
3. **Payment processing works**
4. **Email notifications sent**
5. **CoinPayments integration works**

### Monitor Logs
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check for errors
grep -i error storage/logs/laravel.log
```

---

## 📋 **Complete .env File for cPanel**

Here's your complete production .env file (replace placeholders):

```bash
# ===========================================
# NET ON YOU - PRODUCTION ENVIRONMENT
# ===========================================

# Application Settings
APP_NAME="Net On You"
APP_ENV=production
APP_KEY=base64:PUP/QOGO9myuUXwzAxAXanWITK3pjHJufcK6nork6qc=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database (cPanel MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Cache & Session
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email (cPanel SMTP)
MAIL_MAILER=smtp
MAIL_HOST=yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"

# Business Logic
COMMISSION_RATE=0.10
REFERRAL_BONUS=5.00
MIN_PAYOUT_AMOUNT=50.00

# CoinPayments Integration
COINPAYMENTS_ENABLED=true
COINPAYMENTS_MERCHANT_ID="82fb593d8bc444d7fd126342665a3068"
COINPAYMENTS_PUBLIC_KEY="5a3e09f1e0aa0059e826cc064ed786f25c3f1e6450543314e88ecd552eeb4ddb"
COINPAYMENTS_PRIVATE_KEY="179f143D3be0d7064791f2A30ec32538fc68eee9B29745B084455D3E531e1265"
COINPAYMENTS_IPN_SECRET="529209"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=false
SUBSCRIPTION_PRICE="39.90"
```

---

## 🛠️ **cPanel Specific Commands**

### Via cPanel Terminal
```bash
# Navigate to project
cd public_html/your-project

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test CoinPayments
php test_coinpayments_integration.php
```

---

## 📊 **Monitoring & Maintenance**

### Regular Checks
1. **Monitor transaction logs**
2. **Check email delivery**
3. **Verify CoinPayments IPN**
4. **Monitor server resources**

### Backup Strategy
1. **Database backup** (via cPanel)
2. **File backup** (via cPanel)
3. **Automated backups** (if available)

---

## 🚨 **Troubleshooting**

### Common Issues
1. **500 Error**: Check file permissions
2. **Database Error**: Verify credentials
3. **Email Not Sending**: Check SMTP settings
4. **CoinPayments Not Working**: Check IPN URL

### Debug Commands
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
DB::connection()->getPdo();
exit

# Test email
php artisan tinker
Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });
exit
```

---

## ✅ **Deployment Checklist**

### Before Going Live
- [ ] Database created and configured
- [ ] Email account created and tested
- [ ] Files uploaded with correct permissions
- [ ] .env file configured correctly
- [ ] Migrations run successfully
- [ ] CoinPayments IPN URL updated
- [ ] All features tested

### After Going Live
- [ ] Test payment processing
- [ ] Verify email notifications
- [ ] Monitor logs for errors
- [ ] Test CoinPayments integration
- [ ] Check transaction processing

---

## 🎉 **You're Ready!**

Your Net On You application with CoinPayments and email integration is now ready for production on cPanel!

**Key Features Deployed:**
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email notifications
- ✅ User management
- ✅ Payment processing
- ✅ Database integration
- ✅ Production optimizations

**Next Steps:**
1. Test with small amounts
2. Monitor for 24 hours
3. Go live with confidence!

