# 📧 Complete Email Configuration Guide for Net On You

## 📋 Table of Contents
1. [Email Provider Setup](#email-provider-setup)
2. [Environment Configuration](#environment-configuration)
3. [Database Setup](#database-setup)
4. [Admin Panel Configuration](#admin-panel-configuration)
5. [Email Template Management](#email-template-management)
6. [Testing & Verification](#testing--verification)
7. [Production Deployment](#production-deployment)
8. [Troubleshooting](#troubleshooting)
9. [Best Practices](#best-practices)

---

## 🏗️ Email Provider Setup

### Option 1: Gmail SMTP (Recommended for Testing)

#### Step 1: Enable 2-Factor Authentication
1. **Go to**: [Google Account Settings](https://myaccount.google.com/)
2. **Navigate to**: Security → 2-Step Verification
3. **Enable** 2-Step Verification if not already enabled

#### Step 2: Generate App Password
1. **Go to**: Security → App Passwords
2. **Select**: Mail → Other (Custom name)
3. **Enter**: "Net On You Laravel"
4. **Copy** the generated 16-character password

#### Step 3: Configure .env
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Net On You"
```

### Option 2: SendGrid (Recommended for Production)

#### Step 1: Create SendGrid Account
1. **Visit**: [https://sendgrid.com](https://sendgrid.com)
2. **Sign up** for a free account
3. **Verify** your email address

#### Step 2: Create API Key
1. **Go to**: Settings → API Keys
2. **Click**: "Create API Key"
3. **Name**: "Net On You Production"
4. **Permissions**: "Restricted Access" → "Mail Send"
5. **Copy** the API key

#### Step 3: Verify Sender
1. **Go to**: Settings → Sender Authentication
2. **Verify** your domain or single sender
3. **Follow** the verification steps

#### Step 4: Configure .env
```bash
MAIL_MAILER=sendgrid
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"
```

### Option 3: Mailgun (Alternative Production Option)

#### Step 1: Create Mailgun Account
1. **Visit**: [https://mailgun.com](https://mailgun.com)
2. **Sign up** for an account
3. **Verify** your email

#### Step 2: Add Domain
1. **Go to**: Domains
2. **Click**: "Add New Domain"
3. **Enter**: your domain name
4. **Follow** DNS configuration steps

#### Step 3: Get API Key
1. **Go to**: Settings → API Keys
2. **Copy** your Private API Key

#### Step 4: Configure .env
```bash
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.com
MAIL_PASSWORD=your-mailgun-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"
```

### Option 4: Amazon SES (Enterprise Option)

#### Step 1: AWS Setup
1. **Create** AWS account
2. **Go to**: SES (Simple Email Service)
3. **Verify** your email address

#### Step 2: Get Credentials
1. **Go to**: IAM → Users
2. **Create** new user with SES permissions
3. **Generate** Access Key and Secret Key

#### Step 3: Configure .env
```bash
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"
```

---

## ⚙️ Environment Configuration

### Step 1: Update .env File
Choose your email provider and add the corresponding configuration:

```bash
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@netonyou.com
MAIL_FROM_NAME="Net On You"

# Queue Configuration (for better performance)
QUEUE_CONNECTION=database
QUEUE_DRIVER=database

# Logging Configuration
LOG_CHANNEL=stack
LOG_LEVEL=info
MAIL_LOG_CHANNEL=mail

# Email Module Settings
EMAIL_MODULE_ENABLED=true
EMAIL_TEMPLATES_ENABLED=true
EMAIL_CAMPAIGNS_ENABLED=true
EMAIL_LOGGING_ENABLED=true
```

### Step 2: Update config/mail.php
Ensure your `config/mail.php` includes the correct mailer configuration:

```php
'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
        'port' => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'timeout' => null,
        'local_domain' => env('MAIL_EHLO_DOMAIN'),
    ],
    
    'sendgrid' => [
        'transport' => 'sendgrid',
        'api_key' => env('MAIL_PASSWORD'),
    ],
    
    'mailgun' => [
        'transport' => 'mailgun',
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    
    'ses' => [
        'transport' => 'ses',
    ],
],

'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Example'),
],
```

---

## 🗄️ Database Setup

### Step 1: Run Migrations
```bash
# Navigate to your project directory
cd /path/to/your/project

# Run migrations for email module
php artisan migrate

# If you need to rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

### Step 2: Seed Default Templates
```bash
# Seed the database with default email templates
php artisan db:seed --class=EmailTemplateSeeder

# Or run all seeders
php artisan db:seed
```

### Step 3: Verify Database Tables
Check that these tables exist:
- `email_templates` (email template storage)
- `email_logs` (email delivery tracking)
- `notification_settings` (user preferences)
- `languages` (multi-language support)

---

## 🎛️ Admin Panel Configuration

### Step 1: Access Admin Panel
1. **Login** to: `https://yourdomain.com/admin`
2. **Use** your admin credentials

### Step 2: Configure Email Settings
1. **Navigate to**: Settings → Email Configuration
2. **Configure**:
   - **Email Provider**: Select your provider
   - **SMTP Host**: Your mail server
   - **SMTP Port**: Usually 587 or 465
   - **Username**: Your email username
   - **Password**: Your email password
   - **Encryption**: TLS or SSL
   - **From Address**: noreply@yourdomain.com
   - **From Name**: Net On You

### Step 3: Test Email Configuration
1. **Click**: "Test Email Configuration" button
2. **Enter**: Test email address
3. **Click**: "Send Test Email"
4. **Verify**: Test email received successfully

### Step 4: Enable Email Module
1. **Navigate to**: Settings → Module Management
2. **Enable**: Email Module
3. **Enable**: Email Templates
4. **Enable**: Email Campaigns
5. **Enable**: Email Logging

---

## 📝 Email Template Management

### Step 1: Access Email Templates
1. **Navigate to**: Email Management → Email Templates
2. **View** existing templates
3. **Click**: "Create New Template"

### Step 2: Create Email Template
1. **Fill in** template details:
   - **Name**: welcome_email (unique identifier)
   - **Language**: English (en)
   - **Subject**: Welcome to Net On You, {name}!
   - **Body**: HTML content with variables
   - **Variables**: Select from predefined list

2. **Template Variables Available**:
   - `{name}` - User's name
   - `{email}` - User's email
   - `{plan}` - Subscription plan
   - `{expiry}` - Account expiry date
   - `{amount}` - Payment amount
   - `{transaction_id}` - Transaction ID
   - `{reset_link}` - Password reset link
   - `{payout_id}` - Payout ID
   - `{date}` - Current date
   - `{company_name}` - Company name

### Step 3: Multi-Language Templates
1. **Create** template in primary language (English)
2. **Click**: "Duplicate Template"
3. **Change** language to target language
4. **Translate** subject and body content
5. **Save** the translated template

### Step 4: Test Email Template
1. **From** template list, click "Send Test"
2. **Enter** test email address
3. **Provide** test data for variables
4. **Click**: "Send Test Email"
5. **Verify** email received with correct formatting

---

## 🧪 Testing & Verification

### Step 1: Test Email Configuration
```bash
# Test mail configuration via artisan
php artisan tinker

# Test mail configuration
>>> Mail::raw('Test email from Net On You', function($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});

# Check if email was sent
>>> Mail::failures()
```

### Step 2: Test Email Templates
1. **Create** test user account
2. **Trigger** welcome email registration
3. **Verify** email received with correct template
4. **Check** variable replacement works

### Step 3: Test Email Logging
1. **Send** several test emails
2. **Navigate to**: Email Management → Email Logs
3. **Verify** logs show:
   - Email sent successfully
   - Template used
   - Recipient information
   - Timestamp

### Step 4: Test Multi-Language
1. **Change** user language preference
2. **Trigger** email in different language
3. **Verify** correct language template used
4. **Check** variable replacement in target language

---

## 🚀 Production Deployment

### Step 1: Pre-Deployment Checklist
- [ ] Email provider account created and verified
- [ ] API keys or credentials generated
- [ ] Sender email verified
- [ ] Domain authentication completed (if required)
- [ ] Database migration completed
- [ ] Admin panel configured
- [ ] Email templates created and tested
- [ ] Queue system configured (if using queues)

### Step 2: Update Production .env
```bash
# Production email settings
APP_ENV=production
APP_DEBUG=false

# Email Provider (choose one)
MAIL_MAILER=sendgrid  # or smtp, mailgun, ses
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-production-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"

# Queue Configuration
QUEUE_CONNECTION=database
QUEUE_DRIVER=database

# Logging
LOG_LEVEL=error
MAIL_LOG_CHANNEL=mail
```

### Step 3: Configure Queue System
```bash
# Create queue tables
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work --daemon

# Or use supervisor for production
sudo apt-get install supervisor
```

### Step 4: Final Verification
1. **Test** email sending with real email provider
2. **Verify** email delivery to inbox (not spam)
3. **Check** email logs show successful delivery
4. **Test** all email templates
5. **Verify** multi-language support works

---

## 🔧 Troubleshooting

### Common Issues & Solutions

#### Issue 1: Emails Not Sending
**Symptoms**: No emails sent, no error messages
**Solutions**:
- Check mail configuration in `.env`
- Verify SMTP credentials
- Check email logs for error messages
- Ensure queue worker is running (if using queues)
- Verify sender email is authenticated

#### Issue 2: Emails Going to Spam
**Symptoms**: Emails sent but marked as spam
**Solutions**:
- Verify sender domain with email provider
- Set up SPF, DKIM, and DMARC records
- Use consistent sender address
- Avoid spam trigger words
- Warm up email sending gradually

#### Issue 3: Template Variables Not Replacing
**Symptoms**: Variables show as {name} instead of actual values
**Solutions**:
- Verify variables are defined in template
- Check variable names match exactly
- Ensure data is passed correctly to EmailService
- Check template syntax

#### Issue 4: Multi-Language Not Working
**Symptoms**: Always uses default language template
**Solutions**:
- Verify user language preference is set
- Check template exists for target language
- Verify language codes match exactly
- Check template selection logic

### Debug Commands
```bash
# Check mail configuration
php artisan tinker
>>> config('mail')

# Test mail service
>>> app(\App\Services\EmailService::class)->isEnabled()

# Check email logs
>>> App\Models\EmailLog::latest()->take(5)->get()

# View recent logs
tail -f storage/logs/laravel.log | grep Mail
```

---

## 📊 Best Practices

### 1. Email Deliverability
- **Use** consistent sender address
- **Implement** proper authentication (SPF, DKIM, DMARC)
- **Avoid** spam trigger words
- **Maintain** good sender reputation
- **Monitor** bounce and complaint rates

### 2. Template Design
- **Use** responsive design for mobile
- **Keep** subject lines under 50 characters
- **Include** clear call-to-action buttons
- **Test** across different email clients
- **Use** alt text for images

### 3. Performance
- **Implement** email queuing for bulk sends
- **Use** database transactions for logging
- **Implement** rate limiting
- **Monitor** email delivery performance
- **Clean** old email logs regularly

### 4. Security
- **Validate** all email addresses
- **Sanitize** template variables
- **Implement** rate limiting
- **Log** all email activities
- **Monitor** for abuse

---

## 📈 Monitoring & Maintenance

### Daily Tasks
- [ ] Check email delivery success rate
- [ ] Monitor email logs for errors
- [ ] Check queue worker status
- [ ] Review bounce reports

### Weekly Tasks
- [ ] Analyze email performance metrics
- [ ] Review and update email templates
- [ ] Check sender reputation
- [ ] Clean up old email logs

### Monthly Tasks
- [ ] Review email provider performance
- [ ] Update email templates
- [ ] Analyze user engagement
- [ ] Review and update email policies

---

## 🆘 Support & Resources

### Email Provider Support
- **Gmail**: [Gmail Help](https://support.google.com/mail/)
- **SendGrid**: [SendGrid Support](https://support.sendgrid.com/)
- **Mailgun**: [Mailgun Support](https://help.mailgun.com/)
- **Amazon SES**: [AWS SES Documentation](https://docs.aws.amazon.com/ses/)

### Laravel Support
- **Mail Documentation**: [https://laravel.com/docs/mail](https://laravel.com/docs/mail)
- **Queue Documentation**: [https://laravel.com/docs/queues](https://laravel.com/docs/queues)

### Emergency Contacts
- **Technical Issues**: Your IT team
- **Email Provider Issues**: Provider support
- **System Admin**: Your system administrator

---

## ✅ Success Checklist

Before going live with email system:

- [ ] Email provider account created and verified
- [ ] API keys or credentials configured
- [ ] Sender email authenticated
- [ ] Database migration completed
- [ ] Admin panel configured
- [ ] Email templates created and tested
- [ ] Multi-language support verified
- [ ] Queue system configured (if using queues)
- [ ] Email logging enabled
- [ ] Test emails sent successfully
- [ ] Spam prevention measures implemented
- [ ] Monitoring and alerting configured

---

## 🎉 Congratulations!

You've successfully configured the email system for Net On You! 

**Next Steps**:
1. **Test** thoroughly with all email templates
2. **Monitor** email delivery performance
3. **Train** your team on email management
4. **Go live** with confidence!

**Remember**: Email deliverability is crucial for user engagement. Monitor your sender reputation and adjust settings as needed.

---

**Document Version**: 1.0  
**Last Updated**: January 2025  
**For Support**: Contact your system administrator or email provider support

