# System Core Maintenance - Cron Job Setup Guide

## 🚀 Overview

The `system:core-maintenance` command is a comprehensive maintenance command that runs **ALL** core system functionalities in a single execution. This command combines **11 different operations**:

### 📋 **Included Commands:**

1. **📋 Subscription Checks** - `subscriptions:check-expiry` - Check for expired subscriptions and send notifications
2. **💰 Commission Eligibility** - `commissions:check-eligibility` - Check commission eligibility for all users
3. **💳 Commission Processing** - `commissions:process-monthly` - Process monthly commissions for eligible users
4. **🔄 Commission Re-evaluation** - `commissions:re-evaluate-eligibility` - Re-evaluate commission eligibility for changes
5. **📰 Magazine Reminders** - `magazines:release-reminder` - Send reminder to admins for bimonthly magazine release
6. **🧹 System Cleanup** - `system:cleanup` - Clean up old logs, cache, and temporary files
7. **🏥 Health Check** - `system:health-check` - Check system health and send alerts
8. **💾 Database Backup** - `system:backup-database` - Create database backup
9. **⚡ Cache Optimization** - `system:optimize-cache` - Optimize application cache
10. **📊 Report Generation** - `system:generate-reports` - Generate monthly reports and analytics
11. **🗑️ Clear Expired Reports** - `reports:clear-expired` - Clear expired report cache

## 🌐 **Exact cPanel Integration URL**

### **For netonyou domain:**

```bash
# Daily Execution (Recommended) - 6:00 AM
0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1

# Weekly Execution - Monday 6:00 AM
0 6 * * 1 cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1

# Monthly Execution - 1st of month 6:00 AM
0 6 1 * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

## 📅 Scheduling Options

### Option 1: Daily Execution (Recommended)
```bash
# Run daily at 6:00 AM
0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

### Option 2: Weekly Execution
```bash
# Run weekly on Monday at 6:00 AM
0 6 * * 1 cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

### Option 3: Monthly Execution
```bash
# Run monthly on the 1st at 6:00 AM
0 6 1 * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

## 🔧 Setup Instructions

### 1. Access cPanel Cron Jobs
1. Log into your cPanel account at `https://netonyou.com/cpanel`
2. Navigate to **Advanced** → **Cron Jobs**
3. Click **Add New Cron Job**

### 2. Configure the Cron Job

#### For Daily Execution:
- **Common Settings**: Select "Once a Day (0 6 * * *)"
- **Command**: 
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

#### For Custom Timing:
- **Minute**: `0`
- **Hour**: `6` (6:00 AM)
- **Day**: `*` (every day)
- **Month**: `*` (every month)
- **Weekday**: `*` (every day of the week)
- **Command**: 
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

### 3. Alternative: Manual Cron Entry
If you have SSH access, you can add it directly to crontab:

```bash
# Edit crontab
crontab -e

# Add the line
0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

## 📊 Monitoring and Logs

### View Command Logs
The command execution is logged in Laravel's log files:

```bash
# View recent logs
tail -f /home/netonyou/public_html/storage/logs/laravel.log | grep "System Core Maintenance"
```

### Check Execution Status
You can check if the command is running properly:

```bash
# Test the command manually
cd /home/netonyou/public_html && php artisan system:core-maintenance

# Check scheduled commands
cd /home/netonyou/public_html && php artisan tinker
>>> App\Models\ScheduledCommand::where('command', 'system:core-maintenance')->first()
```

### View Command Logs in Admin Panel
1. Go to **Admin Panel** → **System Scheduler** → **Command Scheduler**
2. Click **Recent Logs** to view execution history
3. The command will appear as `system:core-maintenance`

## 🎯 Command Features

### Comprehensive Execution
The command runs all 11 core maintenance tasks in sequence:
1. **Subscription Checks** - Expiry notifications
2. **Commission Eligibility** - Check all users' eligibility
3. **Commission Processing** - Monthly calculations
4. **Commission Re-evaluation** - Update eligibility for changes
5. **Magazine Reminders** - Admin notifications
6. **System Cleanup** - Log and cache cleanup
7. **Health Check** - System monitoring
8. **Database Backup** - Data protection
9. **Cache Optimization** - Performance improvement
10. **Report Generation** - Analytics and insights
11. **Clear Expired Reports** - Clean up old reports

### Error Handling
- Each task is executed independently
- Failures in one task don't stop others
- Detailed logging of success/failure for each task
- Overall success rate calculation

### Performance Monitoring
- Execution time tracking
- Success rate monitoring
- Detailed logging for troubleshooting

## 🔍 Troubleshooting

### Common Issues

#### 1. Command Not Found
```bash
# Verify command exists
cd /home/netonyou/public_html && php artisan list | grep system:core-maintenance
```

#### 2. Permission Issues
```bash
# Ensure proper permissions
chmod +x /home/netonyou/public_html/artisan
```

#### 3. Path Issues
Make sure the cron job uses the correct path: `/home/netonyou/public_html`

#### 4. Log File Issues
```bash
# Check if logs directory is writable
chmod -R 755 /home/netonyou/public_html/storage/logs
```

### Testing the Setup

#### 1. Manual Test
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance
```

#### 2. Check Scheduled Command
```bash
cd /home/netonyou/public_html && php artisan tinker
>>> App\Models\ScheduledCommand::where('command', 'system:core-maintenance')->first()
```

#### 3. Monitor Execution
```bash
# Watch for execution
tail -f /home/netonyou/public_html/storage/logs/laravel.log
```

## 📈 Benefits

### Automated Maintenance
- **No Manual Intervention** - Runs automatically
- **Comprehensive Coverage** - All 11 core functions in one command
- **Consistent Execution** - Same time every day/week/month

### System Health
- **Proactive Monitoring** - Catches issues early
- **Performance Optimization** - Regular cleanup and optimization
- **Data Protection** - Regular backups

### Business Operations
- **Subscription Management** - Automatic expiry notifications
- **Commission Processing** - Timely payments and eligibility checks
- **Magazine Management** - Admin reminders
- **Reporting** - Regular analytics and insights

## 🎉 Success Indicators

When properly configured, you should see:
- ✅ Command appears in `php artisan list`
- ✅ Scheduled command in database with status "active"
- ✅ Regular execution logs in `storage/logs/laravel.log`
- ✅ Command logs visible in admin panel
- ✅ Successful execution of all 11 maintenance tasks

## 📞 Support

If you encounter issues:
1. Check the Laravel logs: `/home/netonyou/public_html/storage/logs/laravel.log`
2. Verify cron job syntax
3. Test command manually: `cd /home/netonyou/public_html && php artisan system:core-maintenance`
4. Check file permissions and paths
5. Review the admin panel logs for detailed execution history

## 🔗 Quick Links

- **Admin Panel**: `https://netonyou.com/admin`
- **Command Scheduler**: `https://netonyou.com/admin/command-scheduler`
- **cPanel**: `https://netonyou.com/cpanel`

---

**Last Updated**: September 1, 2025  
**Version**: 2.0  
**Status**: Production Ready ✅  
**Domain**: netonyou.com  
**Commands Included**: 11  
**Execution Time**: ~2-5 minutes
