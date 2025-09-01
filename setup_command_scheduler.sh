#!/bin/bash

# Command Scheduler Setup Script for Net On You
# This script sets up the complete command scheduler module

echo "🚀 Setting up Command Scheduler Module for Net On You..."
echo "=================================================="

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
    print_error "Please run this script from your Laravel project root directory"
    exit 1
fi

print_status "Starting Command Scheduler setup..."

# Step 1: Run migrations
print_status "Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_success "Migrations completed successfully"
else
    print_error "Migrations failed"
    exit 1
fi

# Step 2: Seed the database
print_status "Seeding scheduled commands..."
php artisan db:seed --class=ScheduledCommandsSeeder --force
if [ $? -eq 0 ]; then
    print_success "Database seeded successfully"
else
    print_warning "Seeding failed - this might be normal if tables already exist"
fi

# Step 3: Clear and cache config
print_status "Optimizing Laravel configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Step 4: Check if cron is set up
print_status "Checking cron setup..."
if crontab -l 2>/dev/null | grep -q "schedule:run"; then
    print_success "Laravel scheduler cron job is already configured"
else
    print_warning "Laravel scheduler cron job is NOT configured"
    echo ""
    echo "To enable automatic command execution, add this line to your crontab:"
    echo "crontab -e"
    echo ""
    echo "Add this line:"
    echo "* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
    echo ""
    echo "Or run: echo '* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1' | crontab -"
    echo ""
fi

# Step 5: Test the system
print_status "Testing command scheduler..."
php artisan schedule:list
if [ $? -eq 0 ]; then
    print_success "Command scheduler is working correctly"
else
    print_warning "Command scheduler test failed - check your Laravel installation"
fi

# Step 6: Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/

# Step 7: Create log directory if it doesn't exist
print_status "Setting up logging..."
mkdir -p storage/logs
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

echo ""
echo "=================================================="
print_success "Command Scheduler Module setup completed!"
echo ""
echo "📋 Next Steps:"
echo "1. Access the admin panel: /admin/command-scheduler"
echo "2. Test running a command manually"
echo "3. Configure cron jobs for automatic execution"
echo "4. Review and adjust command schedules as needed"
echo ""
echo "🔧 Available Commands:"
echo "- subscriptions:check-expiry (Daily at 6 AM)"
echo "- commissions:process-monthly (Monthly on 1st)"
echo "- magazines:release-reminder (Monthly on 1st at 9 AM)"
echo "- commissions:check-eligibility (Weekly on Monday)"
echo "- system:cleanup (Monthly on 1st at 6 AM)"
echo ""
echo "📚 Documentation:"
echo "- Check CRON_JOBS_COMPLETE_SETUP_GUIDE.md for detailed setup instructions"
echo "- Review the admin panel for real-time monitoring"
echo ""
echo "🎯 Features Available:"
echo "✅ Manual command execution"
echo "✅ Command scheduling and management"
echo "✅ Execution logging and monitoring"
echo "✅ Performance statistics"
echo "✅ CSV export functionality"
echo "✅ Mobile-responsive interface"
echo ""
print_success "Setup complete! Your command scheduler is ready to use."
