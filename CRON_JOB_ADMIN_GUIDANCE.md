# 🚀 Cron Job Management - Admin Guidance

## 📋 Overview

This guide provides comprehensive instructions for managing automated tasks (cron jobs) in your NetOnYou system. The system is divided into **Business Operations** and **System Maintenance** commands, each with specific purposes and scheduling requirements.

---

## 🎯 Quick Access URLs

### **Admin Panel Access**
- **Main Admin Panel**: `https://netonyou.com/admin`
- **Cron Job Management**: `https://netonyou.com/admin/cron-jobs`
- **System Scheduler**: `https://netonyou.com/admin/command-scheduler`
- **cPanel Access**: `https://netonyou.com/cpanel`

---

## 💼 Business Operations Commands

### **1. Subscription Management**
**Command**: `subscriptions:check-expiry`
- **Purpose**: Check for expired subscriptions and send notifications
- **Frequency**: Daily at 6:00 AM
- **cPanel Command**: 
```bash
0 6 * * * cd /home/netonyou/public_html && php artisan subscriptions:check-expiry >> /dev/null 2>&1
```
- **Setup URL**: `https://netonyou.com/cpanel` → Advanced → Cron Jobs
- **Test Command**: `cd /home/netonyou/public_html && php artisan subscriptions:check-expiry`

### **2. Commission Processing**
**Command**: `commissions:check-eligibility`
- **Purpose**: Check commission eligibility for all users
- **Frequency**: Weekly (Monday) at 6:00 AM
- **cPanel Command**:
```bash
0 6 * * 1 cd /home/netonyou/public_html && php artisan commissions:check-eligibility >> /dev/null 2>&1
```
- **Setup URL**: `https://netonyou.com/cpanel` → Advanced → Cron Jobs

**Command**: `commissions:process-monthly`
- **Purpose**: Process monthly commissions for eligible users
- **Frequency**: Monthly (1st) at 12:00 AM
- **cPanel Command**:
```bash
0 0 1 * * cd /home/netonyou/public_html && php artisan commissions:process-monthly >> /dev/null 2>&1
```

**Command**: `commissions:re-evaluate-eligibility`
- **Purpose**: Re-evaluate commission eligibility for changes
- **Frequency**: Daily at 4:00 AM
- **cPanel Command**:
```bash
0 4 * * * cd /home/netonyou/public_html && php artisan commissions:re-evaluate-eligibility >> /dev/null 2>&1
```

### **3. Magazine Management**
**Command**: `magazines:release-reminder`
- **Purpose**: Send reminder to admins for bimonthly magazine release
- **Frequency**: Bimonthly (1st of every 2nd month) at 9:00 AM
- **cPanel Command**:
```bash
0 9 1 */2 * cd /home/netonyou/public_html && php artisan magazines:release-reminder >> /dev/null 2>&1
```

### **4. Report Management**
**Command**: `reports:clear-expired`
- **Purpose**: Clear expired report cache entries
- **Frequency**: Daily at 3:00 AM
- **cPanel Command**:
```bash
0 3 * * * cd /home/netonyou/public_html && php artisan reports:clear-expired >> /dev/null 2>&1
```

---

## ⚙️ System Maintenance Commands

### **1. Core Maintenance (Combined)**
**Command**: `system:core-maintenance`
- **Purpose**: Run comprehensive system core maintenance (all operations)
- **Frequency**: Daily at 6:00 AM
- **cPanel Command**:
```bash
0 6 * * * cd /home/netonyou/public_html && php artisan system:core-maintenance >> /dev/null 2>&1
```

### **2. Individual System Commands**
**Command**: `system:cleanup`
- **Purpose**: Clean up old logs, cache, and temporary files
- **Frequency**: Weekly (Sunday) at 2:00 AM
- **cPanel Command**:
```bash
0 2 * * 0 cd /home/netonyou/public_html && php artisan system:cleanup >> /dev/null 2>&1
```

**Command**: `system:health-check`
- **Purpose**: Check system health and send alerts
- **Frequency**: Daily at 2:00 AM
- **cPanel Command**:
```bash
0 2 * * * cd /home/netonyou/public_html && php artisan system:health-check >> /dev/null 2>&1
```

**Command**: `system:backup-database`
- **Purpose**: Create database backup
- **Frequency**: Daily at 1:00 AM
- **cPanel Command**:
```bash
0 1 * * * cd /home/netonyou/public_html && php artisan system:backup-database >> /dev/null 2>&1
```

**Command**: `system:optimize-cache`
- **Purpose**: Optimize application cache and performance
- **Frequency**: Weekly (Sunday) at 3:00 AM
- **cPanel Command**:
```bash
0 3 * * 0 cd /home/netonyou/public_html && php artisan system:optimize-cache >> /dev/null 2>&1
```

**Command**: `system:generate-reports`
- **Purpose**: Generate monthly reports and analytics
- **Frequency**: Monthly (1st) at 1:00 AM
- **cPanel Command**:
```bash
0 1 1 * * cd /home/netonyou/public_html && php artisan system:generate-reports >> /dev/null 2>&1
```

---

## 🔧 Setup Instructions

