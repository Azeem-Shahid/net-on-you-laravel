#!/bin/bash

# Net On You - Create Deployment Package
# This script creates a complete deployment package with dummy data

echo "=========================================="
echo "Net On You - Creating Deployment Package"
echo "=========================================="

# Set variables
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
PACKAGE_NAME="net_on_you_deploy_${TIMESTAMP}"
ZIP_FILE="${PACKAGE_NAME}.zip"

# Create package directory
mkdir -p $PACKAGE_NAME

echo "Copying application files..."

# Copy essential files
cp -r app $PACKAGE_NAME/
cp -r config $PACKAGE_NAME/
cp -r database $PACKAGE_NAME/
cp -r public $PACKAGE_NAME/
cp -r resources $PACKAGE_NAME/
cp -r routes $PACKAGE_NAME/
cp -r storage $PACKAGE_NAME/
cp artisan $PACKAGE_NAME/
cp composer.json $PACKAGE_NAME/
cp composer.lock $PACKAGE_NAME/
cp .env.example $PACKAGE_NAME/
cp package.json $PACKAGE_NAME/
cp package-lock.json $PACKAGE_NAME/
cp vite.config.js $PACKAGE_NAME/

# Copy documentation
cp -r delivery $PACKAGE_NAME/
cp *.md $PACKAGE_NAME/ 2>/dev/null || true

# Copy deployment scripts
cp export_database.sh $PACKAGE_NAME/
cp cpanel_quick_deploy.sh $PACKAGE_NAME/
cp create_deployment_package.sh $PACKAGE_NAME/

echo "Creating deployment instructions..."

# Create deployment instructions
cat > $PACKAGE_NAME/DEPLOYMENT_INSTRUCTIONS.md << 'EOF'
# Net On You - Deployment Instructions

## Quick Setup

### 1. Upload Files
1. Upload all files to your cPanel public_html directory
2. Extract if uploaded as ZIP

### 2. Set Permissions
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan
```

### 3. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update database settings in .env file
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Run seeders to create dummy data
php artisan db:seed --force
```

### 6. Optimize for Production
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Admin Access

- **URL**: https://yourdomain.com/admin/login
- **Email**: admin@netonyou.com
- **Password**: admin123

## User 1 Referral Network

- **Email**: alex.johnson@example.com
- **Password**: password123
- **Features**: 25+ referrals, commission tracking, complete transaction history

## Cron Jobs Setup

Add these to your cPanel Cron Jobs:

```bash
# Process payments every 6 hours
0 */6 * * * cd /home/your_username/public_html && php artisan payments:process

# Calculate commissions daily
0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate

# Send email notifications every 2 hours
0 */2 * * * cd /home/your_username/public_html && php artisan emails:send
```

## Features Included

✅ Complete referral system with User 1 network  
✅ Multi-level commission tracking  
✅ Payment processing with multiple gateways  
✅ Email templates and notifications  
✅ Multi-language support (4 languages)  
✅ Magazine management system  
✅ Admin dashboard with full functionality  
✅ Subscription management  
✅ Payout system  
✅ Security policies  
✅ Audit logging  
✅ System monitoring  

## Support

Refer to the documentation files for detailed setup instructions.
EOF

# Create quick setup script
cat > $PACKAGE_NAME/quick_setup.sh << 'EOF'
#!/bin/bash

echo "Net On You - Quick Setup"
echo "========================"

# Set permissions
echo "Setting permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

# Install dependencies
echo "Installing dependencies..."
composer install --optimize-autoloader --no-dev

# Generate key
echo "Generating application key..."
php artisan key:generate

# Run migrations and seeders
echo "Setting up database..."
php artisan migrate --force
php artisan db:seed --force

# Optimize
echo "Optimizing for production..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setup completed!"
echo "Admin: https://yourdomain.com/admin/login (admin@netonyou.com / admin123)"
echo "User 1: https://yourdomain.com/login (alex.johnson@example.com / password123)"
EOF

chmod +x $PACKAGE_NAME/quick_setup.sh

# Create database structure file
cat > $PACKAGE_NAME/database_structure.sql << 'EOF'
-- Net On You Database Structure
-- This file contains the database structure for Net On You

-- Note: This is a reference file. The actual database will be created
-- by running Laravel migrations: php artisan migrate

-- Key tables that will be created:
-- - users (with referral relationships)
-- - referrals (referral tracking)
-- - commissions (commission calculations)
-- - transactions (payment history)
-- - subscriptions (subscription management)
-- - magazines (content library)
-- - email_templates (email system)
-- - languages (multi-language support)
-- - contracts (legal documents)
-- - admins (admin accounts)
-- - settings (system configuration)
-- - payout_batches (payout management)

-- Dummy data will be created by running:
-- php artisan db:seed --force
EOF

# Create ZIP package
echo "Creating ZIP package..."
zip -r $ZIP_FILE $PACKAGE_NAME/

# Clean up
rm -rf $PACKAGE_NAME

echo "=========================================="
echo "Deployment package created successfully!"
echo "=========================================="
echo "Package: $ZIP_FILE"
echo ""
echo "Next steps:"
echo "1. Upload $ZIP_FILE to your cPanel File Manager"
echo "2. Extract to public_html directory"
echo "3. Run: chmod +x quick_setup.sh && ./quick_setup.sh"
echo "4. Configure .env file with your database settings"
echo "5. Set up cron jobs in cPanel"
echo ""
echo "Admin access: admin@netonyou.com / admin123"
echo "User 1: alex.johnson@example.com / password123"
echo "=========================================="


