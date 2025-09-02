# Net On You - cPanel Terminal Commands Reference

## Quick Setup Commands

### 1. Initial Setup (Run after file upload)
```bash
# Navigate to your domain directory
cd public_html

# Set file permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate
```

### 2. Database Operations
```bash
# Import database (replace with your database name)
mysql -u your_username -p your_database_name < net_on_you_database_*.sql

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Run migrations (if needed)
php artisan migrate --force

# Run seeders (optional - database already has data)
php artisan db:seed --force
```

### 3. Cache Management
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear all caches at once
php artisan optimize:clear
```

## System Management Commands

### 4. Application Status
```bash
# Check Laravel status
php artisan about

# Check system information
php artisan tinker
>>> phpinfo();
>>> exit

# Check queue status
php artisan queue:work --once
```

### 5. User Management
```bash
# Create admin user
php artisan tinker
>>> $admin = new App\Models\Admin();
>>> $admin->name = 'New Admin';
>>> $admin->email = 'newadmin@example.com';
>>> $admin->password = Hash::make('password123');
>>> $admin->role = 'admin';
>>> $admin->status = 'active';
>>> $admin->save();
>>> exit

# Check user count
php artisan tinker
>>> App\Models\User::count();
>>> exit
```

### 6. Database Queries
```bash
# Check User 1 referral data
php artisan tinker
>>> $user1 = App\Models\User::where('email', 'alex.johnson@example.com')->first();
>>> $user1->referrals()->count();
>>> $user1->commissions()->sum('amount');
>>> exit

# Check commission totals
php artisan tinker
>>> App\Models\Commission::sum('amount');
>>> App\Models\Commission::where('payout_status', 'pending')->sum('amount');
>>> exit

# Check transaction totals
php artisan tinker
>>> App\Models\Transaction::sum('amount');
>>> App\Models\Transaction::where('status', 'completed')->sum('amount');
>>> exit
```

## Cron Job Commands

### 7. Set Up Cron Jobs in cPanel
Add these to your cPanel Cron Jobs:

```bash
# Process payments every 6 hours
0 */6 * * * cd /home/your_username/public_html && php artisan payments:process

# Calculate commissions daily at midnight
0 0 * * * cd /home/your_username/public_html && php artisan commissions:calculate

# Send email notifications every 2 hours
0 */2 * * * cd /home/your_username/public_html && php artisan emails:send

# Generate reports daily
0 1 * * * cd /home/your_username/public_html && php artisan reports:generate

# Clean up old logs weekly
0 2 * * 0 cd /home/your_username/public_html && php artisan logs:cleanup
```

### 8. Test Cron Jobs Manually
```bash
# Test payment processing
php artisan payments:process

# Test commission calculation
php artisan commissions:calculate

# Test email sending
php artisan emails:send

# Test report generation
php artisan reports:generate
```

## Email Testing Commands

### 9. Email Configuration
```bash
# Test email configuration
php artisan tinker
>>> Mail::raw('Test email from Net On You', function($msg) {
>>>     $msg->to('test@example.com')->subject('Test Email');
>>> });
>>> exit

# Send test email to specific user
php artisan tinker
>>> $user = App\Models\User::first();
>>> Mail::to($user->email)->send(new App\Mail\WelcomeEmail($user));
>>> exit
```

## Log Management Commands

### 10. View and Manage Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View recent errors
tail -n 50 storage/logs/laravel.log | grep ERROR

# Clear old logs
php artisan logs:cleanup

# Check log file sizes
du -h storage/logs/*
```

## Performance Optimization Commands

### 11. Optimization
```bash
# Optimize autoloader
composer dump-autoload --optimize

# Clear and rebuild caches
php artisan optimize:clear
php artisan optimize

# Check file permissions
ls -la storage/
ls -la bootstrap/cache/
```

## Troubleshooting Commands

### 12. Common Issues
```bash
# Check PHP version
php -v

# Check Composer version
composer --version

# Check disk space
df -h

# Check memory usage
free -m

# Check if services are running
ps aux | grep php
```

