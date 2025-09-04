#!/bin/bash

# CoinPayments Production Deployment Script
# This script helps deploy your Laravel application with CoinPayments integration to production

set -e  # Exit on any error

echo "🚀 CoinPayments Production Deployment Script"
echo "============================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

print_status "Starting production deployment..."

# Step 1: Pre-deployment checks
echo ""
print_status "Step 1: Pre-deployment checks..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_error ".env file not found. Please create it from .env.example"
    exit 1
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed or not in PATH."
    exit 1
fi

print_success "Pre-deployment checks passed"

# Step 2: Backup current deployment
echo ""
print_status "Step 2: Creating backup..."

BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

if [ -d "storage" ]; then
    cp -r storage "$BACKUP_DIR/"
    print_success "Storage directory backed up to $BACKUP_DIR"
fi

if [ -f ".env" ]; then
    cp .env "$BACKUP_DIR/"
    print_success "Environment file backed up to $BACKUP_DIR"
fi

# Step 3: Install/Update dependencies
echo ""
print_status "Step 3: Installing dependencies..."

if [ -f "composer.lock" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
else
    composer install --optimize-autoloader --no-dev --no-interaction
fi

print_success "Dependencies installed"

# Step 4: Environment configuration
echo ""
print_status "Step 4: Configuring environment..."

# Check if CoinPayments is configured
if ! grep -q "COINPAYMENTS_ENABLED=true" .env; then
    print_warning "CoinPayments not enabled in .env file"
    print_warning "Please ensure COINPAYMENTS_ENABLED=true is set"
fi

# Check if APP_ENV is set to production
if ! grep -q "APP_ENV=production" .env; then
    print_warning "APP_ENV is not set to production"
    print_warning "Please update .env file: APP_ENV=production"
fi

# Check if APP_DEBUG is disabled
if grep -q "APP_DEBUG=true" .env; then
    print_warning "APP_DEBUG is enabled. Consider setting APP_DEBUG=false for production"
fi

print_success "Environment configuration checked"

# Step 5: Laravel optimizations
echo ""
print_status "Step 5: Optimizing Laravel application..."

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_success "Laravel optimizations completed"

# Step 6: Database migrations
echo ""
print_status "Step 6: Running database migrations..."

# Check if database connection is working
if php artisan migrate:status &> /dev/null; then
    php artisan migrate --force
    print_success "Database migrations completed"
else
    print_error "Database connection failed. Please check your database configuration."
    exit 1
fi

# Step 7: Set permissions
echo ""
print_status "Step 7: Setting file permissions..."

# Set proper permissions for Laravel
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Try to set ownership (may fail on shared hosting)
if command -v chown &> /dev/null; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    chown -R apache:apache storage bootstrap/cache 2>/dev/null || true
fi

print_success "File permissions set"

# Step 8: Test CoinPayments integration
echo ""
print_status "Step 8: Testing CoinPayments integration..."

if [ -f "test_coinpayments_integration.php" ]; then
    php test_coinpayments_integration.php
    if [ $? -eq 0 ]; then
        print_success "CoinPayments integration test passed"
    else
        print_warning "CoinPayments integration test had issues"
        print_warning "Please review the test output above"
    fi
else
    print_warning "CoinPayments test script not found"
fi

# Step 9: Final checks
echo ""
print_status "Step 9: Final production checks..."

# Check if application key is set
if ! grep -q "APP_KEY=base64:" .env; then
    print_warning "APP_KEY is not set. Run: php artisan key:generate"
fi

# Check if CoinPayments credentials are set
if ! grep -q "COINPAYMENTS_MERCHANT_ID=" .env; then
    print_error "CoinPayments credentials not configured in .env file"
    exit 1
fi

print_success "Final checks completed"

# Step 10: Deployment summary
echo ""
echo "🎉 DEPLOYMENT COMPLETED!"
echo "========================"
echo ""

# Display important information
print_status "Important Information:"
echo "  📁 Backup created at: $BACKUP_DIR"
echo "  🌐 App URL: $(grep APP_URL .env | cut -d'=' -f2)"
echo "  🔧 Environment: $(grep APP_ENV .env | cut -d'=' -f2)"
echo "  💰 CoinPayments: $(grep COINPAYMENTS_ENABLED .env | cut -d'=' -f2)"
echo "  🪙 Currency: $(grep COINPAYMENTS_CURRENCY2 .env | cut -d'=' -f2)"
echo ""

# Display next steps
print_status "Next Steps:"
echo "  1. Update CoinPayments dashboard IPN URL"
echo "  2. Test with small amounts first"
echo "  3. Monitor logs for IPN notifications"
echo "  4. Verify transaction processing"
echo "  5. Test subscription activation"
echo ""

# Display CoinPayments IPN URL
IPN_URL=$(grep COINPAYMENTS_IPN_URL .env | cut -d'=' -f2 | tr -d '"')
print_status "CoinPayments IPN URL to configure:"
echo "  $IPN_URL"
echo ""

# Display supported currencies
print_status "Supported Currencies:"
echo "  - USDT.TRC20 (Tether - TRON Network)"
echo "  - USDT.ERC20 (Tether - Ethereum Network)"
echo "  - USDC.TRC20 (USD Coin - TRON Network)"
echo "  - USDC.ERC20 (USD Coin - Ethereum Network)"
echo "  - BTC (Bitcoin)"
echo "  - ETH (Ethereum)"
echo ""

print_success "Deployment script completed successfully!"
echo ""
print_warning "Remember to:"
echo "  - Test the payment flow with small amounts"
echo "  - Monitor the application logs"
echo "  - Set up regular backups"
echo "  - Configure monitoring and alerts"
echo ""

echo "🚀 Your CoinPayments integration is now live!"

