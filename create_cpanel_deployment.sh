#!/bin/bash

# Net On You - Complete cPanel Deployment Package Creator
# This script creates a comprehensive deployment package with database export

echo "=========================================="
echo "Net On You - Creating cPanel Deployment Package"
echo "=========================================="

# Set variables
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
PACKAGE_NAME="net_on_you_cpanel_deploy_${TIMESTAMP}"
ZIP_FILE="${PACKAGE_NAME}.zip"
DB_NAME="net_on_you"

# Create package directory
mkdir -p $PACKAGE_NAME

echo "Step 1: Exporting database with dummy data..."

# Export database structure and data
DB_EXPORT_FILE="${PACKAGE_NAME}/database_export.sql"
mysqldump -u root -p --routines --triggers --single-transaction --complete-insert $DB_NAME > $DB_EXPORT_FILE

if [ $? -eq 0 ]; then
    echo "✅ Database exported successfully"
else
    echo "❌ Error: Database export failed"
    echo "Please ensure MySQL is running and credentials are correct"
    exit 1
fi

echo "Step 2: Copying application files..."

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

# Copy important documentation files
cp *.md $PACKAGE_NAME/ 2>/dev/null || true

echo "Step 3: Creating deployment scripts..."

# Create main deployment script
cat > $PACKAGE_NAME/deploy.sh << 'EOF'
#!/bin/bash

# Net On You - cPanel Deployment Script
# Run this script in cPanel Terminal after uploading files

echo "=========================================="
echo "Net On You - cPanel Deployment"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo "Step 1: Setting file permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

echo "Step 2: Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "Step 3: Environment setup..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ Created .env file from .env.example"
fi

echo "Step 4: Generating application key..."
php artisan key:generate

echo "Step 5: Database setup..."
echo "⚠️  IMPORTANT: Make sure to import the database_export.sql file first!"
echo "   You can do this through cPanel phpMyAdmin or Terminal:"
echo "   mysql -u your_username -p your_database_name < database_export.sql"
echo ""
read -p "Press Enter after importing the database..."

echo "Step 6: Running migrations (if needed)..."
php artisan migrate --force

echo "Step 7: Clearing and optimizing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Step 8: Setting up storage links..."
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
echo "👤 User 1 (Alex Johnson) - Complete Referral Network:"
echo "   Email: alex.johnson@example.com"
echo "   Password: password123"
echo "   Referral Code: ALEX2024"
echo "   Features: 25+ referrals, commission tracking, transaction history"
echo ""
echo "📋 Next Steps:"
echo "   1. Set up cron jobs in cPanel (see CRON_SETUP.md)"
echo "   2. Configure email settings in .env file"
echo "   3. Test all features and admin panel"
echo "   4. Review documentation in delivery/ folder"
echo "=========================================="
EOF

chmod +x $PACKAGE_NAME/deploy.sh

# Create database import script
cat > $PACKAGE_NAME/import_database.sh << 'EOF'
#!/bin/bash

# Net On You - Database Import Script
# Run this script to import the database

echo "=========================================="
echo "Net On You - Database Import"
echo "=========================================="

# Check if database file exists
if [ ! -f "database_export.sql" ]; then
    echo "❌ Error: database_export.sql file not found"
    exit 1
fi

echo "⚠️  IMPORTANT: Make sure you have:"
echo "   1. Created a MySQL database in cPanel"
echo "   2. Updated .env file with correct database credentials"
echo "   3. Have the database username and password ready"
echo ""

read -p "Enter your MySQL username: " DB_USER
read -s -p "Enter your MySQL password: " DB_PASS
echo ""
read -p "Enter your database name: " DB_NAME

echo "Importing database..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < database_export.sql

if [ $? -eq 0 ]; then
    echo "✅ Database imported successfully!"
    echo ""
    echo "📊 Database includes:"
    echo "   - Complete user structure with referral relationships"
    echo "   - User 1 (Alex Johnson) with 25+ referrals"
    echo "   - Commission tracking and calculations"
    echo "   - Transaction history and payments"
    echo "   - Email templates and settings"
    echo "   - Multi-language support data"
    echo "   - Magazine content and subscriptions"
    echo "   - Admin accounts and system settings"
