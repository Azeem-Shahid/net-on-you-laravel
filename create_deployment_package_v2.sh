#!/bin/bash

# Create Complete Deployment Package for Net On You - Version 2
# This script creates a ZIP file with .env file pre-configured for user.netonyou.com

echo "🚀 Creating Net On You Deployment Package v2"
echo "============================================="

# Create deployment directory
DEPLOY_DIR="net_on_you_deploy_v2_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$DEPLOY_DIR"

echo "📁 Creating deployment directory: $DEPLOY_DIR"

# Copy essential application files
echo "📋 Copying application files..."

# Core Laravel files
cp -r app "$DEPLOY_DIR/"
cp -r bootstrap "$DEPLOY_DIR/"
cp -r config "$DEPLOY_DIR/"
cp -r database "$DEPLOY_DIR/"
cp -r public "$DEPLOY_DIR/"
cp -r resources "$DEPLOY_DIR/"
cp -r routes "$DEPLOY_DIR/"
cp -r storage "$DEPLOY_DIR/"

# Essential files
cp artisan "$DEPLOY_DIR/"
cp composer.json "$DEPLOY_DIR/"
cp composer.lock "$DEPLOY_DIR/"
cp package.json "$DEPLOY_DIR/"
cp package-lock.json "$DEPLOY_DIR/"
cp phpunit.xml "$DEPLOY_DIR/"
cp vite.config.js "$DEPLOY_DIR/"

# Create .htaccess for public directory
cat > "$DEPLOY_DIR/public/.htaccess" << 'EOF'
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
EOF

# Create .env file pre-configured for user.netonyou.com
cat > "$DEPLOY_DIR/.env" << 'EOF'
# ===========================================
# NET ON YOU - PRODUCTION ENVIRONMENT
# ===========================================
# Pre-configured for user.netonyou.com
# Update database and email settings as needed

# Application Settings
APP_NAME="Net On You"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://user.netonyou.com

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database (cPanel MySQL) - UPDATE THESE
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=YOUR_CPANEL_DATABASE_NAME
DB_USERNAME=YOUR_CPANEL_DATABASE_USER
DB_PASSWORD=YOUR_CPANEL_DATABASE_PASSWORD

# Cache & Session
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email (cPanel SMTP) - UPDATE THESE
MAIL_MAILER=smtp
MAIL_HOST=user.netonyou.com
MAIL_PORT=587
MAIL_USERNAME=noreply@user.netonyou.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@user.netonyou.com"
MAIL_FROM_NAME="Net On You"

# Business Logic
COMMISSION_RATE=0.10
REFERRAL_BONUS=5.00
MIN_PAYOUT_AMOUNT=50.00

# CoinPayments Integration - PRE-CONFIGURED
COINPAYMENTS_ENABLED=true
COINPAYMENTS_MERCHANT_ID="82fb593d8bc444d7fd126342665a3068"
COINPAYMENTS_PUBLIC_KEY="5a3e09f1e0aa0059e826cc064ed786f25c3f1e6450543314e88ecd552eeb4ddb"
COINPAYMENTS_PRIVATE_KEY="179f143D3be0d7064791f2A30ec32538fc68eee9B29745B084455D3E531e1265"
COINPAYMENTS_IPN_SECRET="529209"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="https://user.netonyou.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=false
SUBSCRIPTION_PRICE="39.90"
EOF

# Create installation script
cat > "$DEPLOY_DIR/install.sh" << 'EOF'
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
EOF

# Make installation script executable
chmod +x "$DEPLOY_DIR/install.sh"

# Copy test scripts
cp test_coinpayments_integration.php "$DEPLOY_DIR/"
cp test_coinpayments_complete.php "$DEPLOY_DIR/"
cp test_full_integration.sh "$DEPLOY_DIR/"
cp test_payment_flow.sh "$DEPLOY_DIR/"

# Make test scripts executable
chmod +x "$DEPLOY_DIR/test_full_integration.sh"
chmod +x "$DEPLOY_DIR/test_payment_flow.sh"

# Create README for deployment
cat > "$DEPLOY_DIR/README_DEPLOYMENT.md" << 'EOF'
# 🚀 Net On You - Deployment Package v2

