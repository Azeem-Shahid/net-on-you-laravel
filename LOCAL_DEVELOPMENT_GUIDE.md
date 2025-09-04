# Net On You - Local Development Guide

## 🚀 Quick Start

Your Net On You project is now running locally! Here's everything you need to know:

### ✅ Server Status
- **Status**: ✅ Running
- **URL**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin/login
- **User Panel**: http://localhost:8000/login

## 🔐 Access Credentials

### Admin Access
- **URL**: http://localhost:8000/admin/login
- **Email**: `admin@netonyou.com`
- **Password**: `admin123`

### User Access (User 1 - Alex Johnson)
- **URL**: http://localhost:8000/login
- **Email**: `alex.johnson@example.com`
- **Password**: `password123`

### Additional Test Users
- **Sarah Wilson**: `sarah.wilson@example.com` / `password123`
- **Michael Brown**: `michael.brown@example.com` / `password123`
- **Emma Davis**: `emma.davis@example.com` / `password123`

## 🛠️ Development Commands

### Start the Server
```bash
# Start development server
php artisan serve

# Start on specific host and port
php artisan serve --host=0.0.0.0 --port=8000
```

### Stop the Server
```bash
# Press Ctrl+C in the terminal where the server is running
# Or find the process and kill it
ps aux | grep "php artisan serve"
kill [PID]
```

### Database Commands
```bash
# Run migrations (if needed)
php artisan migrate

# Run seeders (if needed)
php artisan db:seed

# Fresh migration with seeders
php artisan migrate:fresh --seed
```

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Testing the Application

### 1. Main Website
- Visit: http://localhost:8000
- Should show the Net On You homepage

### 2. Admin Panel
- Visit: http://localhost:8000/admin/login
- Login with: `admin@netonyou.com` / `admin123`
- Test all admin features

### 3. User Panel
- Visit: http://localhost:8000/login
- Login with: `alex.johnson@example.com` / `password123`
- Test user features and referral system

### 4. Key Features to Test
- ✅ User registration and login
- ✅ Admin dashboard
- ✅ Referral system
- ✅ Commission tracking
- ✅ Payment processing
- ✅ Email templates
- ✅ Multi-language support
- ✅ Magazine management

## 📁 Project Structure

```
net_on_you/
├── app/                    # Application code
├── config/                 # Configuration files
├── database/              # Migrations, seeders, factories
├── public/                # Web accessible files
├── resources/             # Views, assets, language files
├── routes/                # Route definitions
├── storage/               # Logs, cache, uploaded files
├── vendor/                # Composer dependencies
├── .env                   # Environment configuration
├── artisan                # Laravel command line tool
└── composer.json          # Composer dependencies
```

## 🔧 Environment Configuration

The project is configured for local development with:
- **Database**: SQLite (database/database.sqlite)
- **Cache**: File-based
- **Session**: File-based
- **Mail**: Log driver (emails logged to storage/logs)
- **Debug**: Enabled

## 🐛 Troubleshooting

### Server Won't Start
```bash
# Check if port is in use
netstat -tulpn | grep :8000

# Use different port
php artisan serve --port=8001
```

### Database Issues
```bash
# Check database file
ls -la database/database.sqlite

# Recreate database
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Cache Issues
```bash
# Clear all caches
php artisan optimize:clear
```

## 📊 Features Available

### Admin Features
- ✅ User management
- ✅ Referral system management
- ✅ Commission tracking
- ✅ Payment processing
- ✅ Email template management
- ✅ Magazine management
- ✅ Multi-language support
- ✅ Analytics and reports
- ✅ Security management
- ✅ System settings

### User Features
- ✅ User registration and login
- ✅ Referral system
- ✅ Commission tracking
- ✅ Payment processing
- ✅ Subscription management
- ✅ Magazine access
- ✅ Multi-language support
- ✅ Profile management

## 🎯 Testing Scenarios

### 1. Referral System Test
1. Login as Alex Johnson (`alex.johnson@example.com`)
2. Check referral dashboard
3. View commission earnings
4. Test referral links

### 2. Admin Management Test
1. Login as admin (`admin@netonyou.com`)
2. Create new users
3. Manage referrals
4. Process commissions
5. Send emails

### 3. Payment System Test
1. Test manual payment upload
2. Check payment processing
3. Verify commission calculations
4. Test payout system

## 📝 Development Notes

- **PHP Version**: 8.2.28
- **Laravel Version**: 10.x
- **Database**: SQLite (local development)
- **Server**: Built-in PHP server
- **Debug Mode**: Enabled
- **Logs**: Available in `storage/logs/laravel.log`

## 🚀 Next Steps

1. **Test all features** using the provided credentials
2. **Customize the application** as needed
3. **Add new features** or modify existing ones
4. **Deploy to production** when ready

## 📞 Support

If you encounter any issues:
1. Check the logs in `storage/logs/laravel.log`
2. Verify the server is running on http://localhost:8000
3. Test with the provided credentials
4. Check the troubleshooting section above

---

**Your Net On You application is now running locally and ready for development!** 🎉