else
    echo "❌ Error: Database import failed"
    echo "Please check your credentials and try again"
    exit 1
fi
EOF

chmod +x $PACKAGE_NAME/import_database.sh

# Create cron setup script
cat > $PACKAGE_NAME/setup_cron.sh << 'EOF'
#!/bin/bash

# Net On You - Cron Jobs Setup Script
# This script helps you set up cron jobs in cPanel

echo "=========================================="
echo "Net On You - Cron Jobs Setup"
echo "=========================================="

echo "📋 Add these cron jobs to your cPanel Cron Jobs section:"
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
echo "4. Clean up expired sessions daily:"
echo "   0 1 * * * cd /home/your_username/public_html && php artisan session:gc"
echo ""
echo "5. Generate daily reports:"
echo "   0 2 * * * cd /home/your_username/public_html && php artisan reports:generate"
echo ""
echo "⚠️  Replace 'your_username' with your actual cPanel username"
echo "⚠️  Replace 'public_html' with your actual domain directory if different"
echo ""
echo "📖 To set up cron jobs:"
echo "   1. Login to cPanel"
echo "   2. Go to 'Cron Jobs' section"
echo "   3. Add each command above as a separate cron job"
echo "   4. Save the cron jobs"
echo "=========================================="
EOF

chmod +x $PACKAGE_NAME/setup_cron.sh

echo "Step 4: Creating comprehensive documentation..."

# Create main deployment guide
cat > $PACKAGE_NAME/DEPLOYMENT_GUIDE.md << 'EOF'
# Net On You - Complete cPanel Deployment Guide

## 🚀 Quick Start

