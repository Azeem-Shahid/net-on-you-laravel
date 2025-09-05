#!/bin/bash

echo "🚀 Net On You - cPanel Installation Script"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ This script must be run from the Laravel project root directory"
    exit 1
fi

echo "📁 Current directory: $(pwd)"

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

if [ $? -ne 0 ]; then
    echo "❌ Failed to install PHP dependencies"
    echo "Please check if Composer is available in cPanel Terminal"
    exit 1
fi

echo "✅ PHP dependencies installed"

# Set permissions for cPanel
echo "🔐 Setting file permissions for cPanel..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public

echo "✅ File permissions set"

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
echo "⚡ Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Caching completed"

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Database migration failed"
    echo "Please check your database configuration in .env file"
    echo "Make sure to update .env with your cPanel database credentials"
    exit 1
fi

echo "✅ Database migrations completed"

# Test CoinPayments integration
echo "💰 Testing CoinPayments integration..."
if [ -f "test_coinpayments_integration.php" ]; then
    php test_coinpayments_integration.php
    if [ $? -eq 0 ]; then
        echo "✅ CoinPayments integration test passed"
    else
        echo "⚠️ CoinPayments integration test had issues"
        echo "Please check your CoinPayments configuration"
    fi
else
    echo "⚠️ CoinPayments test script not found"
fi

echo ""
echo "🎉 cPanel installation completed successfully!"
echo ""
echo "📋 Next Steps:"
echo "1. Update .env file with your cPanel database settings"
echo "2. Update CoinPayments IPN URL in dashboard"
echo "3. Test with small amounts first"
echo "4. Monitor logs for any issues"
echo ""
echo "🔧 Required .env settings for cPanel:"
echo "DB_DATABASE=your_cpanel_database_name"
echo "DB_USERNAME=your_cpanel_database_user"
echo "DB_PASSWORD=your_database_password"
echo "MAIL_PASSWORD=your_email_password"
echo ""
echo "🌐 Your domain is pre-configured: user.netonyou.com"
