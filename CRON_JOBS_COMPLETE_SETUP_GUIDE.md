# ⏰ Complete Cron Jobs Setup Guide for Net On You

## 📋 Table of Contents
1. [Overview of Required Cron Jobs](#overview-of-required-cron-jobs)
2. [System Requirements](#system-requirements)
3. [Laravel Scheduler Setup](#laravel-scheduler-setup)
4. [Individual Command Setup](#individual-command-setup)
5. [Server Cron Configuration](#server-cron-configuration)
6. [Testing & Verification](#testing--verification)
7. [Monitoring & Maintenance](#monitoring--maintenance)
8. [Troubleshooting](#troubleshooting)
9. [Production Deployment](#production-deployment)
10. [Best Practices](#best-practices)

---

## 🎯 Overview of Required Cron Jobs

Your Net On You system requires several automated tasks to run smoothly:

### Essential Cron Jobs
- **Daily**: Subscription expiry checks, email queue processing
- **Weekly**: Commission eligibility checks, system health monitoring
- **Monthly**: Commission processing, system cleanup, magazine reminders
- **Custom**: Payment webhook processing, real-time notifications

### Why Cron Jobs Are Critical
- **Automated Operations**: Reduce manual work and human error
- **System Health**: Regular maintenance and cleanup
- **Business Logic**: Automated commission processing and notifications
- **Performance**: Cache clearing and optimization
- **Compliance**: Regular audit logging and data management

---

## 🖥️ System Requirements

### Server Requirements
- **Operating System**: Linux (Ubuntu/CentOS), macOS, or Windows
- **PHP**: 8.1 or higher
- **Laravel**: 10.x or higher
- **Database**: MySQL/PostgreSQL with proper permissions
- **Storage**: Sufficient disk space for logs and temporary files

### Software Dependencies
- **Cron Service**: Built-in cron daemon (Linux/macOS) or Task Scheduler (Windows)
- **PHP Extensions**: Required for your Laravel application
- **Database Access**: Proper credentials for scheduled commands
- **File Permissions**: Write access to storage and log directories

---

## ⚙️ Laravel Scheduler Setup

### Current Scheduled Commands
Your system already has these commands scheduled in `app/Console/Kernel.php`:

```php
// Monthly commission processing (1st-10th of each month)
$schedule->command('commissions:process-monthly')
        ->monthlyOn(1, '00:00')
        ->description('Process monthly commissions for eligible users');

// Bimonthly magazine release reminder (every 2 months)
$schedule->command('magazines:release-reminder')
        ->cron('0 9 1 */2 *') // Every 1st of every 2nd month at 9 AM
        ->description('Send reminder to admins for bimonthly magazine release');

// Daily subscription expiry checks
$schedule->command('subscriptions:check-expiry')
        ->dailyAt('06:00')
        ->description('Check for expired subscriptions and send notifications');

// Weekly commission eligibility checks
$schedule->command('commissions:check-eligibility')
        ->weekly()
        ->description('Check commission eligibility for all users');

// Monthly cleanup of old logs and cache
$schedule->command('system:cleanup')
        ->monthly()
        ->description('Clean up old logs, cache, and temporary files');
```

### Enhanced Schedule Configuration
Let's enhance your scheduler with additional important tasks:

```php
protected function schedule(Schedule $schedule): void
{
    // Daily Tasks
    $schedule->command('subscriptions:check-expiry')
            ->dailyAt('06:00')
            ->description('Check for expired subscriptions and send notifications');
    
    $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->description('Process email and notification queues');
    
    $schedule->command('system:health-check')
            ->dailyAt('02:00')
            ->description('Check system health and send alerts');
    
    // Weekly Tasks
    $schedule->command('commissions:check-eligibility')
            ->weekly()
            ->description('Check commission eligibility for all users');
    
    $schedule->command('system:backup-database')
            ->weekly()
            ->description('Create database backup');
    
    $schedule->command('system:optimize-cache')
            ->weekly()
            ->description('Optimize application cache');
    
    // Monthly Tasks
    $schedule->command('commissions:process-monthly')
            ->monthlyOn(1, '00:00')
            ->description('Process monthly commissions for eligible users');
    
    $schedule->command('magazines:release-reminder')
            ->cron('0 9 1 */2 *')
            ->description('Send reminder to admins for bimonthly magazine release');
    
    $schedule->command('system:cleanup')
            ->monthly()
            ->description('Clean up old logs, cache, and temporary files');
    
    $schedule->command('system:generate-reports')
            ->monthlyOn(1, '01:00')
            ->description('Generate monthly reports and analytics');
    
    // Custom Schedules
    $schedule->command('system:clear-expired-reports')
            ->dailyAt('03:00')
            ->description('Clear expired report cache');
    
    $schedule->command('commissions:re-evaluate-eligibility')
            ->dailyAt('04:00')
            ->description('Re-evaluate commission eligibility for changes');
}
```

---

## 🔧 Individual Command Setup

### 1. System Health Check Command
Create a new command for system health monitoring:

```bash
php artisan make:command SystemHealthCheck
```

**File**: `app/Console/Commands/SystemHealthCheck.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Check system health and send alerts';

    public function handle()
    {
        $this->info('Starting system health check...');
        
        $issues = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->line('✓ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
            $this->error('✗ Database connection: FAILED');
        }
        
        // Check storage permissions
        if (Storage::disk('local')->put('health-check.txt', 'test')) {
            Storage::disk('local')->delete('health-check.txt');
            $this->line('✓ Storage permissions: OK');
        } else {
            $issues[] = 'Storage permissions failed';
            $this->error('✗ Storage permissions: FAILED');
        }
        
        // Check disk space
        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        $diskUsed = $diskTotal - $diskFree;
        $diskUsagePercent = ($diskUsed / $diskTotal) * 100;
        
        if ($diskUsagePercent > 90) {
            $issues[] = "Disk usage critical: {$diskUsagePercent}%";
            $this->warn("⚠ Disk usage: {$diskUsagePercent}%");
        } else {
            $this->line("✓ Disk usage: {$diskUsagePercent}%");
        }
        
        // Check queue status
        $queueSize = DB::table('jobs')->count();
        if ($queueSize > 1000) {
            $issues[] = "Queue size large: {$queueSize} jobs";
            $this->warn("⚠ Queue size: {$queueSize} jobs");
        } else {
            $this->line("✓ Queue size: {$queueSize} jobs");
        }
        
        // Send alerts if issues found
        if (!empty($issues)) {
            $this->sendHealthAlert($issues);
            Log::warning('System health check found issues', ['issues' => $issues]);
            return 1;
        }
        
        $this->info('System health check completed successfully');
        return 0;
    }
    
    private function sendHealthAlert(array $issues)
    {
        // Send email alert to administrators
        // Implementation depends on your email system
        $this->warn('Health alerts sent to administrators');
    }
}
```

### 2. Database Backup Command
Create a command for automated database backups:

```bash
php artisan make:command SystemBackupDatabase
```

**File**: `app/Console/Commands/SystemBackupDatabase.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SystemBackupDatabase extends Command
{
    protected $signature = 'system:backup-database';
    protected $description = 'Create database backup';

    public function handle()
    {
        $this->info('Starting database backup...');
        
        try {
            $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Ensure backup directory exists
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                config('database.connections.mysql.host'),
                config('database.connections.mysql.port'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $backupPath
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info("Database backup created: {$filename}");
                
                // Clean old backups (keep last 30 days)
                $this->cleanOldBackups();
                
                return 0;
            } else {
                throw new \Exception('mysqldump command failed');
            }
            
        } catch (\Exception $e) {
            $this->error('Database backup failed: ' . $e->getMessage());
            Log::error('Database backup failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
    
    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/*.sql');
        
        foreach ($files as $file) {
            if (filemtime($file) < now()->subDays(30)->timestamp) {
                unlink($file);
                $this->line("Removed old backup: " . basename($file));
            }
        }
    }
}
```

### 3. Cache Optimization Command
Create a command for cache optimization:

```bash
php artisan make:command SystemOptimizeCache
```

**File**: `app/Console/Commands/SystemOptimizeCache.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SystemOptimizeCache extends Command
{
    protected $signature = 'system:optimize-cache';
    protected $description = 'Optimize application cache';

    public function handle()
    {
        $this->info('Starting cache optimization...');
        
        try {
            // Clear expired cache
            Cache::flush();
            $this->line('✓ Expired cache cleared');
            
            // Optimize Laravel
            Artisan::call('config:cache');
            $this->line('✓ Configuration cached');
            
            Artisan::call('route:cache');
            $this->line('✓ Routes cached');
            
            Artisan::call('view:cache');
            $this->line('✓ Views cached');
            
            // Clear old report cache
            Artisan::call('system:clear-expired-reports');
            $this->line('✓ Expired reports cleared');
            
            $this->info('Cache optimization completed successfully');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Cache optimization failed: ' . $e->getMessage());
            Log::error('Cache optimization failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}
```

### 4. Report Generation Command
Create a command for automated report generation:

```bash
php artisan make:command SystemGenerateReports
```

**File**: `app/Console/Commands/SystemGenerateReports.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Magazine;
use Carbon\Carbon;

class SystemGenerateReports extends Command
{
    protected $signature = 'system:generate-reports';
    protected $description = 'Generate monthly reports and analytics';

    public function handle()
    {
        $this->info('Starting monthly report generation...');
        
        try {
            $lastMonth = now()->subMonth();
            $startDate = $lastMonth->startOfMonth();
            $endDate = $lastMonth->endOfMonth();
            
            // Generate user statistics
            $userStats = $this->generateUserStats($startDate, $endDate);
            
            // Generate financial statistics
            $financialStats = $this->generateFinancialStats($startDate, $endDate);
            
            // Generate content statistics
            $contentStats = $this->generateContentStats($startDate, $endDate);
            
            // Store reports in database
            $this->storeReports($userStats, $financialStats, $contentStats);
            
            // Send report notifications to administrators
            $this->sendReportNotifications($userStats, $financialStats, $contentStats);
            
            $this->info('Monthly reports generated successfully');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Report generation failed: ' . $e->getMessage());
            Log::error('Report generation failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
    
    private function generateUserStats($startDate, $endDate)
    {
        return [
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => User::where('last_login_at', '>=', $startDate)->count(),
            'total_users' => User::count(),
        ];
    }
    
    private function generateFinancialStats($startDate, $endDate)
    {
        return [
            'total_revenue' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('amount'),
            'total_transactions' => Transaction::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_commissions' => Commission::whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
        ];
    }
    
    private function generateContentStats($startDate, $endDate)
    {
        return [
            'total_magazines' => Magazine::count(),
            'active_magazines' => Magazine::where('status', 'active')->count(),
            'total_downloads' => Magazine::sum('download_count'),
        ];
    }
    
    private function storeReports($userStats, $financialStats, $contentStats)
    {
        // Store in ReportCache model or similar
        // Implementation depends on your reporting system
    }
    
    private function sendReportNotifications($userStats, $financialStats, $contentStats)
    {
        // Send email notifications to administrators
        // Implementation depends on your email system
    }
}
```

---

## 🖥️ Server Cron Configuration

### Linux/macOS Cron Setup

#### Step 1: Access Crontab
```bash
# Edit crontab for your web server user (usually www-data, apache, or nginx)
sudo crontab -u www-data -e

# Or for your application user
crontab -e
```

#### Step 2: Add Laravel Scheduler
Add this line to your crontab:

```bash
# Laravel Scheduler - Run every minute
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

# Alternative with logging
* * * * * cd /path/to/your/project && php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1
```

#### Step 3: Verify Cron Service
```bash
# Check if cron service is running
sudo systemctl status cron

# Start cron service if not running
sudo systemctl start cron

# Enable cron service to start on boot
sudo systemctl enable cron
```

### Windows Task Scheduler Setup

#### Step 1: Open Task Scheduler
1. **Press**: `Win + R`
2. **Type**: `taskschd.msc`
3. **Press**: Enter

#### Step 2: Create Basic Task
1. **Right-click**: "Task Scheduler Library"
2. **Select**: "Create Basic Task"
3. **Name**: "Laravel Scheduler"
4. **Trigger**: Daily, every 1 minute
5. **Action**: Start a program
6. **Program**: `C:\path\to\php.exe`
7. **Arguments**: `C:\path\to\your\project\artisan schedule:run`

#### Step 3: Advanced Settings
1. **Right-click**: Created task
2. **Select**: "Properties"
3. **Check**: "Run with highest privileges"
4. **Set**: "Run whether user is logged on or not"

### Docker Cron Setup

If using Docker, add to your Dockerfile:

```dockerfile
# Install cron
RUN apt-get update && apt-get install -y cron

# Add crontab
COPY crontab /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler
RUN crontab /etc/cron.d/laravel-scheduler

# Start cron service
CMD ["cron", "-f"]
```

**File**: `crontab`
```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Testing & Verification

### Test Individual Commands
```bash
# Test system health check
php artisan system:health-check

# Test database backup
php artisan system:backup-database

# Test cache optimization
php artisan system:optimize-cache

# Test report generation
php artisan system:generate-reports

# Test system cleanup
php artisan system:cleanup
```

### Test Scheduler
```bash
# List all scheduled commands
php artisan schedule:list

# Test scheduler without running commands
php artisan schedule:run --verbose

# Run specific scheduled command
php artisan schedule:run --only="system:health-check"
```

### Verify Cron Execution
```bash
# Check cron logs
sudo tail -f /var/log/syslog | grep CRON

# Check Laravel scheduler logs
tail -f /var/log/laravel-scheduler.log

# Check if commands are running
ps aux | grep "php artisan schedule:run"
```

---

## 📊 Monitoring & Maintenance

### Daily Monitoring
- [ ] Check scheduler logs for errors
- [ ] Verify commands executed successfully
- [ ] Monitor system resources
- [ ] Check queue processing

### Weekly Monitoring
- [ ] Review command execution times
- [ ] Check disk space usage
- [ ] Verify backup creation
- [ ] Monitor system performance

### Monthly Monitoring
- [ ] Review all scheduled tasks
- [ ] Optimize command schedules
- [ ] Update maintenance procedures
- [ ] Review and update documentation

### Log Monitoring
```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor system logs
sudo tail -f /var/log/syslog

# Monitor cron logs
sudo tail -f /var/log/cron.log
```

---

## 🔧 Troubleshooting

### Common Issues

#### Issue 1: Scheduler Not Running
**Symptoms**: Commands not executing automatically
**Solutions**:
1. Check cron service status
2. Verify crontab entry
3. Check file permissions
4. Verify PHP path in crontab

#### Issue 2: Commands Failing
**Symptoms**: Commands run but fail
**Solutions**:
1. Check command logs
2. Verify database connectivity
3. Check file permissions
4. Test commands manually

#### Issue 3: High Resource Usage
**Symptoms**: System becomes slow during cron execution
**Solutions**:
1. Optimize command execution
2. Add resource limits
3. Schedule commands during off-peak hours
4. Monitor and adjust schedules

#### Issue 4: Permission Denied
**Symptoms**: Commands fail due to permissions
**Solutions**:
1. Check user permissions
2. Verify file ownership
3. Set proper file permissions
4. Use correct user in crontab

### Debug Commands
```bash
# Check cron service status
sudo systemctl status cron

# List current crontab
crontab -l

# Check cron logs
sudo tail -f /var/log/cron.log

# Test PHP execution
php -v

# Test Laravel artisan
php artisan --version

# Check file permissions
ls -la /path/to/your/project/artisan
```

---

## 🚀 Production Deployment

### Pre-Deployment Checklist
- [ ] All commands tested in staging
- [ ] Cron jobs configured on production server
- [ ] Monitoring and logging enabled
- [ ] Backup procedures tested
- [ ] Team trained on monitoring

### Production Configuration
```bash
# Production crontab entry
* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1

# Production log rotation
sudo nano /etc/logrotate.d/laravel-scheduler
```

**File**: `/etc/logrotate.d/laravel-scheduler`
```
/var/log/laravel-scheduler.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### Performance Optimization
```bash
# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper file permissions
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage
```

---

## 📚 Best Practices

### Scheduling Best Practices
1. **Avoid Peak Hours**: Schedule heavy tasks during off-peak hours
2. **Resource Management**: Monitor and limit resource usage
3. **Error Handling**: Implement proper error handling and logging
4. **Dependencies**: Consider command dependencies and order
5. **Monitoring**: Set up alerts for failed commands

### Command Best Practices
1. **Idempotent**: Commands should be safe to run multiple times
2. **Logging**: Implement comprehensive logging
3. **Error Handling**: Graceful error handling and recovery
4. **Performance**: Optimize for speed and efficiency
5. **Security**: Follow security best practices

### Maintenance Best Practices
1. **Regular Reviews**: Review and update schedules regularly
2. **Documentation**: Keep documentation up to date
3. **Testing**: Test changes in staging before production
4. **Backup**: Regular backup of cron configurations
5. **Monitoring**: Continuous monitoring and alerting

---

## 🆘 Support & Resources

### Internal Support
- **System Administrator**: For server configuration
- **IT Team**: For technical issues
- **Development Team**: For command logic

### External Resources
- **Laravel Documentation**: [https://laravel.com/docs/scheduling](https://laravel.com/docs/scheduling)
- **Cron Documentation**: [https://man7.org/linux/man-pages/man5/crontab.5.html](https://man7.org/linux/man-pages/man5/crontab.5.html)
- **Linux Cron**: [https://help.ubuntu.com/community/CronHowto](https://help.ubuntu.com/community/CronHowto)

### Emergency Contacts
- **Technical Issues**: Your IT team
- **System Problems**: Your system administrator
- **Command Issues**: Your development team

---

## ✅ Success Checklist

Before going live with cron jobs:

- [ ] All commands tested individually
- [ ] Scheduler tested in staging environment
- [ ] Cron service configured on production server
- [ ] Monitoring and logging enabled
- [ ] Backup procedures tested
- [ ] Team trained on monitoring and troubleshooting
- [ ] Documentation completed and distributed
- [ ] Performance baseline established
- [ ] Alert system configured
- [ ] Maintenance procedures defined

---

## 🎉 Congratulations!

You've successfully configured automated cron jobs for your Net On You system! 

**Next Steps**:
1. **Monitor** system performance and command execution
2. **Optimize** schedules based on usage patterns
3. **Train** your team on monitoring and troubleshooting
4. **Scale** automation as your system grows

**Remember**: Automated systems require monitoring and maintenance. Regular review and optimization will ensure your system runs smoothly and efficiently.

---

**Document Version**: 1.0  
**Last Updated**: January 2025  
**For Support**: Contact your system administrator or IT team