## 📋 What's Included

This package contains everything needed to deploy Net On You with CoinPayments integration:

- ✅ Complete Laravel application
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email configuration
- ✅ Database migrations
- ✅ Installation scripts
- ✅ Test scripts
- ✅ Production configuration
- ✅ Pre-configured for user.netonyou.com

## 🚀 Quick Installation

### 1. Upload Files
Upload all files to your hosting directory (usually `public_html/` or subdirectory)

### 2. Run Installation
```bash
# Make installation script executable
chmod +x install.sh

# Run installation
./install.sh
```

### 3. Update Database Settings
Edit `.env` file and update these settings:
```bash
# Database settings (REQUIRED)
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Email settings (REQUIRED)
MAIL_PASSWORD=your_email_password
```

### 4. Test Installation
```bash
# Run integration test
php test_coinpayments_integration.php

# Run complete test suite
php test_coinpayments_complete.php
```

## 🔧 Pre-Configured Settings

The following settings are already configured for user.netonyou.com:

- ✅ APP_URL=https://user.netonyou.com
- ✅ MAIL_HOST=user.netonyou.com
- ✅ MAIL_FROM_ADDRESS=noreply@user.netonyou.com
- ✅ COINPAYMENTS_IPN_URL=https://user.netonyou.com/payments/coinpayments/ipn
- ✅ All CoinPayments credentials

## 📧 Email Configuration

The email is pre-configured for user.netonyou.com. You only need to:

1. Create email account in cPanel: `noreply@user.netonyou.com`
2. Update `MAIL_PASSWORD` in .env file

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://user.netonyou.com/payments/coinpayments/ipn`

2. **Test with Small Amounts**:
   - Create test transaction with $1.00
   - Monitor logs for IPN notifications

## 🧪 Testing

```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Test payment flow
./test_payment_flow.sh

# Test complete functionality
php test_coinpayments_complete.php
```

## 📊 Monitoring

```bash
# Watch logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# Check database
php artisan tinker --execute="DB::connection()->getPdo();"
```

## 🆘 Troubleshooting

### Common Issues:
1. **500 Error**: Check file permissions
2. **Database Error**: Verify .env database settings
3. **Email Not Sending**: Check SMTP configuration
4. **CoinPayments Not Working**: Check IPN URL

### Debug Commands:
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test database
php artisan tinker --execute="DB::connection()->getPdo();"

# Test email
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('test@example.com')->subject('Test'); });"
```

## ✅ Production Checklist

Before going live:
- [ ] All files uploaded
- [ ] Installation completed
- [ ] Database settings updated in .env
- [ ] Email password updated in .env
- [ ] Database migrations run
- [ ] CoinPayments IPN URL updated
- [ ] Email configuration tested
- [ ] All tests passing
- [ ] Test with small amounts

## 🎉 Ready to Go Live!

Your Net On You application with CoinPayments integration is ready for production!

**Features Included:**
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email notifications
- ✅ User management
- ✅ Payment processing
- ✅ Database integration
- ✅ Production optimizations
- ✅ Pre-configured for user.netonyou.com
EOF

# Create cPanel specific installation script
cat > "$DEPLOY_DIR/cpanel_install.sh" << 'EOF'
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
EOF

chmod +x "$DEPLOY_DIR/cpanel_install.sh"

# Create ZIP file
echo "📦 Creating deployment package..."
ZIP_FILE="net_on_you_deploy_v2_$(date +%Y%m%d_%H%M%S).zip"

# Create ZIP excluding unnecessary files
cd "$DEPLOY_DIR"
zip -r "../$ZIP_FILE" . -x "*.git*" "*.DS_Store*" "node_modules/*" "vendor/*" "storage/logs/*" "storage/framework/cache/*" "storage/framework/sessions/*" "storage/framework/views/*"
cd ..

echo "✅ Deployment package created: $ZIP_FILE"
echo "📁 Package size: $(du -h "$ZIP_FILE" | cut -f1)"

# Create installation commands file
cat > "INSTALLATION_COMMANDS_V2.md" << 'EOF'
# 🚀 Net On You - Installation Commands v2

## 📦 After Uploading ZIP File

### 1. Extract Files
```bash
# Extract the ZIP file
unzip net_on_you_deploy_v2_*.zip

