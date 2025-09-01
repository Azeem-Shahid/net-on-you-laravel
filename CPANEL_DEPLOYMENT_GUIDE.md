# 🚀 cPanel Deployment Guide for Net On You

## Prerequisites

- cPanel hosting account with PHP 8.2+ support
- MySQL database
- SSH access (preferred) or File Manager access
- Domain or subdomain configured

## 📦 Step 1: Prepare Files for Upload

### 1.1 Create Deployment Archive
```bash
# Create a clean deployment package
tar -czf net-on-you-deploy.tar.gz \
  --exclude=node_modules \
  --exclude=.git \
  --exclude=storage/logs/* \
  --exclude=storage/framework/cache/* \
  --exclude=storage/framework/sessions/* \
  --exclude=storage/framework/views/* \
  --exclude=.env \
  .
```

### 1.2 Files to Upload
- All project files (excluding development files)
- Custom `.env` file for production
- Database backup/migration files

## 🌐 Step 2: cPanel Setup

### 2.1 Database Setup
1. **Create MySQL Database**
   - Go to cPanel → MySQL Databases
   - Create database: `yourdomain_netonyou`
   - Create user: `yourdomain_netuser`
   - Grant all privileges to user on database

### 2.2 Domain Configuration
1. **Set Document Root**
   - Point domain to `/public_html/net-on-you/public`
   - Or create subdomain: `app.yourdomain.com` → `/public_html/net-on-you/public`

### 2.3 PHP Configuration
1. **Set PHP Version**
   - Go to cPanel → MultiPHP Manager
   - Set PHP version to 8.2 or higher

2. **PHP Extensions** (ensure enabled)
   - `mysqli`
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`
   - `bcmath`

## 📂 Step 3: File Upload & Setup

### 3.1 Upload Files
```bash
# Via SSH
cd /home/yourusername/public_html
mkdir net-on-you
cd net-on-you
wget https://yourtempdomain.com/net-on-you-deploy.tar.gz
tar -xzf net-on-you-deploy.tar.gz
rm net-on-you-deploy.tar.gz
```

### 3.2 Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
```

### 3.3 Environment Configuration
Create `.env` file:
```bash
APP_NAME="Net On You"
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourdomain_netonyou
DB_USERNAME=yourdomain_netuser
DB_PASSWORD=your_database_password

# Email Configuration (use cPanel email or external SMTP)
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"

# CoinPayments Configuration
COINPAYMENTS_MERCHANT_ID=your_merchant_id
COINPAYMENTS_PUBLIC_KEY=your_public_key
COINPAYMENTS_PRIVATE_KEY=your_private_key
COINPAYMENTS_IPN_SECRET=your_ipn_secret
COINPAYMENTS_CURRENCY2=USDT.TRC20
COINPAYMENTS_IPN_URL=https://yourdomain.com/payments/coinpayments/ipn
COINPAYMENTS_ENABLED=true
COINPAYMENTS_SANDBOX=false

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache Configuration
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

## 🗄️ Step 4: Database Setup

### 4.1 Generate Application Key
```bash
cd /home/yourusername/public_html/net-on-you
php artisan key:generate
```

### 4.2 Run Migrations
```bash
php artisan migrate --force
```

### 4.3 Seed Database
```bash
php artisan db:seed --force
```

### 4.4 Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ⏰ Step 5: Cron Jobs Setup

### 5.1 Laravel Scheduler
Add this to cPanel → Cron Jobs:

**Frequency:** Every Minute (`* * * * *`)
**Command:**
```bash
cd /home/yourusername/public_html/net-on-you && php artisan schedule:run >> /dev/null 2>&1
```

### 5.2 Manual Cron Jobs (Alternative)
If Laravel scheduler doesn't work, add these individual crons:

**Monthly Commission Processing (1st of each month at 2 AM):**
```bash
0 2 1 * * cd /home/yourusername/public_html/net-on-you && php artisan commissions:process-monthly >> /home/yourusername/logs/commissions.log 2>&1
```

**Daily Subscription Check (Daily at 1 AM):**
```bash
0 1 * * * cd /home/yourusername/public_html/net-on-you && php artisan subscriptions:check-expiry >> /home/yourusername/logs/subscriptions.log 2>&1
```

**Weekly System Cleanup (Sundays at 3 AM):**
```bash
0 3 * * 0 cd /home/yourusername/public_html/net-on-you && php artisan system:cleanup >> /home/yourusername/logs/cleanup.log 2>&1
```

**Daily Database Backup (Daily at 4 AM):**
```bash
0 4 * * * cd /home/yourusername/public_html/net-on-you && php artisan system:backup-database >> /home/yourusername/logs/backup.log 2>&1
```

## 🔧 Step 6: Testing & Verification

### 6.1 Basic Tests
1. **Website Access:** Visit your domain
2. **Admin Login:** `https://yourdomain.com/admin`
3. **User Registration:** Test user registration
4. **Email:** Test email functionality
5. **Payment:** Test CoinPayments integration

