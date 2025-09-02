#!/bin/bash

# Net On You - Database Export Script
# This script creates a complete database export with dummy data

echo "Starting Net On You database export..."

# Set variables
DB_NAME="net_on_you"
EXPORT_DIR="database_export"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
EXPORT_FILE="net_on_you_database_${TIMESTAMP}.sql"
ZIP_FILE="net_on_you_deploy_${TIMESTAMP}.zip"

# Create export directory
mkdir -p $EXPORT_DIR

echo "Creating database export..."

# Export database structure and data
mysqldump -u root -p --routines --triggers --single-transaction $DB_NAME > $EXPORT_DIR/$EXPORT_FILE

if [ $? -eq 0 ]; then
    echo "Database exported successfully to $EXPORT_DIR/$EXPORT_FILE"
else
    echo "Error: Database export failed"
    exit 1
fi

# Create deployment package
echo "Creating deployment package..."

# Copy necessary files
cp -r app $EXPORT_DIR/
cp -r config $EXPORT_DIR/
cp -r database $EXPORT_DIR/
cp -r public $EXPORT_DIR/
cp -r resources $EXPORT_DIR/
cp -r routes $EXPORT_DIR/
cp -r storage $EXPORT_DIR/
cp artisan $EXPORT_DIR/
cp composer.json $EXPORT_DIR/
cp composer.lock $EXPORT_DIR/
cp .env.example $EXPORT_DIR/
cp package.json $EXPORT_DIR/
cp package-lock.json $EXPORT_DIR/
cp vite.config.js $EXPORT_DIR/

# Copy documentation
cp -r delivery $EXPORT_DIR/
cp *.md $EXPORT_DIR/ 2>/dev/null || true

# Create deployment instructions
cat > $EXPORT_DIR/DEPLOYMENT_INSTRUCTIONS.md << 'EOF'
# Net On You - Deployment Instructions

## Database Import

1. Upload the database file to your cPanel File Manager
2. Use cPanel Terminal to import the database:

```bash
# Navigate to your domain directory
cd public_html

# Import the database
mysql -u your_username -p your_database_name < net_on_you_database_*.sql
```

## File Upload

1. Upload all files to your domain's public_html directory
2. Set proper permissions:

```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## Environment Setup

1. Copy .env.example to .env
2. Update database credentials in .env file
3. Generate application key:

```bash
php artisan key:generate
```

## Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies (if needed)
npm install
npm run build
```

## Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate --force

# Run seeders (optional - database already has data)
php artisan db:seed --force
```

## Set Up Cron Jobs

Add these cron jobs in cPanel:

```bash
# Process payments every 6 hours
0 */6 * * * cd /home/your_username/public_html && php artisan payments:process

# Calculate commissions daily
0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate

# Send email notifications every 2 hours
0 */2 * * * cd /home/your_username/public_html && php artisan emails:send
```

## Admin Access

- URL: https://yourdomain.com/admin/login
- Email: admin@netonyou.com
- Password: admin123

## User 1 Referral Network

User 1 (Alex Johnson) has been set up with:
- 10 Level 1 referrals
- 15 Level 2 referrals
- Active commissions and transactions
- Complete referral network data

Email: alex.johnson@example.com
Password: password123
Referral Code: ALEX2024

## Features Included

- Complete referral system with multi-level commissions
- Payment processing with multiple gateways
- Email templates and notifications
- Multi-language support
- Magazine management
- Admin dashboard
- Commission tracking
- Payout management
- Security policies
- Audit logging
- Report caching
- Command scheduling

## Support

For technical support, refer to the documentation in the delivery/ folder.
EOF

# Create cPanel deployment script
cat > $EXPORT_DIR/cpanel_deploy.sh << 'EOF'
#!/bin/bash

# Net On You - cPanel Deployment Script
# Run this script in cPanel Terminal after uploading files

echo "Starting Net On You deployment on cPanel..."

# Set permissions
echo "Setting file permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

# Install dependencies
echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Generate application key
echo "Generating application key..."
php artisan key:generate

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"
echo "Admin login: https://yourdomain.com/admin/login"
echo "Email: admin@netonyou.com"
echo "Password: admin123"
EOF

chmod +x $EXPORT_DIR/cpanel_deploy.sh

# Create ZIP file
echo "Creating ZIP package..."
zip -r $ZIP_FILE $EXPORT_DIR/

if [ $? -eq 0 ]; then
    echo "Deployment package created successfully: $ZIP_FILE"
    echo "Package includes:"
    echo "- Complete database with dummy data"
    echo "- All application files"
    echo "- Deployment instructions"
    echo "- cPanel deployment script"
    echo "- Documentation"
else
    echo "Error: Failed to create ZIP package"
    exit 1
fi

# Clean up
rm -rf $EXPORT_DIR

echo "Export completed successfully!"
echo "Upload $ZIP_FILE to your cPanel and extract it in public_html"
echo "Follow the instructions in DEPLOYMENT_INSTRUCTIONS.md"


