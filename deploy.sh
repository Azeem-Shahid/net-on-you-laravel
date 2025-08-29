#!/bin/bash

# Laravel cPanel Deployment Script
# This script should be run after uploading the ZIP file to cPanel

echo "🚀 Starting Laravel deployment..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please make sure you're in the Laravel project directory."
    exit 1
fi

echo "✅ Found Laravel project in $(pwd)"

# Set proper permissions
echo "🔐 Setting proper permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 644 storage/logs/
chmod -R 644 storage/framework/cache/
chmod -R 644 storage/framework/sessions/
chmod -R 644 storage/framework/views/

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Run database seeders (optional - remove if not needed)
echo "🌱 Running database seeders..."
php artisan db:seed --force

# Optimize the application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper file ownership (adjust username if needed)
echo "👤 Setting file ownership..."
chown -R $(whoami):$(whoami) .

echo "✅ Laravel deployment completed successfully!"
echo "🌐 Your website should now be live!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your .env file with database credentials"
echo "2. Set up your domain in cPanel"
echo "3. Test your website"
echo ""
echo "🔧 If you encounter issues, check the Laravel logs in storage/logs/"
