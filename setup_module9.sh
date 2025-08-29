#!/bin/bash

# Module 9: Analytics & Reports Setup Script
# This script sets up the analytics and reporting functionality for the admin panel

echo "🚀 Setting up Module 9: Analytics & Reports..."

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
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_status "Starting Module 9 setup..."

# Step 1: Run migrations
print_status "Running database migrations..."
php artisan migrate

if [ $? -eq 0 ]; then
    print_success "Migrations completed successfully"
else
    print_error "Migration failed. Please check the database connection and try again."
    exit 1
fi

# Step 2: Install required packages
print_status "Installing required packages..."

# Check if dompdf is installed
if ! composer show barryvdh/laravel-dompdf > /dev/null 2>&1; then
    print_status "Installing DomPDF for PDF generation..."
    composer require barryvdh/laravel-dompdf
else
    print_success "DomPDF is already installed"
fi

# Check if Laravel Excel is installed
if ! composer show maatwebsite/excel > /dev/null 2>&1; then
    print_status "Installing Laravel Excel for CSV export..."
    composer require maatwebsite/excel
else
    print_success "Laravel Excel is already installed"
fi

# Step 3: Publish vendor assets
print_status "Publishing vendor assets..."
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"

# Step 4: Clear caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Step 5: Create storage symlink if it doesn't exist
if [ ! -L "public/storage" ]; then
    print_status "Creating storage symlink..."
    php artisan storage:link
fi

# Step 6: Set up scheduled tasks
print_status "Setting up scheduled tasks..."

# Check if the command exists in the kernel
if ! grep -q "ClearExpiredReports" app/Console/Kernel.php; then
    print_warning "Please add the following line to app/Console/Kernel.php in the schedule method:"
    echo "    \$schedule->command('reports:clear-expired')->daily();"
else
    print_success "Scheduled task already configured"
fi

# Step 7: Test the setup
print_status "Testing the setup..."

# Check if routes are registered
if php artisan route:list | grep -q "admin.analytics"; then
    print_success "Analytics routes are registered"
else
    print_error "Analytics routes are not registered. Please check the routes file."
fi

# Check if models exist
if [ -f "app/Models/ReportCache.php" ] && [ -f "app/Models/AuditLog.php" ]; then
    print_success "Analytics models are created"
else
    print_error "Analytics models are missing. Please check the Models directory."
fi

# Check if controller exists
if [ -f "app/Http/Controllers/Admin/AnalyticsController.php" ]; then
    print_success "Analytics controller is created"
else
    print_error "Analytics controller is missing. Please check the Controllers directory."
fi

# Check if views exist
if [ -d "resources/views/admin/analytics" ]; then
    print_success "Analytics views are created"
else
    print_error "Analytics views are missing. Please check the Views directory."
fi

# Step 8: Set up permissions (if using Spatie Laravel Permission)
if [ -f "app/Models/Permission.php" ]; then
    print_status "Setting up permissions..."
    php artisan permission:create-permission "view_analytics"
    php artisan permission:create-permission "export_reports"
    print_success "Permissions created"
else
    print_warning "Spatie Laravel Permission not detected. Please set up permissions manually if needed."
fi

# Step 9: Create sample data for testing (optional)
read -p "Do you want to create sample data for testing? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Creating sample data..."
    
    # Create sample users
    php artisan tinker --execute="
        App\Models\User::factory()->count(10)->create();
        App\Models\Transaction::factory()->count(20)->create(['status' => 'completed']);
        App\Models\Commission::factory()->count(15)->create();
        App\Models\Magazine::factory()->count(5)->create();
        echo 'Sample data created successfully';
    "
    
    print_success "Sample data created"
fi

# Step 10: Final checks
print_status "Performing final checks..."

# Check database tables
if php artisan tinker --execute="echo 'Report cache table: ' . (Schema::hasTable('report_cache') ? 'OK' : 'MISSING');" | grep -q "OK"; then
    print_success "Report cache table exists"
else
    print_error "Report cache table is missing"
fi

if php artisan tinker --execute="echo 'Audit logs table: ' . (Schema::hasTable('audit_logs') ? 'OK' : 'MISSING');" | grep -q "OK"; then
    print_success "Audit logs table exists"
else
    print_error "Audit logs table is missing"
fi

# Step 11: Display access information
echo
print_success "Module 9: Analytics & Reports setup completed!"
echo
echo "📊 Access your analytics dashboard at:"
echo "   http://your-domain.com/admin/analytics"
echo
echo "🔧 Available features:"
echo "   • Real-time KPIs and metrics"
echo "   • Interactive charts and graphs"
echo "   • CSV and PDF report generation"
echo "   • Advanced filtering and date ranges"
echo "   • Report caching for performance"
echo "   • Audit logging for compliance"
echo
echo "📋 Next steps:"
echo "   1. Log in to your admin panel"
echo "   2. Navigate to Analytics & Reports"
echo "   3. Configure your preferred filters"
echo "   4. Generate and export reports"
echo "   5. Set up scheduled cache clearing (optional)"
echo
echo "🧪 To run tests:"
echo "   php artisan test --filter=AnalyticsTest"
echo
echo "📚 For more information, check the documentation in:"
echo "   MODULE_9_README.md"
echo

# Step 12: Create README file
cat > MODULE_9_README.md << 'EOF'
# Module 9: Analytics & Reports

## Overview
This module provides comprehensive analytics and reporting capabilities for administrators, including real-time KPIs, interactive charts, and exportable reports.

## Features
- **Dashboard KPIs**: Active users, subscriptions, payments, commissions
- **Interactive Charts**: Revenue trends, user growth, commission tracking
- **Report Generation**: CSV and PDF exports for all data types
- **Advanced Filtering**: Date ranges, languages, statuses, levels
- **Performance Optimization**: Report caching and database indexing
- **Audit Trail**: Complete logging of admin actions
- **Mobile-First Design**: Responsive interface for all devices

## Database Tables
- `report_cache`: Cached reports for performance
- `audit_logs`: Admin action logging

## Routes
- `GET /admin/analytics` - Analytics dashboard
- `POST /admin/analytics/export` - Export reports
- `GET /admin/analytics/kpis` - Get KPI data
- `GET /admin/analytics/chart-data` - Get chart data

## Models
- `ReportCache`: Manages report caching
- `AuditLog`: Tracks admin actions

## Controllers
- `AnalyticsController`: Handles all analytics functionality

## Views
- `admin.analytics.index`: Main dashboard
- `admin.analytics.reports.pdf`: PDF report template

## Commands
- `reports:clear-expired`: Clears expired report cache

## Testing
Run the comprehensive test suite:
```bash
php artisan test --filter=AnalyticsTest
```

## Security
- Role-based access control
- Input sanitization
- Audit logging for compliance
- No PII exposure in exports

## Performance
- Report caching (configurable TTL)
- Database indexing on key fields
- Optimized queries with eager loading
- Background cache clearing

## Mobile Support
- Responsive grid layouts
- Touch-friendly controls
- Optimized charts for small screens
- Collapsible filter sections
EOF

print_success "README file created: MODULE_9_README.md"

echo
print_success "🎉 Setup complete! Your analytics module is ready to use."
echo
print_warning "Remember to:"
echo "   • Configure your database connection"
echo "   • Set up proper admin authentication"
echo "   • Configure your web server"
echo "   • Set up SSL for production use"
echo
print_status "Happy analyzing! 📊"
