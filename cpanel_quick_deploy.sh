#!/bin/bash

# Net On You - Quick cPanel Deployment Script
# Run this script in cPanel Terminal after uploading files

echo "=========================================="
echo "Net On You - Quick cPanel Deployment"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo "Setting file permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "Generating application key..."
php artisan key:generate

echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=========================================="
echo "Deployment completed successfully!"
echo "=========================================="
echo ""
echo "Admin Access:"
echo "URL: https://yourdomain.com/admin/login"
echo "Email: admin@netonyou.com"
echo "Password: admin123"
echo ""
echo "User 1 Referral Network:"
echo "Email: alex.johnson@example.com"
echo "Password: password123"
echo "Referral Code: ALEX2024"
echo ""
echo "Next steps:"
echo "1. Set up cron jobs in cPanel"
echo "2. Configure email settings in .env"
echo "3. Test all features"
echo "4. Review deployment guide for details"
echo "=========================================="



