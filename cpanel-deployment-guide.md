# cPanel Deployment Guide for Laravel Net On You

## 🚀 Quick Deployment Steps

### 1. Create Deployment ZIP
```bash
# Run this command in your project root
zip -r net-on-you-deploy.zip . -x "*.git*" "node_modules/*" "storage/logs/*" "storage/framework/cache/*" "storage/framework/sessions/*" "storage/framework/views/*" ".env" "tests/*" "vendor/*"
```

### 2. Upload to cPanel
1. Login to your cPanel
2. Go to File Manager
3. Navigate to `public_html` folder
4. Upload the `net-on-you-deploy.zip` file
5. Extract the ZIP file

### 3. Run Deployment Commands
```bash
# SSH into your cPanel or use Terminal in cPanel
cd public_html

# Make deployment script executable
chmod +x deploy.sh

# Run the deployment script
./deploy.sh
```

## 📋 Manual Deployment Commands

If you prefer to run commands manually:

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

## ⚙️ Configuration

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

## 🔧 Troubleshooting

### Common Issues:
- **500 Error**: Check file permissions and `.env` configuration
- **Database Connection**: Verify database credentials and MySQL service
- **White Page**: Check Laravel logs in `storage/logs/`
- **Permission Denied**: Run `chmod` commands for storage and cache folders

### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

## 📱 Post-Deployment

1. Test all major functionality
2. Verify admin panel access
3. Check email functionality
4. Test payment system
5. Verify referral system
6. Check multi-language support

## 🆘 Support

If you encounter issues:
1. Check Laravel logs in `storage/logs/`
2. Verify file permissions
3. Ensure all required PHP extensions are enabled
4. Check cPanel error logs

## 📊 Performance Tips

- Enable OPcache in PHP
- Use Redis for caching (if available)
- Enable Gzip compression
- Optimize images and assets
- Use CDN for static files