### 6.2 Cron Job Testing
```bash
# Test manual command execution
cd /home/yourusername/public_html/net-on-you
php artisan system:health-check
```

### 6.3 Log Monitoring
Create logs directory:
```bash
mkdir -p /home/yourusername/logs
chmod 755 /home/yourusername/logs
```

Monitor logs:
```bash
tail -f /home/yourusername/logs/commissions.log
tail -f storage/logs/laravel.log
```

## 🛡️ Step 7: Security & Maintenance

### 7.1 Security Setup
1. **SSL Certificate:** Enable SSL in cPanel
2. **File Permissions:** Verify secure permissions
3. **Database Access:** Restrict database access
4. **Hide .env:** Ensure `.env` is not web-accessible

### 7.2 Regular Maintenance
1. **Log Rotation:** Set up log rotation for storage/logs
2. **Database Backup:** Regular database backups
3. **File Backup:** Regular full site backups
4. **Updates:** Keep dependencies updated

## 📧 Step 8: Email Configuration

### 8.1 cPanel Email Setup
1. **Create Email Account:** `noreply@yourdomain.com`
2. **SMTP Settings:**
   - Host: `mail.yourdomain.com`
   - Port: `587` (TLS) or `465` (SSL)
   - Authentication: Yes

### 8.2 External SMTP (Recommended)
Use services like:
- SendGrid
- Mailgun
- Amazon SES
- Gmail SMTP

## 🚨 Troubleshooting

### Common Issues

**1. Permission Errors**
```bash
chmod -R 755 storage bootstrap/cache
```

**2. Route Cache Issues**
```bash
php artisan route:clear
php artisan config:clear
```

**3. Database Connection**
- Check `.env` database settings
- Verify database user permissions
- Test MySQL connection

**4. Cron Jobs Not Running**
- Check cron job logs
- Verify PHP path: `which php`
- Test manual execution

**5. Email Not Working**
- Verify SMTP settings
- Check email logs
- Test with external SMTP

## 📞 Support Commands

```bash
# Check application status
php artisan about

# Clear all caches
php artisan optimize:clear

# Check scheduled commands
php artisan schedule:list

# Test specific command
php artisan system:health-check

# View logs
tail -f storage/logs/laravel.log
```

## ✅ Final Checklist

- [ ] Domain/subdomain configured
- [ ] Database created and configured
- [ ] Files uploaded with correct permissions
- [ ] .env file configured for production
- [ ] Migrations run successfully
- [ ] Database seeded
- [ ] Application optimized (cached)
- [ ] Cron jobs configured
- [ ] SSL certificate installed
- [ ] Email functionality tested
- [ ] Payment gateway tested
- [ ] Admin panel accessible
- [ ] User registration working
- [ ] Backup system in place

## 🎯 Post-Deployment

1. **Test all functionality thoroughly**
2. **Monitor error logs for first 24-48 hours**
3. **Set up monitoring/alerting**
4. **Document any custom configurations**
5. **Train admins on the system**

Your Net On You platform is now ready for production use! 🎉
