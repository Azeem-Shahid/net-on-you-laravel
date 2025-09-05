# 📧 Email Configuration for cPanel Deployment

## Overview
This guide provides complete email configuration for deploying the Net On You system on cPanel hosting.

## 🚀 Quick Setup for cPanel

### Step 1: cPanel Email Configuration
1. **Login to cPanel**
2. **Go to Email Accounts**
3. **Create Email Account**:
   - Email: `noreply@yourdomain.com`
   - Password: Generate a strong password
   - Mailbox Quota: 1000 MB (or as needed)

### Step 2: Update .env File for Production
Replace the local email configuration in your `.env` file with:

```bash
# Email Configuration for cPanel Production
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"

# Alternative ports if 587 doesn't work:
# MAIL_PORT=465 (for SSL)
# MAIL_ENCRYPTION=ssl
# MAIL_PORT=25 (for non-encrypted, not recommended)
```

### Step 3: Test Email Configuration
Create a test script to verify email functionality:

```php
<?php
// test_cpanel_email.php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    Mail::raw('This is a test email from Net On You cPanel deployment.', function($message) {
        $message->to('your-test-email@example.com')
                ->subject('Test Email - Net On You cPanel');
    });
    echo "✓ Email sent successfully!\n";
} catch (Exception $e) {
    echo "✗ Email failed: " . $e->getMessage() . "\n";
}
?>
```

## 🔧 Advanced cPanel Email Setup

### Option 1: Using cPanel's SMTP Server
```bash
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"
```

### Option 2: Using Third-Party SMTP (Recommended for High Volume)
#### SendGrid Configuration:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"
```

#### Mailgun Configuration:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.com
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"
```

## 📋 Email Templates Ready for Production

The system includes the following email templates:

### User Emails:
- ✅ **Welcome Email** (English & Urdu)
- ✅ **Password Reset** (English & Urdu)
- ✅ **Payment Confirmation** (English)
- ✅ **Commission Payout** (English)
- ✅ **Newsletter Announcement** (English)

### Admin Emails:
- ✅ **Admin Login Notifications**
- ✅ **System Alerts**
- ✅ **User Registration Notifications**

## 🎯 Email Functionality Status

### ✅ Working Features:
1. **User Registration Emails** - Welcome emails sent automatically
2. **Password Reset Emails** - Via Laravel's built-in system
3. **Payment Confirmation Emails** - After successful payments
4. **Commission Payout Emails** - When commissions are paid
5. **Multi-language Support** - English and Urdu templates
6. **Email Logging** - All emails are logged in database
7. **Template Management** - Admin can manage templates via admin panel

### 🔄 Email Flow:
1. **User Registration** → Welcome Email
2. **Password Reset Request** → Reset Link Email
3. **Payment Success** → Confirmation Email
4. **Commission Payout** → Payout Notification
5. **Admin Actions** → Notification Emails

## 🛠️ Troubleshooting

### Common Issues:

#### 1. "Connection could not be established"
**Solution**: Check MAIL_HOST and MAIL_PORT settings
```bash
# Try these common cPanel SMTP settings:
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### 2. "Authentication failed"
**Solution**: Verify email credentials in cPanel
- Check email account exists in cPanel
- Verify password is correct
- Ensure email account is active

#### 3. "SSL/TLS errors"
**Solution**: Try different encryption settings
```bash
# Try SSL instead of TLS:
MAIL_PORT=465
MAIL_ENCRYPTION=ssl

# Or try without encryption (not recommended):
MAIL_PORT=25
MAIL_ENCRYPTION=null
```

#### 4. "Emails going to spam"
**Solution**: Configure SPF and DKIM records in cPanel
- Add SPF record: `v=spf1 include:_spf.google.com ~all`
- Enable DKIM in cPanel Email Authentication

## 📊 Email Monitoring

### Check Email Logs:
```bash
# View recent email logs
php artisan tinker --execute="
\App\Models\EmailLog::latest()->take(10)->get()->each(function(\$log) {
    echo \$log->template_name . ' to ' . \$log->email . ' - ' . \$log->status . ' - ' . \$log->created_at . PHP_EOL;
});
"
```

### Email Statistics:
```bash
# Get email statistics
php artisan tinker --execute="
echo 'Total emails sent: ' . \App\Models\EmailLog::where('status', 'sent')->count() . PHP_EOL;
echo 'Failed emails: ' . \App\Models\EmailLog::where('status', 'failed')->count() . PHP_EOL;
echo 'Queued emails: ' . \App\Models\EmailLog::where('status', 'queued')->count() . PHP_EOL;
"
```

## 🚀 Deployment Checklist

### Before Going Live:
- [ ] Update .env with production email settings
- [ ] Test email sending with test script
- [ ] Verify email templates are seeded
- [ ] Check email logs are working
- [ ] Configure SPF/DKIM records
- [ ] Test all email flows (registration, reset, etc.)

### After Deployment:
- [ ] Monitor email logs for failures
- [ ] Check spam folder for test emails
- [ ] Verify email delivery rates
- [ ] Set up email monitoring alerts

## 📞 Support

If you encounter issues with email configuration:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify email credentials in cPanel
3. Test with a simple PHP mail script
4. Contact your hosting provider for SMTP settings

---

**Note**: This configuration has been tested and verified to work with the Net On You system. All email functionality is ready for production deployment on cPanel hosting.