# Move to your web directory (usually public_html)
mv net_on_you_deploy_v2_*/* /path/to/your/web/directory/
cd /path/to/your/web/directory/
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# If composer is not available, download it first:
# curl -sS https://getcomposer.org/installer | php
# php composer.phar install --optimize-autoloader --no-dev
```

### 3. Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public
```

### 4. Generate Application Key
```bash
# Generate Laravel application key
php artisan key:generate
```

### 5. Clear and Cache Configurations
```bash
# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Run Database Migrations
```bash
# Run database migrations
php artisan migrate --force
```

### 7. Update Database Settings
Edit `.env` file and update these REQUIRED settings:
```bash
# Database settings (REQUIRED)
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

# Email settings (REQUIRED)
MAIL_PASSWORD=your_email_password
```

### 8. Test Installation
```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Run complete test suite
php test_coinpayments_complete.php
```

## 🔧 cPanel Specific Commands

If using cPanel, run these commands in cPanel Terminal:

```bash
# Navigate to your project directory
cd public_html/your-project-folder

# Run the cPanel installation script
chmod +x cpanel_install.sh
./cpanel_install.sh
```

## 📧 Email Configuration

The email is pre-configured for user.netonyou.com:

1. **Create email account in cPanel**: `noreply@user.netonyou.com`
2. **Update password in .env**: `MAIL_PASSWORD=your_email_password`

## 💰 CoinPayments Configuration

1. **Update IPN URL in CoinPayments Dashboard**:
   - Go to Account Settings → Merchant Settings
   - Set IPN URL: `https://user.netonyou.com/payments/coinpayments/ipn`

2. **Test with Small Amounts**:
   - Create test transaction with $1.00
   - Monitor logs for IPN notifications

## 🧪 Testing Commands

```bash
# Test CoinPayments integration
php test_coinpayments_integration.php

# Test payment flow
./test_payment_flow.sh

# Test complete functionality
php test_coinpayments_complete.php
```

## 📊 Monitoring Commands

```bash
# Watch logs
tail -f storage/logs/laravel.log | grep -i coinpayments

# Check database
php artisan tinker --execute="DB::connection()->getPdo();"
```

## ✅ Production Checklist

Before going live:
- [ ] All files uploaded and extracted
- [ ] Dependencies installed
- [ ] Permissions set correctly
- [ ] Application key generated
- [ ] Caches cleared and rebuilt
- [ ] Database migrations run
- [ ] Database settings updated in .env
- [ ] Email password updated in .env
- [ ] CoinPayments IPN URL updated
- [ ] Email configuration tested
- [ ] All tests passing
- [ ] Test with small amounts

## 🎉 Ready to Go Live!

Your Net On You application with CoinPayments integration is ready for production!

**Features Included:**
- ✅ CoinPayments integration (USDT/USDC)
- ✅ Email notifications
- ✅ User management
- ✅ Payment processing
- ✅ Database integration
- ✅ Production optimizations
- ✅ Pre-configured for user.netonyou.com
EOF

echo ""
echo "🎉 Deployment Package v2 Created Successfully!"
echo "=============================================="
echo "📦 Package: $ZIP_FILE"
echo "📁 Size: $(du -h "$ZIP_FILE" | cut -f1)"
echo ""
echo "📋 What's Included:"
echo "✅ Complete Laravel application"
echo "✅ CoinPayments integration (USDT/USDC)"
echo "✅ Email configuration"
echo "✅ Database migrations"
echo "✅ Installation scripts"
echo "✅ Test scripts"
echo "✅ Production configuration"
echo "✅ Pre-configured for user.netonyou.com"
echo "✅ .env file ready (no need to rename)"
echo ""
echo "🚀 Next Steps:"
echo "1. Upload $ZIP_FILE to your hosting"
echo "2. Extract the files"
echo "3. Run installation commands"
echo "4. Update database settings in .env"
echo "5. Test and go live!"
echo ""
echo "📖 See INSTALLATION_COMMANDS_V2.md for detailed instructions"

