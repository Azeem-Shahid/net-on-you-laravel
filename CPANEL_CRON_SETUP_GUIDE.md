# 🌐 **cPanel Cron Job Setup Guide for Net On You**

## 🎯 **Overview**

This guide shows you how to set up cron jobs in **cPanel** using **web URLs** instead of command line commands. This is the standard approach for shared hosting environments where you don't have direct server access.

## 🔑 **Key Difference: cPanel vs Command Line**

### **Traditional Command Line Cron (VPS/Dedicated)**
```bash
* * * * * cd /path/to/project && php artisan schedule:run
```

### **cPanel Web URL Cron (Shared Hosting)**
```
* * * * * curl -s "https://yoursite.com/cron" > /dev/null 2>&1
```

## 🚀 **Available Cron URLs**

### **1. Main Cron Endpoint (Runs All Active Commands)**
```
https://yoursite.com/cron
```
**Purpose**: Executes all scheduled commands that are due to run
**Use Case**: Main cron job that runs every minute

### **2. Maintenance Commands**
```
https://yoursite.com/cron/maintenance
```
**Purpose**: Runs system maintenance commands
**Commands**: `system:cleanup`, `system:health-check`, `system:optimize-cache`, `system:clear-expired-reports`
**Use Case**: Daily or weekly system maintenance

### **3. Update Commands**
```
https://yoursite.com/cron/update
```
**Purpose**: Runs system update and reporting commands
**Commands**: `system:generate-reports`, `system:backup-database`, `subscriptions:check-expiry`, `commissions:check-eligibility`
**Use Case**: Weekly or monthly updates

### **4. Business Operations**
```
https://yoursite.com/cron/business
```
**Purpose**: Runs essential business logic commands
**Commands**: `subscriptions:check-expiry`, `commissions:check-eligibility`, `commissions:re-evaluate-eligibility`
**Use Case**: Daily business operations

### **5. Specific Command Execution**
```
https://yoursite.com/cron/command/{command-name}
```
**Examples**:
- `https://yoursite.com/cron/command/system:cleanup`
- `https://yoursite.com/cron/command/subscriptions:check-expiry`
- `https://yoursite.com/cron/command/commissions:process-monthly`

## 📋 **Step-by-Step cPanel Setup**

### **Step 1: Access cPanel Cron Jobs**
1. **Login to cPanel**
2. **Find "Cron Jobs"** in the Advanced section
3. **Click "Cron Jobs"**

### **Step 2: Add Main Cron Job**
1. **Common Settings**: Select "Every Minute (* * * * *)"
2. **Command**: `curl -s "https://yoursite.com/cron" > /dev/null 2>&1`
3. **Click "Add New Cron Job"**

### **Step 3: Add Maintenance Cron Job**
1. **Common Settings**: Select "Once a Day (0 2 * * *)" (2 AM)
2. **Command**: `curl -s "https://yoursite.com/cron/maintenance" > /dev/null 2>&1`
3. **Click "Add New Cron Job"**

### **Step 4: Add Business Operations Cron Job**
1. **Common Settings**: Select "Once a Day (0 6 * * *)" (6 AM)
2. **Command**: `curl -s "https://yoursite.com/cron/business" > /dev/null 2>&1`
3. **Click "Add New Cron Job"**

### **Step 5: Add Weekly Update Cron Job**
1. **Common Settings**: Select "Once a Week (0 3 * * 1)" (Monday 3 AM)
2. **Command**: `curl -s "https://yoursite.com/cron/update" > /dev/null 2>&1`
3. **Click "Add New Cron Job"**

## ⏰ **Recommended Cron Schedule**

| **Purpose** | **Frequency** | **Time** | **URL** | **Description** |
|-------------|---------------|----------|---------|-----------------|
| **Main Scheduler** | Every Minute | * * * * * | `/cron` | Runs all due commands |
| **System Maintenance** | Daily | 0 2 * * * | `/cron/maintenance` | Cleanup and health checks |
| **Business Operations** | Daily | 0 6 * * * | `/cron/business` | Subscription and commission checks |
| **Weekly Updates** | Weekly | 0 3 * * 1 | `/cron/update` | Reports and backups |
| **Monthly Processing** | Monthly | 0 1 1 * * | `/cron/command/commissions:process-monthly` | Monthly commissions |

## 🔒 **Security: Adding Secret Key (Optional)**

For additional security, you can add a secret key to your cron URLs:

### **1. Set Secret in .env File**
```env
CRON_SECRET=your-super-secret-key-here
```

### **2. Use Secret in Cron Commands**
```bash
# With secret
curl -s "https://yoursite.com/cron?secret=your-super-secret-key-here" > /dev/null 2>&1

# Without secret (still works but less secure)
curl -s "https://yoursite.com/cron" > /dev/null 2>&1
```

## 🧪 **Testing Your Cron Jobs**

### **Test 1: Manual URL Testing**
1. **Open your browser**
2. **Navigate to**: `https://yoursite.com/cron`
3. **Expected Result**: JSON response showing execution results

### **Test 2: Check Laravel Logs**
```bash
# View recent logs
tail -f storage/logs/laravel.log

# Look for cron execution entries
grep "Cron job executed" storage/logs/laravel.log
```

