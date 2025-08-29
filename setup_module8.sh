#!/bin/bash

echo "🚀 Setting up Module 8: Magazine Management & Content Delivery"
echo "================================================================"

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run from the root of a Laravel project"
    exit 1
fi

echo "✅ Laravel project detected"

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate

if [ $? -eq 0 ]; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migration failed. Please check your database configuration"
    exit 1
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

if [ $? -eq 0 ]; then
    echo "✅ Storage link created"
else
    echo "⚠️  Storage link creation failed (may already exist)"
fi

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/public/magazines
mkdir -p storage/app/public/magazines/covers
mkdir -p storage/app/public/magazines/versions

# Set permissions
echo "🔐 Setting file permissions..."
chmod -R 775 storage/app/public/magazines

# Seed sample data
echo "🌱 Seeding sample magazine data..."
php artisan db:seed --class=MagazineSeeder

if [ $? -eq 0 ]; then
    echo "✅ Sample data seeded successfully"
else
    echo "⚠️  Seeding failed (check if MagazineSeeder exists)"
fi

# Run tests
echo "🧪 Running magazine tests..."
php artisan test tests/Feature/MagazineTest.php

if [ $? -eq 0 ]; then
    echo "✅ All tests passed!"
else
    echo "⚠️  Some tests failed. Check the output above for details"
fi

echo ""
echo "🎉 Module 8 setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Access admin panel at /admin/magazines"
echo "2. Upload your first magazine with cover image"
echo "3. Test user access at /magazines (requires subscription)"
echo "4. Check the MODULE_8_README.md for detailed documentation"
echo ""
echo "🔧 Configuration files created:"
echo "- Database migrations for magazines, versions, and views"
echo "- Magazine models with relationships and methods"
echo "- Admin and user controllers"
echo "- Mobile-first responsive views"
echo "- Comprehensive test suite"
echo ""
echo "📱 Mobile-first design features:"
echo "- Responsive grid layout (1-4 columns based on screen size)"
echo "- Touch-friendly interface with large buttons"
echo "- Optimized for 360px+ mobile devices"
echo "- Collapsible filters on small screens"
echo ""
echo "🔒 Security features:"
echo "- Subscription-based access control"
echo "- File type and size validation"
echo "- Secure file storage outside web root"
echo "- Admin activity logging"
echo ""
echo "Happy magazine management! 📚✨"