### **Step 1: Access cPanel**
1. Go to: `https://netonyou.com/cpanel`
2. Login with your credentials
3. Navigate to **Advanced** → **Cron Jobs**

### **Step 2: Add New Cron Job**
1. Click **Add New Cron Job**
2. **Common Settings**: Select the appropriate frequency
3. **Command**: Copy the exact command from the list above
4. Click **Add New Cron Job** to save

### **Step 3: Verify Setup**
1. **Test Command**: Run the command manually first
2. **Check Logs**: Monitor execution in admin panel
3. **Verify Schedule**: Confirm the cron job is active

---

## 📊 Monitoring & Management

### **Admin Panel Monitoring**
- **Cron Job Dashboard**: `https://netonyou.com/admin/cron-jobs`
  - View all commands and their status
  - Run commands manually
  - Get setup guides for each command
  - Monitor execution history

- **System Scheduler**: `https://netonyou.com/admin/command-scheduler`
  - Advanced command scheduling
  - Batch command execution
  - Detailed logs and statistics

### **Log Monitoring**
- **Laravel Logs**: `/home/netonyou/public_html/storage/logs/laravel.log`
- **Command Logs**: Available in admin panel
- **cPanel Logs**: Check cPanel error logs if needed

---

## 🎯 Recommended Setup Priority

### **High Priority (Essential Business Operations)**
1. **Subscription Checks** - Daily at 6:00 AM
2. **Commission Processing** - Monthly at 12:00 AM
3. **System Core Maintenance** - Daily at 6:00 AM
4. **Database Backup** - Daily at 1:00 AM

### **Medium Priority (Regular Maintenance)**
1. **Commission Eligibility** - Weekly on Monday
2. **System Cleanup** - Weekly on Sunday
3. **Health Checks** - Daily at 2:00 AM
4. **Cache Optimization** - Weekly on Sunday

### **Low Priority (Optional)**
1. **Magazine Reminders** - Bimonthly
2. **Report Generation** - Monthly
3. **Expired Reports Cleanup** - Daily at 3:00 AM

---

## 🔍 Troubleshooting

### **Common Issues**

#### **1. Command Not Found**
```bash
# Verify command exists
cd /home/netonyou/public_html && php artisan list | grep command-name
```

#### **2. Permission Issues**
```bash
# Ensure proper permissions
chmod +x /home/netonyou/public_html/artisan
chmod -R 755 /home/netonyou/public_html/storage/logs
```

#### **3. Path Issues**
- Ensure the path is exactly: `/home/netonyou/public_html`
- Not `/home/username/public_html` or any other variation

#### **4. Log File Issues**
```bash
# Check if logs directory is writable
chmod -R 755 /home/netonyou/public_html/storage/logs
```

### **Testing Commands**
```bash
# Test any command manually
cd /home/netonyou/public_html && php artisan command-name

# Check execution logs
tail -f /home/netonyou/public_html/storage/logs/laravel.log | grep "command-name"
```

---

## 📈 Performance Optimization

### **Execution Time Limits**
- **Business Commands**: 2-5 minutes each
- **System Commands**: 1-3 minutes each
- **Core Maintenance**: 5-10 minutes total

### **Resource Usage**
- **Memory**: Each command uses ~50-100MB
- **CPU**: Low to moderate usage
- **Database**: Minimal impact during off-peak hours

### **Scheduling Best Practices**
- **Avoid Peak Hours**: Schedule during low-traffic periods (1:00 AM - 6:00 AM)
- **Stagger Commands**: Don't run all commands at the same time
- **Monitor Performance**: Check logs for any performance issues

---

## 🛡️ Security Considerations

### **Command Security**
- All commands run with limited permissions
- No sensitive data in command output
- Logs are stored securely

### **Access Control**
- Only admin users can run commands manually
- Cron jobs run with system permissions
- All executions are logged and auditable

---

## 📞 Support & Maintenance

### **Regular Maintenance Tasks**
1. **Weekly**: Review execution logs
2. **Monthly**: Verify all cron jobs are running
3. **Quarterly**: Update command schedules if needed

### **Emergency Procedures**
1. **Stop All Commands**: Disable cron jobs in cPanel
2. **Check Logs**: Review recent execution logs
3. **Contact Support**: If issues persist

### **Backup Procedures**
- **Database Backups**: Automated daily at 1:00 AM
- **Log Backups**: Retained for 30 days
- **Configuration Backups**: Export cron job settings

---

## 🎉 Success Indicators

### **When Everything is Working Correctly**
- ✅ All commands execute successfully
- ✅ No error messages in logs
- ✅ Business operations run smoothly
- ✅ System performance remains optimal
- ✅ Regular backups are created
- ✅ Users receive timely notifications

### **Monitoring Checklist**
- [ ] Daily subscription checks complete
- [ ] Weekly commission processing successful
- [ ] Monthly reports generated
- [ ] System cleanup running regularly
- [ ] Database backups created daily
- [ ] No failed command executions
- [ ] All scheduled tasks on time

---

**Last Updated**: September 1, 2025  
**Version**: 1.0  
**Status**: Production Ready ✅  
**Domain**: netonyou.com  
**Admin Panel**: https://netonyou.com/admin  
**cPanel**: https://netonyou.com/cpanel