### 13. Database Troubleshooting
```bash
# Check database connection
php artisan tinker
>>> try { DB::connection()->getPdo(); echo "Connected successfully"; } catch(Exception $e) { echo "Connection failed: " . $e->getMessage(); }
>>> exit

# Check table structure
php artisan tinker
>>> Schema::getColumnListing('users');
>>> exit

# Check specific user data
php artisan tinker
>>> App\Models\User::where('email', 'alex.johnson@example.com')->with('referrals', 'commissions')->first();
>>> exit
```

## Security Commands

### 14. Security Checks
```bash
# Check file permissions
find . -type f -perm 777
find . -type d -perm 777

# Check for sensitive files
find . -name "*.env*" -o -name "*.key" -o -name "*.pem"

# Verify .htaccess
cat public/.htaccess
```

## Backup Commands

### 15. Create Backups
```bash
# Backup database
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
tar -czf files_backup_$(date +%Y%m%d_%H%M%S).tar.gz --exclude=node_modules --exclude=vendor .

# Backup storage directory
tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## Monitoring Commands

### 16. System Monitoring
```bash
# Check system load
uptime

# Check memory usage
free -m

# Check disk usage
df -h

# Check active connections
netstat -an | grep :80 | wc -l
```

## Quick Fix Commands

### 17. Common Fixes
```bash
# Fix permission issues
chown -R your_username:your_username .
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Fix composer issues
composer clear-cache
composer install --no-dev --optimize-autoloader

# Fix Laravel issues
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

## User 1 Referral Network Commands

### 18. Check User 1 Data
```bash
# Get User 1 details
php artisan tinker
>>> $user1 = App\Models\User::where('email', 'alex.johnson@example.com')->first();
>>> echo "User ID: " . $user1->id;
>>> echo "Name: " . $user1->name;
>>> echo "Referral Code: " . $user1->referral_code;
>>> exit

# Check Level 1 referrals
php artisan tinker
>>> $user1 = App\Models\User::where('email', 'alex.johnson@example.com')->first();
>>> $level1 = $user1->referrals()->where('level', 1)->count();
>>> echo "Level 1 referrals: " . $level1;
>>> exit

# Check Level 2 referrals
php artisan tinker
>>> $user1 = App\Models\User::where('email', 'alex.johnson@example.com')->first();
>>> $level2 = $user1->referrals()->where('level', 2)->count();
>>> echo "Level 2 referrals: " . $level2;
>>> exit

# Check total commissions
php artisan tinker
>>> $user1 = App\Models\User::where('email', 'alex.johnson@example.com')->first();
>>> $total = $user1->commissions()->sum('amount');
>>> echo "Total commissions: $" . number_format($total, 2);
>>> exit
```

## Complete Deployment Checklist

### 19. Final Verification
```bash
# 1. Check file permissions
ls -la storage/
ls -la bootstrap/cache/

# 2. Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# 3. Check admin access
php artisan tinker
>>> App\Models\Admin::count();
>>> exit

# 4. Check user data
php artisan tinker
>>> App\Models\User::count();
>>> App\Models\Referral::count();
>>> App\Models\Commission::count();
>>> exit

# 5. Test email
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
>>> exit

# 6. Check cron jobs
crontab -l
```

## Emergency Commands

### 20. Emergency Recovery
```bash
# Reset to clean state
php artisan migrate:fresh --seed

# Restore from backup
mysql -u your_username -p your_database_name < backup_file.sql

# Reinstall dependencies
rm -rf vendor/
composer install --no-dev --optimize-autoloader

# Reset permissions
chmod -R 755 .
chmod -R 775 storage/ bootstrap/cache/
```

---

## Quick Reference

| Task | Command |
|------|---------|
| Set permissions | `find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \;` |
| Install dependencies | `composer install --optimize-autoloader --no-dev` |
| Generate key | `php artisan key:generate` |
| Clear caches | `php artisan optimize:clear` |
| Optimize | `php artisan optimize` |
| Check status | `php artisan about` |
| Test database | `php artisan tinker` then `DB::connection()->getPdo();` |
| View logs | `tail -f storage/logs/laravel.log` |
| Check User 1 | `php artisan tinker` then `App\Models\User::where('email', 'alex.johnson@example.com')->first();` |

**Remember**: Replace `your_username` and `your_database_name` with your actual cPanel credentials.


