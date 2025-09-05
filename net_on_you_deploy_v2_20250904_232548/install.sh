#!/bin/bash

echo "🚀 Net On You Installation Script"
echo "================================="

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed or not in PATH"
    exit 1
fi

echo "✅ PHP found: $(php -v | head -n1)"

# Check if Composer is available
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed"
    echo "Please install Composer first: https://getcomposer.org/download/"
    exit 1
fi

echo "✅ Composer found: $(composer --version)"

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

if [ $? -ne 0 ]; then
    echo "❌ Failed to install PHP dependencies"
    exit 1
fi

echo "✅ PHP dependencies installed"

# Set permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

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
    exit 1
fi

echo "✅ Database migrations completed"

# Test CoinPayments integration
echo "💰 Testing CoinPayments integration..."
php test_coinpayments_integration.php

if [ $? -eq 0 ]; then
    echo "✅ CoinPayments integration test passed"
else
    echo "⚠️ CoinPayments integration test had issues"
    echo "Please check your CoinPayments configuration"
fi

echo ""
echo "🎉 Installation completed successfully!"
echo ""
echo "📋 Next Steps:"
echo "1. Update .env file with your database settings"
echo "2. Update CoinPayments IPN URL in dashboard"
echo "3. Test with small amounts first"
echo "4. Monitor logs for any issues"
echo ""
echo "🔧 Required .env settings to update:"
echo "DB_DATABASE=your_cpanel_database_name"
echo "DB_USERNAME=your_cpanel_database_user"
echo "DB_PASSWORD=your_database_password"
echo "MAIL_PASSWORD=your_email_password"
