#!/bin/bash

# Magazine Cover Image Deployment Script
# Run this script on your production server

echo "🚀 Starting Magazine Cover Image Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    print_error "This doesn't appear to be a Laravel project. Please run from the project root."
    exit 1
fi

echo "📁 Creating required directories..."
mkdir -p storage/app/public/magazines/covers
chmod 755 storage/app/public/magazines/covers
print_status "Created storage directories"

echo "🔗 Creating storage link..."
php artisan storage:link
print_status "Storage link created"

echo "🗃️ Checking database..."
# Check if cover_image_path column exists
DB_CHECK=$(php artisan tinker --execute="echo Schema::hasColumn('magazines', 'cover_image_path') ? 'true' : 'false';" 2>/dev/null)

if [ "$DB_CHECK" = "false" ]; then
    print_warning "cover_image_path column doesn't exist. Adding it..."
    php artisan tinker --execute="Schema::table('magazines', function(\$table) { \$table->string('cover_image_path', 255)->nullable()->after('mime_type'); });"
    print_status "Database column added"
else
    print_status "Database column already exists"
fi

echo "🧹 Clearing caches..."
php artisan config:clear
php artisan view:clear
php artisan cache:clear
print_status "Caches cleared"

echo "📋 Checking placeholder images..."
if [ -f "public/images/magazine-placeholder.svg" ] && [ -f "public/images/magazine-placeholder.jpg" ]; then
    print_status "Placeholder images found"
else
    print_warning "Placeholder images not found. Please upload them manually."
fi

echo "🔍 Verifying deployment..."
# Check if storage link exists
if [ -L "public/storage" ]; then
    print_status "Storage link verified"
else
    print_error "Storage link not found. Run: php artisan storage:link"
fi

# Check if covers directory exists and is writable
if [ -d "storage/app/public/magazines/covers" ] && [ -w "storage/app/public/magazines/covers" ]; then
    print_status "Covers directory is writable"
else
    print_error "Covers directory not writable. Check permissions."
fi

echo ""
echo "🎉 Magazine Cover Image deployment completed!"
echo ""
echo "📝 Next steps:"
echo "1. Upload the modified files to your production server"
echo "2. Test the admin panel magazine creation/editing"
echo "3. Test the user-side magazine display"
echo ""
echo "📁 Files to upload:"
echo "   - app/Http/Controllers/Admin/MagazineController.php"
echo "   - resources/views/admin/magazines/create.blade.php"
echo "   - resources/views/admin/magazines/edit.blade.php"
echo "   - resources/views/admin/magazines/index.blade.php"
echo "   - resources/views/magazines/index.blade.php"
echo "   - resources/views/magazines/show.blade.php"
echo "   - public/images/magazine-placeholder.svg"
echo "   - public/images/magazine-placeholder.jpg"