### **Test 3: Check Admin Panel**
1. **Go to**: `/admin/command-scheduler`
2. **Check**: Recent logs and execution times
3. **Verify**: Commands are running automatically

## 🚨 **Troubleshooting Common Issues**

### **Issue 1: Cron Jobs Not Running**
**Symptoms**: Commands not executing automatically
**Solutions**:
1. **Check cPanel cron job status**
2. **Verify URL accessibility** (test in browser)
3. **Check server error logs**
4. **Verify cron service is active**

### **Issue 2: Commands Failing**
**Symptoms**: Cron runs but commands fail
**Solutions**:
1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Verify database connectivity**
3. **Check file permissions**
4. **Review command dependencies**

### **Issue 3: Permission Denied**
**Symptoms**: 403 or 500 errors
**Solutions**:
1. **Check .env file permissions**
2. **Verify storage directory permissions**
3. **Check web server configuration**
4. **Review Laravel cache settings**

### **Issue 4: Slow Execution**
**Symptoms**: Commands take too long
**Solutions**:
1. **Optimize command performance**
2. **Use appropriate cron frequencies**
3. **Monitor server resources**
4. **Consider using queues for heavy tasks**

## 📱 **Mobile-Friendly Cron Management**

### **Admin Panel Features**
- **Real-time Monitoring**: View command execution status
- **Manual Execution**: Run commands on-demand
- **Batch Operations**: Execute multiple commands together
- **Log Management**: View and export execution logs
- **Performance Analytics**: Track execution times and success rates

### **Quick Actions**
- **System Maintenance**: One-click maintenance execution
- **Business Operations**: Daily business logic execution
- **Custom Groups**: Create your own command combinations
- **Scheduling**: Set command frequencies and timing

## 🔧 **Advanced Configuration**

### **Custom Cron Expressions**
If you need custom timing, you can use specific cron expressions:

```bash
# Every 15 minutes
*/15 * * * * curl -s "https://yoursite.com/cron" > /dev/null 2>&1

# Every 2 hours
0 */2 * * * curl -s "https://yoursite.com/cron" > /dev/null 2>&1

# Weekdays only
0 6 * * 1-5 curl -s "https://yoursite.com/cron/business" > /dev/null 2>&1
```

### **Multiple Server Setup**
If you have multiple servers, you can set up cron jobs on each:

```bash
# Server 1: Main operations
* * * * * curl -s "https://yoursite.com/cron" > /dev/null 2>&1

# Server 2: Backup operations
0 2 * * * curl -s "https://yoursite.com/cron/update" > /dev/null 2>&1
```

## 📊 **Monitoring & Alerts**

### **Built-in Monitoring**
- **Execution Logs**: All cron executions are logged
- **Performance Metrics**: Track execution times
- **Success Rates**: Monitor command reliability
- **Error Tracking**: Identify and resolve failures

### **External Monitoring**
You can use external monitoring services to check if your cron URLs are accessible:

```bash
# UptimeRobot, Pingdom, or similar
https://yoursite.com/cron
https://yoursite.com/cron/maintenance
https://yoursite.com/cron/business
```

## 🎯 **Best Practices**

### **1. Timing Optimization**
- **Peak Hours**: Avoid running heavy commands during peak usage
- **Server Load**: Distribute commands across different times
- **Resource Usage**: Monitor server impact during execution

### **2. Error Handling**
- **Log Everything**: Ensure all executions are logged
- **Monitor Failures**: Set up alerts for failed commands
- **Graceful Degradation**: Handle command failures gracefully

### **3. Security**
- **Use HTTPS**: Always use secure URLs
- **Secret Keys**: Implement secret verification for sensitive operations
- **Access Control**: Limit cron URL access to necessary IPs if possible

### **4. Performance**
- **Command Optimization**: Optimize individual commands
- **Batch Processing**: Use batch execution for related commands
- **Queue Integration**: Use Laravel queues for heavy operations

## 🚀 **Quick Start Checklist**

- [ ] **Set up main cron job** (every minute)
- [ ] **Configure maintenance cron** (daily at 2 AM)
- [ ] **Set up business operations** (daily at 6 AM)
- [ ] **Add weekly updates** (Monday at 3 AM)
- [ ] **Test all URLs manually**
- [ ] **Verify logs are being created**
- [ ] **Check admin panel for execution status**
- [ ] **Monitor performance and adjust timing**

## 🎉 **Success Indicators**

Your cron jobs are working correctly when you see:

✅ **Automatic execution** of scheduled commands  
✅ **Regular log entries** in Laravel logs  
✅ **Updated statistics** in admin panel  
✅ **Commands running** at scheduled times  
✅ **No manual intervention** required for basic operations  

## 📞 **Support & Troubleshooting**

### **If You Need Help**
1. **Check this guide** for common solutions
2. **Review Laravel logs** for error details
3. **Test URLs manually** in your browser
4. **Contact your hosting provider** for cPanel issues
5. **Check our documentation** for advanced configuration

---

**Remember**: cPanel cron jobs use **web URLs**, not command line commands. This makes them perfect for shared hosting environments where you don't have direct server access.

**Your cron jobs are now web-based and fully integrated with your admin panel!** 🎯
