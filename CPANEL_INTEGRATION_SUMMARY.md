# 🚀 NetOnYou - cPanel Integration Summary

## 🌐 **Domain Information**
- **Domain**: netonyou.com
- **cPanel URL**: https://netonyou.com/cpanel
- **Admin Panel**: https://netonyou.com/admin
- **Command Scheduler**: https://netonyou.com/admin/command-scheduler

## 📋 **System Core Maintenance Command**

### **Command Name**: `system:core-maintenance`

### **What it does**: Runs **ALL 11 core system operations** in a single execution:

1. 📋 **Subscription Checks** - Check for expired subscriptions and send notifications
2. 💰 **Commission Eligibility** - Check commission eligibility for all users  
3. 💳 **Commission Processing** - Process monthly commissions for eligible users
4. 🔄 **Commission Re-evaluation** - Re-evaluate commission eligibility for changes
5. 📰 **Magazine Reminders** - Send reminder to admins for bimonthly magazine release
6. 🧹 **System Cleanup** - Clean up old logs, cache, and temporary files
7. 🏥 **Health Check** - Check system health and send alerts
8. 💾 **Database Backup** - Create database backup
9. ⚡ **Cache Optimization** - Optimize application cache
10. 📊 **Report Generation** - Generate monthly reports and analytics
11. 🗑️ **Clear Expired Reports** - Clear expired report cache

## 🔧 **Exact cPanel Setup**

### **Step 1: Access cPanel**
1. Go to: `https://netonyou.com/cpanel`
2. Login with your credentials
3. Navigate to **Advanced** → **Cron Jobs**

### **Step 2: Add New Cron Job**

#### **For Daily Execution (Recommended)**:
- **Common Settings**: Select "Once a Day (0 6 * * *)"
- **Command**: 
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

#### **For Weekly Execution**:
- **Common Settings**: Select "Once a Week (0 6 * * 1)"
- **Command**: 
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

#### **For Monthly Execution**:
- **Common Settings**: Select "Once a Month (0 6 1 * *)"
- **Command**: 
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

### **Step 3: Manual Cron Entry (Alternative)**
If you have SSH access:

```bash
# Edit crontab
crontab -e

# Add this line for daily execution at 6:00 AM
0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

## 📊 **Monitoring & Testing**

### **Test the Command Manually**:
```bash
cd /home/netonyou/public_html && php artisan system:core-maintenance
```

### **Check Command Exists**:
```bash
cd /home/netonyou/public_html && php artisan list | grep system:core-maintenance
```

### **View Execution Logs**:
```bash
tail -f /home/netonyou/public_html/storage/logs/laravel.log | grep "System Core Maintenance"
```

### **Check Scheduled Command in Database**:
```bash
cd /home/netonyou/public_html && php artisan tinker
>>> App\Models\ScheduledCommand::where('command', 'system:core-maintenance')->first()
```

## 🎯 **Admin Panel Integration**

### **Access Command Scheduler**:
1. Go to: `https://netonyou.com/admin`
2. Navigate to: **System Scheduler** → **Command Scheduler**
3. You'll see the "Core Maintenance" button in Quick Actions
4. Click to run manually or view execution history

### **View Execution History**:
1. In Command Scheduler, click **Recent Logs**
2. Look for `system:core-maintenance` entries
3. View detailed execution results and success rates

## ⚡ **Quick Commands Reference**

### **All Individual Commands**:
```bash
# Test individual commands
php artisan subscriptions:check-expiry
php artisan commissions:check-eligibility
php artisan commissions:process-monthly
php artisan commissions:re-evaluate-eligibility
php artisan magazines:release-reminder
php artisan system:cleanup
php artisan system:health-check
php artisan system:backup-database
php artisan system:optimize-cache
php artisan system:generate-reports
php artisan reports:clear-expired
```

### **Run Combined Command**:
```bash
php artisan system:core-maintenance
```

## 🔍 **Troubleshooting**

### **Common Issues**:

1. **Command Not Found**:
   ```bash
   cd /home/netonyou/public_html && php artisan list | grep system:core-maintenance
   ```

2. **Permission Issues**:
   ```bash
   chmod +x /home/netonyou/public_html/artisan
   chmod -R 755 /home/netonyou/public_html/storage/logs
   ```

3. **Path Issues**:
   - Ensure the path is exactly: `/home/netonyou/public_html`
   - Not `/home/username/public_html` or any other variation

4. **Log File Issues**:
   ```bash
   chmod -R 755 /home/netonyou/public_html/storage/logs
   ```

## 📈 **Expected Results**

### **Successful Execution Shows**:
```
🚀 Starting System Core Maintenance...
📋 Checking subscriptions...
✅ Subscription checks completed successfully
💰 Checking commission eligibility...
✅ Commission eligibility check completed successfully
💳 Processing monthly commissions...
✅ Commission processing completed successfully
🔄 Re-evaluating commission eligibility...
✅ Commission re-evaluation completed successfully
📰 Sending magazine release reminders...
✅ Magazine release reminders completed successfully
🧹 Running system cleanup...
✅ System cleanup completed successfully
🏥 Running health check...
✅ Health check completed successfully
💾 Creating database backup...
✅ Database backup completed successfully
⚡ Optimizing cache...
✅ Cache optimization completed successfully
📊 Generating reports...
✅ Report generation completed successfully
🗑️ Clearing expired reports...
✅ Clear expired reports completed successfully

📋 System Core Maintenance Summary:
=====================================
✅ Subscription Checks
✅ Commission Eligibility
✅ Commission Processing
✅ Commission Re Evaluation
✅ Magazine Reminders
✅ System Cleanup
✅ Health Check
✅ Database Backup
✅ Cache Optimization
✅ Report Generation
✅ Clear Expired Reports

🎯 Success Rate: 11/11 (100.0%)
⏱️ Total Execution Time: 2500ms
📅 Completed at: 2025-09-01 18:30:00
```

## 🎉 **Benefits**

- **🔄 Automated**: No manual intervention needed
- **📊 Comprehensive**: All 11 core functions in one command
- **⏰ Consistent**: Runs at the same time every day/week/month
- **📈 Monitored**: Full execution tracking and logging
- **🛡️ Reliable**: Robust error handling and recovery
- **💰 Business Ready**: Handles all subscription, commission, and system operations

## 📞 **Support**

If you encounter issues:
1. Check logs: `/home/netonyou/public_html/storage/logs/laravel.log`
2. Test manually: `cd /home/netonyou/public_html && php artisan system:core-maintenance`
3. Check admin panel: `https://netonyou.com/admin/command-scheduler`
4. Verify cron job syntax and timing
5. Check file permissions and paths

---

**Last Updated**: September 1, 2025  
**Version**: 2.0  
**Status**: Production Ready ✅  
**Domain**: netonyou.com  
**Commands Included**: 11  
**Execution Time**: ~2-5 minutes  
**Recommended Schedule**: Daily at 6:00 AM
