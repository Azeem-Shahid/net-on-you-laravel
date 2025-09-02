#!/bin/bash

# Net On You - Simple cPanel Deployment Package (No Database)
# This script creates a deployment package with just the application files

echo "=========================================="
echo "Net On You - Creating Simple Deployment Package"
echo "=========================================="

# Set variables
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
PACKAGE_NAME="net_on_you_app_${TIMESTAMP}"
ZIP_FILE="${PACKAGE_NAME}.zip"

# Create package directory
mkdir -p $PACKAGE_NAME

echo "Copying application files..."

# Copy essential Laravel files
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

# Copy vendor directory (if exists)
if [ -d "vendor" ]; then
    echo "Copying vendor directory..."
    cp -r vendor $PACKAGE_NAME/
fi

# Copy documentation
if [ -d "delivery" ]; then
    cp -r delivery $PACKAGE_NAME/
fi

echo "Creating deployment script..."

# Create simple deployment script
cat > $PACKAGE_NAME/deploy.sh << 'EOF'
#!/bin/bash

echo "=========================================="
echo "Net On You - Quick Deployment"
echo "=========================================="

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

# Clear and optimize caches
echo "Optimizing for production..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set up storage link
echo "Setting up storage link..."
php artisan storage:link

echo "=========================================="
echo "✅ Deployment completed successfully!"
echo "=========================================="
echo ""
echo "🔐 Admin Access:"
echo "   URL: https://yourdomain.com/admin/login"
echo "   Email: admin@netonyou.com"
echo "   Password: admin123"
echo ""
echo "👤 User 1 (Alex Johnson):"
echo "   Email: alex.johnson@example.com"
echo "   Password: password123"
echo "   Referral Code: ALEX2024"
echo "=========================================="
EOF

chmod +x $PACKAGE_NAME/deploy.sh

# Create cron setup script
cat > $PACKAGE_NAME/setup_cron.sh << 'EOF'
#!/bin/bash

echo "=========================================="
echo "Net On You - Cron Jobs Setup"
echo "=========================================="

echo "📋 Add these cron jobs to your cPanel:"
echo ""
echo "1. Process payments every 6 hours:"
echo "   0 */6 * * * cd /home/your_username/public_html && php artisan payments:process"
echo ""
echo "2. Calculate commissions daily:"
echo "   0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate"
echo ""
echo "3. Send email notifications every 2 hours:"
echo "   0 */2 * * * cd /home/your_username/public_html && php artisan emails:send"
echo ""
echo "⚠️  Replace 'your_username' with your actual cPanel username"
echo "=========================================="
EOF

chmod +x $PACKAGE_NAME/setup_cron.sh

# Create simple instructions
cat > $PACKAGE_NAME/README.md << 'EOF'
# Net On You - Quick Deployment

## 🚀 Deployment Steps

### 1. Upload Files
Upload all files to your cPanel `public_html` directory

### 2. Run Deployment Script
```bash
chmod +x deploy.sh
./deploy.sh
```

### 3. Set Up Cron Jobs
```bash
chmod +x setup_cron.sh
./setup_cron.sh
```

## 🔐 Login Credentials

### Admin
- **URL**: `https://yourdomain.com/admin/login`
- **Email**: `admin@netonyou.com`
- **Password**: `admin123`

### User 1 (Alex Johnson)
- **Email**: `alex.johnson@example.com`
- **Password**: `password123`
- **Referral Code**: `ALEX2024`

## 📋 What's Included
- Complete Laravel application
- All dependencies and vendor files
- Automated deployment script
- Cron jobs setup guide
- User 1 with 25+ referrals and commission tracking
EOF

echo "Creating ZIP package..."
zip -r $ZIP_FILE $PACKAGE_NAME/

# Clean up
rm -rf $PACKAGE_NAME

echo "=========================================="
echo "🎉 Deployment Package Created!"
echo "=========================================="
echo ""
echo "📦 Package: $ZIP_FILE"
echo "📊 Size: $(du -h $ZIP_FILE | cut -f1)"
echo ""
echo "🚀 Upload Instructions:"
echo "   1. Upload $ZIP_FILE to cPanel File Manager"
echo "   2. Extract to public_html directory"
echo "   3. Run: chmod +x deploy.sh && ./deploy.sh"
echo "   4. Set up cron jobs using setup_cron.sh"
echo ""
echo "🔐 Admin: admin@netonyou.com / admin123"
echo "👤 User 1: alex.johnson@example.com / password123"
echo "=========================================="

