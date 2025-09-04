# Net On You - Complete cPanel Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying Net On You with comprehensive dummy data to your cPanel hosting account.

## Prerequisites
- cPanel hosting account with PHP 8.1+ support
- MySQL database access
- Terminal access in cPanel
- File Manager access

## Step 1: Database Setup

### 1.1 Create Database
1. Login to cPanel
2. Go to "MySQL Databases"
3. Create a new database: `yourdomain_netonyou`
4. Create a database user: `yourdomain_netuser`
5. Assign user to database with ALL PRIVILEGES
6. Note down the database credentials

### 1.2 Import Database
1. Go to cPanel File Manager
2. Navigate to your domain's public_html directory
3. Upload the database file: `net_on_you_database_*.sql`
4. Open cPanel Terminal
5. Run the import command:

```bash
# Navigate to public_html
cd public_html

# Import database (replace with your actual database name)
mysql -u yourdomain_netuser -p yourdomain_netonyou < net_on_you_database_*.sql
```

## Step 2: File Upload

### 2.1 Upload Application Files
1. Upload the ZIP file to public_html
2. Extract all files to public_html directory
3. Ensure all files are in the root of public_html

### 2.2 Set File Permissions
Run these commands in cPanel Terminal:

```bash
# Navigate to public_html
cd public_html

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set special permissions for Laravel
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan
```

## Step 3: Environment Configuration

### 3.1 Create .env File
1. Copy .env.example to .env
2. Update the following settings in .env:

```env
APP_NAME="Net On You"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourdomain_netonyou
DB_USERNAME=yourdomain_netuser
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.2 Generate Application Key
Run in Terminal:

```bash
php artisan key:generate
```

## Step 4: Install Dependencies

### 4.1 Install PHP Dependencies
```bash
# Install Composer dependencies
composer install --optimize-autoloader --no-dev
```

### 4.2 Install Node.js Dependencies (if needed)
```bash
# Install Node.js dependencies
npm install

# Build assets
npm run build
```

## Step 5: Laravel Setup

### 5.1 Run Migrations
```bash
# Run database migrations
php artisan migrate --force
```

### 5.2 Clear and Cache
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 6: Cron Jobs Setup

### 6.1 Add Cron Jobs in cPanel
1. Go to "Cron Jobs" in cPanel
2. Add these cron jobs:

```bash
# Process payments every 6 hours
0 */6 * * * cd /home/your_username/public_html && php artisan payments:process

# Calculate commissions daily at midnight
0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate

# Send email notifications every 2 hours
0 */2 * * * cd /home/your_username/public_html && php artisan emails:send

# Generate reports daily
0 1 * * * cd /home/your_username/public_html && php artisan reports:generate

# Clean up old logs weekly
0 2 * * 0 cd /home/your_username/public_html && php artisan logs:cleanup
```

## Step 7: Security Configuration

### 7.1 Set Up SSL Certificate
1. Enable SSL in cPanel
2. Force HTTPS redirects
3. Update APP_URL to use https://

### 7.2 Configure .htaccess
Ensure your .htaccess file includes:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Step 8: Testing

### 8.1 Test Admin Access
1. Visit: `https://yourdomain.com/admin/login`
2. Login with:
   - Email: `admin@netonyou.com`
   - Password: `admin123`

### 8.2 Test User 1 Referral Network
1. Visit: `https://yourdomain.com/login`
2. Login with:
   - Email: `alex.johnson@example.com`
   - Password: `password123`
3. Check referral dashboard for extensive referral data

### 8.3 Test Features
- User registration and login
- Subscription management
- Referral system
- Payment processing
- Email notifications
- Multi-language support

## Step 9: Performance Optimization

### 9.1 Enable OPcache
Add to your .env file:
```env
OPCACHE_ENABLE=true
```

### 9.2 Configure Redis (if available)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 9.3 Set Up CDN
Configure your CDN to serve static assets from:
- `/public/css/`
- `/public/js/`
- `/public/images/`

## Step 10: Monitoring and Maintenance

### 10.1 Set Up Log Monitoring
Monitor these log files:
- `storage/logs/laravel.log`
- `storage/logs/payment.log`
- `storage/logs/email.log`

### 10.2 Regular Maintenance
Run these commands weekly:

```bash
# Clear old logs
php artisan logs:cleanup

# Optimize database
php artisan optimize

# Clear expired sessions
php artisan session:gc
```

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
- Check file permissions
- Verify .env configuration
- Check Laravel logs

#### 2. Database Connection Error
- Verify database credentials
- Check database server status
- Ensure database exists

#### 3. Cron Jobs Not Running
- Verify cron job syntax
- Check cron job logs
- Ensure PHP path is correct

#### 4. Email Not Sending
- Verify SMTP settings
- Check email logs
- Test with simple email

### Support Commands

```bash
# Check Laravel status
php artisan about

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check queue status
php artisan queue:work --once

# Test email sending
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

## Dummy Data Overview

The deployment includes comprehensive dummy data:

### User 1 Referral Network (Alex Johnson)
- **Email**: alex.johnson@example.com
- **Password**: password123
- **Referral Code**: ALEX2024
- **10 Level 1 referrals** with active subscriptions
- **15 Level 2 referrals** with commission tracking
- **Complete transaction history**
- **Commission calculations** for all levels

### Additional Test Users
- Various subscription statuses (active, expired, cancelled)
- Multiple payment methods (credit card, crypto, PayPal)
- Different languages (English, Spanish, French, German)
- Complete referral relationships

### System Data
- **25+ magazines** in multiple languages
- **Email templates** for all notifications
- **Commission tracking** with payout batches
- **Admin accounts** with different roles
- **Security policies** and audit logs
- **Scheduled commands** and system reports

## Admin Access

### Super Admin
- **URL**: https://yourdomain.com/admin/login
- **Email**: admin@netonyou.com
- **Password**: admin123

### Content Manager
- **Email**: content@netonyou.com
- **Password**: content123

### Support Manager
- **Email**: support@netonyou.com
- **Password**: support123

## Quick Commands Reference

```bash
# Deploy script (run after file upload)
./cpanel_deploy.sh

# Check system status
php artisan about

# Clear all caches
php artisan optimize:clear

# Run specific seeder
php artisan db:seed --class=EnhancedDummyDataSeeder

# Check cron jobs
crontab -l

# View logs
tail -f storage/logs/laravel.log
```

## Support

For technical support:
1. Check the documentation in the `delivery/` folder
2. Review Laravel logs in `storage/logs/`
3. Verify all configurations in `.env`
4. Test individual components using artisan commands

---

**Deployment completed successfully!** Your Net On You platform is now ready with comprehensive dummy data including User 1's extensive referral network.