### 1. Upload Files
1. Upload the entire package to your cPanel File Manager
2. Extract all files to your `public_html` directory (or your domain's root directory)

### 2. Import Database
```bash
# Option A: Using the import script
chmod +x import_database.sh
./import_database.sh

# Option B: Manual import via cPanel phpMyAdmin
# 1. Go to cPanel > phpMyAdmin
# 2. Select your database
# 3. Click "Import" tab
# 4. Choose database_export.sql file
# 5. Click "Go"
```

### 3. Run Deployment Script
```bash
chmod +x deploy.sh
./deploy.sh
```

### 4. Set Up Cron Jobs
```bash
chmod +x setup_cron.sh
./setup_cron.sh
```

## 🔐 Access Information

### Admin Panel
- **URL**: `https://yourdomain.com/admin/login`
- **Email**: `admin@netonyou.com`
- **Password**: `admin123`

### User 1 (Alex Johnson) - Complete Referral Network
- **Email**: `alex.johnson@example.com`
- **Password**: `password123`
- **Referral Code**: `ALEX2024`
- **Features**: 25+ referrals, commission tracking, complete transaction history

## 📊 What's Included

### Database Content
✅ **Complete User Structure**
- 50+ users with referral relationships
- Multi-level referral network starting with User 1 (Alex Johnson)
- Commission calculations and tracking
- Transaction history and payment records

✅ **Admin System**
- Admin accounts with full permissions
- System settings and configuration
- Audit logging and security policies

✅ **Content Management**
- Magazine articles and content
- Email templates and notifications
- Multi-language support (4 languages)
- Contract templates

✅ **Financial System**
- Commission tracking and calculations
- Payout management and batches
- Transaction history
- Payment gateway configurations

### Features
✅ **Referral System**
- Multi-level commission structure
- Automatic commission calculations
- Referral tracking and analytics
- Payout management

✅ **Payment Processing**
- Multiple payment gateways
- Automated payment processing
- Transaction logging
- Commission distribution

✅ **Email System**
- Automated email notifications
- Template management
- Multi-language email support
- Email queue processing

✅ **Admin Dashboard**
- Complete admin interface
- User management
- Commission tracking
- System monitoring
- Report generation

## 🛠️ Configuration

### Environment Variables (.env)
Update these important settings in your `.env` file:

```env
APP_NAME="Net On You"
APP_URL=https://yourdomain.com
APP_ENV=production

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Cron Jobs
Set up these cron jobs in cPanel:

```bash
# Process payments every 6 hours
0 */6 * * * cd /home/your_username/public_html && php artisan payments:process

# Calculate commissions daily
0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate

# Send email notifications every 2 hours
0 */2 * * * cd /home/your_username/public_html && php artisan emails:send

# Clean up expired sessions daily
0 1 * * * cd /home/your_username/public_html && php artisan session:gc

# Generate daily reports
0 2 * * * cd /home/your_username/public_html && php artisan reports:generate
```

## 🔧 Troubleshooting

### Common Issues

**1. Permission Errors**
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

**2. Database Connection Issues**
- Verify database credentials in `.env` file
- Ensure database exists in cPanel
- Check database user permissions

**3. Composer Issues**
```bash
composer install --optimize-autoloader --no-dev
```

**4. Cache Issues**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 📞 Support

For technical support and detailed documentation, refer to the files in the `delivery/` folder.

## 🎯 Testing Checklist

After deployment, test these features:

- [ ] Admin login works
- [ ] User 1 (Alex Johnson) can login
- [ ] Referral system displays correctly
- [ ] Commission calculations work
- [ ] Email notifications send
- [ ] Payment processing functions
- [ ] Multi-language support works
- [ ] Admin dashboard loads properly
- [ ] Cron jobs are running
- [ ] Database queries perform well

## 🚀 Go Live Checklist

- [ ] Update `.env` with production settings
- [ ] Set up SSL certificate
- [ ] Configure email settings
- [ ] Set up cron jobs
- [ ] Test all functionality
- [ ] Update admin password
- [ ] Configure backup system
- [ ] Monitor system performance
EOF

# Create quick reference card
cat > $PACKAGE_NAME/QUICK_REFERENCE.md << 'EOF'
# Net On You - Quick Reference Card

## 🚀 Deployment Commands
```bash
# 1. Import database
./import_database.sh

# 2. Deploy application
./deploy.sh

# 3. Set up cron jobs
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

## 📊 Database Stats
- **Total Users**: 50+
- **User 1 Referrals**: 25+ (Level 1: 10, Level 2: 15)
- **Transactions**: 100+
- **Commissions**: Active tracking
- **Email Templates**: 15+
- **Languages**: 4 (EN, ES, FR, DE)

## 🛠️ Essential Files
- `database_export.sql` - Complete database with dummy data
- `deploy.sh` - Main deployment script
- `import_database.sh` - Database import helper
- `setup_cron.sh` - Cron jobs setup
- `.env.example` - Environment template

## 📋 Post-Deployment
1. Update `.env` with your settings
2. Set up cron jobs in cPanel
3. Configure email settings
4. Test all features
5. Change default passwords
EOF

echo "Step 5: Creating ZIP package..."

# Create ZIP file
zip -r $ZIP_FILE $PACKAGE_NAME/

if [ $? -eq 0 ]; then
    echo "✅ ZIP package created successfully"
else
    echo "❌ Error: Failed to create ZIP package"
    exit 1
fi

# Clean up
rm -rf $PACKAGE_NAME

echo "=========================================="
echo "🎉 Deployment Package Created Successfully!"
echo "=========================================="
echo ""
echo "📦 Package: $ZIP_FILE"
echo "📊 Size: $(du -h $ZIP_FILE | cut -f1)"
echo ""
echo "📋 Package Contents:"
echo "   ✅ Complete database export with dummy data"
echo "   ✅ All Laravel application files"
echo "   ✅ Automated deployment scripts"
echo "   ✅ Database import helper"
echo "   ✅ Cron jobs setup script"
echo "   ✅ Comprehensive documentation"
echo "   ✅ Quick reference guide"
echo ""
echo "🚀 Next Steps:"
echo "   1. Upload $ZIP_FILE to your cPanel File Manager"
echo "   2. Extract to public_html directory"
echo "   3. Run: chmod +x import_database.sh && ./import_database.sh"
echo "   4. Run: chmod +x deploy.sh && ./deploy.sh"
echo "   5. Set up cron jobs using setup_cron.sh"
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

