# Net On You - Complete Integration Guide

## Table of Contents
1. [Email Integration Setup](#email-integration-setup)
2. [CoinPayments Integration Setup](#coinpayments-integration-setup)
3. [System Configuration](#system-configuration)
4. [Testing & Verification](#testing--verification)
5. [Troubleshooting](#troubleshooting)

---

## Email Integration Setup

### 1. SMTP Configuration

#### Required Email Settings
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Net On You"
```

#### Step-by-Step SMTP Setup:

##### Option A: Gmail SMTP
1. **Enable 2-Factor Authentication**
   - Go to Google Account settings
   - Enable 2FA on your Gmail account

2. **Generate App Password**
   - Go to Security settings
   - Generate app password for "Mail"
   - Copy the 16-character password

3. **Configure .env File**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-gmail@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-gmail@gmail.com
   MAIL_FROM_NAME="Net On You"
   ```

##### Option B: Custom SMTP Server
1. **Get SMTP Credentials**
   - Contact your email provider
   - Get SMTP server details
   - Obtain username and password

2. **Configure .env File**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mail.yourdomain.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=your-smtp-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Net On You"
   ```

##### Option C: Mailgun
1. **Create Mailgun Account**
   - Sign up at mailgun.com
   - Verify your domain
   - Get API credentials

2. **Configure .env File**
   ```env
   MAIL_MAILER=mailgun
   MAILGUN_DOMAIN=your-domain.com
   MAILGUN_SECRET=your-mailgun-api-key
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="Net On You"
   ```

### 2. Email Template Setup

#### Default Email Templates
The system includes these default templates:

1. **Welcome Email**
   - Template: `welcome_email`
   - Variables: `{{user_name}}`, `{{user_email}}`

2. **Email Verification**
   - Template: `email_verification`
   - Variables: `{{user_name}}`, `{{verification_url}}`

3. **Password Reset**
   - Template: `password_reset`
   - Variables: `{{user_name}}`, `{{reset_url}}`

4. **Payment Confirmation**
   - Template: `payment_confirmation`
   - Variables: `{{user_name}}`, `{{amount}}`, `{{transaction_id}}`

5. **Subscription Expiry**
   - Template: `subscription_expiry`
   - Variables: `{{user_name}}`, `{{expiry_date}}`

6. **Commission Notification**
   - Template: `commission_notification`
   - Variables: `{{user_name}}`, `{{commission_amount}}`

#### Creating Custom Templates
1. **Access Admin Panel**
   - Go to `/admin/email-templates`
   - Click "Create New Template"

2. **Template Configuration**
   - **Name**: Template identifier
   - **Subject**: Email subject line
   - **Content**: HTML email content
   - **Language**: Template language
   - **Category**: Template category

3. **Using Variables**
   ```html
   <h1>Welcome {{user_name}}!</h1>
   <p>Your email: {{user_email}}</p>
   <p>Your referral code: {{referral_code}}</p>
   ```

### 3. Email Testing

#### Test Email Sending
1. **Admin Panel Testing**
   - Go to `/admin/email-templates`
   - Select template
   - Click "Send Test"
   - Enter test email address

2. **Command Line Testing**
   ```bash
   php artisan mail:test
   ```

3. **Check Email Logs**
   - Go to `/admin/email-logs`
   - View sent emails
   - Check delivery status

#### Common Email Issues
1. **Authentication Failed**
   - Check username/password
   - Verify 2FA settings
   - Test SMTP connection

2. **Emails Not Sending**
   - Check SMTP settings
   - Verify port configuration
   - Test with different provider

3. **Emails Going to Spam**
   - Set up SPF records
   - Configure DKIM
   - Use reputable SMTP provider

---

## CoinPayments Integration Setup

### 1. CoinPayments Account Setup

#### Account Creation
1. **Sign Up**
   - Visit coinpayments.net
   - Create merchant account
   - Verify email address

2. **Account Verification**
   - Complete KYC process
   - Verify business details
   - Wait for approval

3. **Get API Credentials**
   - Go to Account → API Keys
   - Generate new API key
   - Copy API key and secret

### 2. CoinPayments Configuration

#### Environment Variables
```env
COINPAYMENTS_MERCHANT_ID=your_merchant_id
COINPAYMENTS_IPN_SECRET=your_ipn_secret
COINPAYMENTS_PRIVATE_KEY=your_private_key
COINPAYMENTS_PUBLIC_KEY=your_public_key
COINPAYMENTS_IPN_URL=https://yourdomain.com/payments/coinpayments/ipn
```

#### Step-by-Step Configuration:

1. **Get Merchant ID**
   - Log into CoinPayments account
   - Go to Account → Account Settings
   - Copy Merchant ID

2. **Generate API Keys**
   - Go to Account → API Keys
   - Click "Generate New Key"
   - Set permissions (IPN, payments)
   - Copy Public and Private keys

3. **Set IPN Secret**
   - Go to Account → IPN Settings
   - Set IPN Secret (random string)
   - Configure IPN URL: `https://yourdomain.com/payments/coinpayments/ipn`

4. **Configure .env File**
   ```env
   COINPAYMENTS_MERCHANT_ID=your_merchant_id
   COINPAYMENTS_IPN_SECRET=your_ipn_secret
   COINPAYMENTS_PRIVATE_KEY=your_private_key
   COINPAYMENTS_PUBLIC_KEY=your_public_key
   COINPAYMENTS_IPN_URL=https://yourdomain.com/payments/coinpayments/ipn
   ```

### 3. CoinPayments Webhook Setup

#### IPN (Instant Payment Notification) Configuration

1. **IPN URL Setup**
   - URL: `https://yourdomain.com/payments/coinpayments/ipn`
   - Method: POST
   - Content-Type: application/x-www-form-urlencoded

2. **IPN Secret Verification**
   - CoinPayments sends IPN secret with each notification
   - System verifies secret before processing
   - Ensures payment authenticity

3. **Payment Status Handling**
   ```php
   // Payment status codes
   'pending' => 'Payment pending'
   'completed' => 'Payment completed'
   'failed' => 'Payment failed'
   'expired' => 'Payment expired'
   'refunded' => 'Payment refunded'
   ```

### 4. Supported Cryptocurrencies

#### Default Supported Coins:
- **Bitcoin (BTC)**
- **Ethereum (ETH)**
- **Litecoin (LTC)**
- **Bitcoin Cash (BCH)**
- **USDT (Tether)**
- **And 2000+ other cryptocurrencies**

#### Adding/Removing Coins:
1. **Admin Panel Configuration**
   - Go to `/admin/settings`
   - Find CoinPayments settings
   - Configure accepted coins

2. **API Configuration**
   - Use CoinPayments API to get available coins
   - Filter by your preferences
   - Update system configuration

### 5. Payment Flow Integration

#### Payment Process:
1. **User Initiates Payment**
   - User selects subscription plan
   - Chooses cryptocurrency
   - Clicks "Pay with Crypto"

2. **Create Payment Request**
   ```php
   $payment = [
       'amount' => $subscription->price,
       'currency1' => 'USD',
       'currency2' => $cryptocurrency,
       'buyer_email' => $user->email,
       'item_name' => $subscription->name,
       'ipn_url' => config('coinpayments.ipn_url'),
       'success_url' => route('payment.success'),
       'cancel_url' => route('payment.cancel')
   ];
   ```

3. **Redirect to CoinPayments**
   - User redirected to CoinPayments checkout
   - Payment processed securely
   - User completes payment

4. **IPN Processing**
   - CoinPayments sends IPN notification
   - System verifies payment
   - Updates subscription status
   - Sends confirmation email

### 6. Security Configuration

#### Security Best Practices:
1. **HTTPS Required**
   - All payment URLs must use HTTPS
   - SSL certificate required
   - Secure cookie settings

2. **IPN Verification**
   - Always verify IPN secret
   - Check payment amounts
   - Validate transaction IDs

3. **Error Handling**
   - Log all payment attempts
   - Handle failed payments gracefully
   - Provide clear error messages

---

## System Configuration

### 1. Database Configuration

#### Required Tables:
1. **Transactions Table**
   ```sql
   CREATE TABLE transactions (
       id BIGINT PRIMARY KEY,
       user_id BIGINT,
       amount DECIMAL(10,2),
       currency VARCHAR(10),
       payment_method VARCHAR(50),
       status VARCHAR(20),
       transaction_id VARCHAR(255),
       created_at TIMESTAMP,
       updated_at TIMESTAMP
   );
   ```

2. **Payment Notifications Table**
   ```sql
   CREATE TABLE payment_notifications (
       id BIGINT PRIMARY KEY,
       transaction_id VARCHAR(255),
       payment_data TEXT,
       status VARCHAR(20),
       created_at TIMESTAMP
   );
   ```

### 2. Queue Configuration

#### Email Queue Setup:
```env
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids
```

#### Queue Commands:
```bash
# Start queue worker
php artisan queue:work

# Monitor queue
php artisan queue:monitor

# Clear failed jobs
php artisan queue:flush
```

### 3. Cron Job Configuration

#### Required Cron Jobs:
```bash
# Process payments every 5 minutes
*/5 * * * * cd /path/to/your/app && php artisan payments:process

# Send email notifications every minute
* * * * * cd /path/to/your/app && php artisan emails:send

# Calculate commissions daily
0 0 * * * cd /path/to/your/app && php artisan commissions:calculate

# Clean up old logs weekly
0 0 * * 0 cd /path/to/your/app && php artisan logs:cleanup
```

### 4. File Permissions

#### Required Permissions:
```bash
# Storage directory
chmod -R 755 storage/
chown -R www-data:www-data storage/

# Bootstrap cache
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data bootstrap/cache/

# Logs directory
chmod -R 755 storage/logs/
chown -R www-data:www-data storage/logs/
```

---

## Testing & Verification

### 1. Email Testing

#### Test Email Sending:
1. **Admin Panel Test**
   - Go to `/admin/email-templates`
   - Select any template
   - Click "Send Test"
   - Enter your email address

2. **Command Line Test**
   ```bash
   php artisan mail:test --email=your@email.com
   ```

3. **Check Email Logs**
   - Go to `/admin/email-logs`
   - Verify email was sent
   - Check delivery status

#### Email Configuration Test:
```bash
# Test SMTP connection
php artisan mail:test-smtp

# Test email templates
php artisan mail:test-templates

# Test email queue
php artisan queue:work --once
```

### 2. CoinPayments Testing

#### Test Payment Flow:
1. **Create Test Payment**
   - Go to payment page
   - Select test cryptocurrency
   - Complete payment process

2. **Test IPN Processing**
   - Use CoinPayments test mode
   - Send test IPN notifications
   - Verify payment processing

3. **Check Transaction Logs**
   - Go to `/admin/transactions`
   - Verify payment recorded
   - Check payment status

#### CoinPayments Test Mode:
```env
COINPAYMENTS_TEST_MODE=true
COINPAYMENTS_TEST_MERCHANT_ID=your_test_merchant_id
```

### 3. Integration Testing

#### Complete Flow Test:
1. **User Registration**
   - Register new user
   - Verify email received
   - Check user created

2. **Payment Process**
   - Select subscription
   - Complete payment
   - Verify subscription active

3. **Email Notifications**
   - Check payment confirmation
   - Verify welcome email
   - Test referral notifications

#### Automated Testing:
```bash
# Run integration tests
php artisan test --filter=PaymentTest

# Run email tests
php artisan test --filter=EmailTest

# Run CoinPayments tests
php artisan test --filter=CoinPaymentsTest
```

---

## Troubleshooting

### 1. Email Issues

#### Common Email Problems:

**Problem**: Emails not sending
**Solutions**:
1. Check SMTP settings in .env
2. Verify email credentials
3. Test SMTP connection
4. Check firewall settings
5. Verify port configuration

**Problem**: Emails going to spam
**Solutions**:
1. Set up SPF records
2. Configure DKIM
3. Use reputable SMTP provider
4. Avoid spam trigger words
5. Warm up email domain

**Problem**: Template variables not working
**Solutions**:
1. Check variable syntax
2. Verify variable names
3. Test template rendering
4. Check template cache
5. Clear application cache

#### Email Debugging:
```bash
# Check email configuration
php artisan config:show mail

# Test SMTP connection
php artisan mail:test-smtp

# View email logs
tail -f storage/logs/laravel.log

# Clear email cache
php artisan cache:clear
```

### 2. CoinPayments Issues

#### Common Payment Problems:

**Problem**: Payment not processing
**Solutions**:
1. Check API credentials
2. Verify IPN URL
3. Test IPN secret
4. Check payment amounts
5. Verify currency settings

**Problem**: IPN not received
**Solutions**:
1. Check IPN URL accessibility
2. Verify IPN secret
3. Test IPN endpoint
4. Check server logs
5. Contact CoinPayments support

**Problem**: Payment status not updating
**Solutions**:
1. Check IPN processing
2. Verify database updates
3. Check transaction logs
4. Test payment flow
5. Review error logs

#### CoinPayments Debugging:
```bash
# Check CoinPayments configuration
php artisan config:show coinpayments

# Test API connection
php artisan coinpayments:test

# View payment logs
tail -f storage/logs/coinpayments.log

# Check IPN processing
php artisan coinpayments:test-ipn
```

### 3. System Issues

#### Common System Problems:

**Problem**: Queue not processing
**Solutions**:
1. Start queue worker
2. Check queue configuration
3. Monitor queue status
4. Clear failed jobs
5. Check server resources

**Problem**: Cron jobs not running
**Solutions**:
1. Verify cron configuration
2. Check file permissions
3. Test cron commands
4. Monitor cron logs
5. Check server timezone

**Problem**: Database connection issues
**Solutions**:
1. Check database credentials
2. Verify database server
3. Test database connection
4. Check database logs
5. Verify table structure

#### System Debugging:
```bash
# Check system status
php artisan system:status

# Test database connection
php artisan db:test

# Check queue status
php artisan queue:status

# Monitor system logs
tail -f storage/logs/laravel.log
```

### 4. Performance Issues

#### Optimization Tips:
1. **Email Optimization**
   - Use queue for email sending
   - Batch email processing
   - Optimize email templates
   - Use CDN for images

2. **Payment Optimization**
   - Cache payment configurations
   - Optimize database queries
   - Use background processing
   - Monitor payment performance

3. **System Optimization**
   - Enable caching
   - Optimize database
   - Use CDN for assets
   - Monitor server resources

#### Performance Monitoring:
```bash
# Check system performance
php artisan system:performance

# Monitor queue performance
php artisan queue:monitor

# Check database performance
php artisan db:performance

# Monitor memory usage
php artisan system:memory
```

---

## Security Checklist

### Email Security:
- [ ] Use secure SMTP connection (TLS/SSL)
- [ ] Implement email authentication (SPF, DKIM)
- [ ] Use strong email passwords
- [ ] Enable 2FA for email accounts
- [ ] Monitor email logs regularly
- [ ] Implement rate limiting
- [ ] Use reputable email providers

### Payment Security:
- [ ] Use HTTPS for all payment URLs
- [ ] Verify IPN signatures
- [ ] Implement payment validation
- [ ] Use secure API keys
- [ ] Monitor payment logs
- [ ] Implement fraud detection
- [ ] Regular security audits

### System Security:
- [ ] Keep software updated
- [ ] Use strong passwords
- [ ] Implement access controls
- [ ] Monitor system logs
- [ ] Regular backups
- [ ] SSL certificate installed
- [ ] Firewall configured

---

## Support Resources

### Documentation:
- **Laravel Documentation**: https://laravel.com/docs
- **CoinPayments API**: https://www.coinpayments.net/merchant-tools-api
- **Email Providers**: Check your email provider's documentation

### Support Channels:
- **Technical Support**: support@yourdomain.com
- **CoinPayments Support**: https://support.coinpayments.net
- **Email Provider Support**: Contact your email provider

### Monitoring Tools:
- **System Monitoring**: Use Laravel Telescope or similar
- **Payment Monitoring**: CoinPayments merchant dashboard
- **Email Monitoring**: Email provider analytics

---

*This integration guide covers all major setup requirements. For specific issues, refer to the troubleshooting section or contact support.*
