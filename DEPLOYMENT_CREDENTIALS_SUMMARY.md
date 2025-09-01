# 🚀 Net On You - Deployment Credentials & Instructions

## 📋 Project Overview
**Project Name:** Net On You  
**Deployment Package:** `net-on-you-deploy-latest.zip` (31MB)  
**Created:** August 30, 2025  
**Database Status:** ✅ Freshly seeded with sample data  

## 🔐 Admin Credentials

### Super Admin (Full Access)
- **Email:** `superadmin@example.com`
- **Password:** `admin123`
- **Role:** `super_admin`
- **Status:** `active`
- **Access:** Complete system access

### Content Admin (Editor)
- **Email:** `content@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `active`
- **Access:** Content management, magazines, articles

### User Admin (Editor)
- **Email:** `useradmin@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `active`
- **Access:** User management, profiles, subscriptions

### Financial Admin (Accountant)
- **Email:** `finance@example.com`
- **Password:** `admin123`
- **Role:** `accountant`
- **Status:** `active`
- **Access:** Financial reports, transactions, commissions

### Support Admin (Editor)
- **Email:** `support@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `active`
- **Access:** User support, ticket management

### Moderator (Editor)
- **Email:** `moderator@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `active`
- **Access:** Content moderation, user management

### Inactive Admin (Editor)
- **Email:** `inactive@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `inactive`
- **Access:** Limited (account disabled)

### Blocked Admin (Editor)
- **Email:** `blocked@example.com`
- **Password:** `admin123`
- **Role:** `editor`
- **Status:** `inactive`
- **Access:** Blocked (account suspended)

## 👥 User Credentials

### Premium Users (Active Subscriptions)
- **John Doe:** `john@example.com` / `password123` (Premium Plan)
- **Jane Smith:** `jane@example.com` / `password123` (Basic Plan)
- **Bob Wilson:** `bob@example.com` / `password123` (Premium Plan, Spanish)
- **Maria Garcia:** `maria@example.com` / `password123` (Premium Plan, Spanish)
- **Pierre Dubois:** `pierre@example.com` / `password123` (Premium Plan, French)
- **Hans Mueller:** `hans@example.com` / `password123` (Premium Plan, German)

### Users with Expired Subscriptions
- **Alice Johnson:** `alice@example.com` / `password123` (Premium Plan, Expired)
- **Charlie Brown:** `charlie@example.com` / `password123` (Basic Plan, Expired)

### Unverified Users
- **David Lee:** `david@example.com` / `password123` (No subscription)
- **Emma Davis:** `emma@example.com` / `password123` (No subscription, German)

### Referral System Users
- **Referrer User:** `referrer@example.com` / `password123` (Premium Plan)
- **Referred User 1:** `referred1@example.com` / `password123` (Basic Plan)
- **Referred User 2:** `referred2@example.com` / `password123` (Basic Plan)

### Blocked User
- **Frank Miller:** `frank@example.com` / `password123` (Premium Plan, Blocked)

## 🌐 Multi-Language Support
- **English (en):** Default language
- **Spanish (es):** Bob Wilson, Maria Garcia
- **French (fr):** Charlie Brown, Pierre Dubois
- **German (de):** Emma Davis, Hans Mueller

## 📚 Sample Content
- **11 Magazines** in various categories (Technology, Business)
- **Multiple subscription plans** (Basic, Premium, Pro, Annual)
- **Referral system** with commission tracking
- **Email templates** for various notifications
- **Security policies** and audit logs

## 🚀 Deployment Instructions

### 1. Upload to cPanel
1. Login to your cPanel
2. Go to File Manager
3. Navigate to `public_html` folder
4. Upload `net-on-you-deploy-latest.zip`
5. Extract the ZIP file

### 2. Run Deployment Commands
```bash
# SSH into your cPanel or use Terminal in cPanel
cd public_html

# Make deployment script executable
chmod +x deploy.sh

# Run the deployment script
./deploy.sh
```

### 3. Manual Deployment (Alternative)
```bash
# Navigate to project directory
cd public_html

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 644 storage/logs/
chmod -R 644 storage/framework/cache/
chmod -R 644 storage/framework/sessions/
chmod -R 644 storage/framework/views/

# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
php artisan migrate --force

# Run database seeders
php artisan db:seed --force

# Optimize the application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ⚙️ Configuration Requirements

### 1. Environment File
1. Copy `env.example` to `.env`
2. Update database credentials
3. Set your domain in `APP_URL`
4. Generate application key

### 2. Database Setup
1. Create MySQL database in cPanel
2. Update `.env` file with database credentials
3. Run migrations and seeders

### 3. Domain Configuration
1. Point your domain to the `public_html` folder
2. Ensure SSL certificate is installed
3. Update `APP_URL` in `.env` file

## 🔧 Important Notes

### Security
- **Change default passwords** after first login
- **Enable 2FA** for admin accounts
- **Review security policies** in admin panel
- **Monitor audit logs** regularly

### Performance
- **Enable OPcache** in PHP settings
- **Use Redis** for caching (if available)
- **Enable Gzip compression**
- **Optimize images** and assets

### Maintenance
- **Regular backups** using SystemBackupDatabase command
- **Monitor cron jobs** for automated tasks
- **Check logs** in `storage/logs/`
- **Update dependencies** regularly

## 📱 Post-Deployment Checklist

- [ ] Test admin panel access with superadmin account
- [ ] Verify user registration and login
- [ ] Test magazine access and subscriptions
- [ ] Check payment system functionality
- [ ] Verify referral system
- [ ] Test multi-language support
- [ ] Check email functionality
- [ ] Verify commission calculations
- [ ] Test security features
- [ ] Review audit logs

## 🆘 Troubleshooting

### Common Issues
- **500 Error:** Check file permissions and `.env` configuration
- **Database Connection:** Verify database credentials and MySQL service
- **White Page:** Check Laravel logs in `storage/logs/`
- **Permission Denied:** Run `chmod` commands for storage and cache folders

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

## 📞 Support Information

- **Project:** Net On You
- **Framework:** Laravel 10.x
- **Database:** MySQL/SQLite
- **Frontend:** Blade templates with Tailwind CSS
- **Payment:** CoinPayments integration
- **Multi-language:** Built-in translation system

## 🎯 Quick Start

1. **Upload** `net-on-you-deploy-latest.zip` to cPanel
2. **Extract** in `public_html` folder
3. **Run** `./deploy.sh` script
4. **Login** with `superadmin@example.com` / `admin123`
5. **Configure** your domain and database
6. **Test** all functionality
7. **Change** default passwords

---

**⚠️ IMPORTANT:** Change all default passwords immediately after deployment for security!

**📅 Created:** August 30, 2025  
**🔑 Package:** `net-on-you-deploy-latest.zip` (31MB)  
**✅ Status:** Ready for deployment
