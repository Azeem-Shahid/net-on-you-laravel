# Module 6: Email & Notification Management

## Overview

This module provides a comprehensive email and notification management system for the Net On You platform. It includes admin-manageable email templates, multi-language support, delivery tracking, and campaign management capabilities.

## Features

### Core Functionality
- **Admin-Manageable Templates**: Create, edit, and delete email templates with variable support
- **Multi-Language Support**: Templates available in multiple languages (English, Urdu, French, etc.)
- **Variable System**: Dynamic content replacement using placeholders like `{name}`, `{plan}`, `{expiry}`
- **Delivery Tracking**: Comprehensive logging of all email activities
- **Campaign Management**: Bulk email campaigns with recipient targeting
- **Opt-out Handling**: Respect user marketing preferences

### Email Templates
- Welcome emails for new users
- Password reset notifications
- Payment confirmations
- Commission payout notifications
- Newsletter and announcements

## Database Structure

### Tables Created

#### `email_templates`
- Template metadata (name, language, subject, body)
- Variable definitions as JSON
- Admin audit trail (created_by, updated_by)

#### `email_logs`
- Complete email delivery history
- Status tracking (sent, failed, queued)
- Error logging and retry capabilities

#### `notification_settings`
- User preferences for marketing emails
- Language preferences
- Opt-in/opt-out management

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Default Templates
```bash
php artisan db:seed --class=EmailTemplateSeeder
```

### 3. Configure Mail Settings
Update your `.env` file with your mail provider settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@netonyou.com
MAIL_FROM_NAME="Net On You"
```

## Admin Panel Access

### Routes
- **Email Templates**: `/admin/email-templates`
- **Email Logs**: `/admin/email-logs`
- **Campaigns**: `/admin/campaigns`

### Permissions
- Requires admin authentication
- Access controlled by `auth:admin` middleware

## Usage

### Creating Email Templates

1. Navigate to `/admin/email-templates/create`
2. Fill in template details:
   - **Name**: Unique identifier (e.g., `welcome_email`)
   - **Language**: Template language (en, ur, fr, etc.)
   - **Subject**: Email subject line with variables
   - **Body**: Email content with HTML support
   - **Variables**: Select from predefined variables or add custom ones

3. Save the template

### Sending Test Emails

1. From the template list, click "Send Test"
2. Enter a test email address
3. Optionally provide test data for variables
4. Send the test email

### Creating Campaigns

1. Navigate to `/admin/campaigns/create`
2. Select a template and customize the subject
3. Choose recipient type:
   - **All Users**: Send to everyone
   - **Marketing Opt-in Only**: Respect user preferences
   - **Custom Selection**: Target specific users
4. Preview the campaign
5. Send the campaign

### Managing Email Logs

1. View all email activity at `/admin/email-logs`
2. Filter by status, template, user, or date range
3. Retry failed emails
4. Export logs to CSV
5. Clear old logs

## Testing Instructions

### Functional Tests

#### 1. Template Management
```bash
# Test template creation
php artisan test --filter=test_can_create_email_template

# Test template editing
php artisan test --filter=test_can_edit_email_template

# Test template deletion
php artisan test --filter=test_can_delete_email_template
```

#### 2. Email Sending
```bash
# Test welcome email
php artisan test --filter=test_welcome_email_sent_on_registration

# Test password reset email
php artisan test --filter=test_password_reset_email_sent

# Test payment confirmation email
php artisan test --filter=test_payment_confirmation_email_sent
```

#### 3. Campaign Management
```bash
# Test campaign creation
php artisan test --filter=test_can_create_campaign

# Test bulk email sending
php artisan test --filter=test_bulk_email_campaign

# Test recipient counting
php artisan test --filter=test_recipient_count_calculation
```

### Manual Testing Checklist

#### Admin Panel Tests
- [ ] Access `/admin/email-templates` (admin only)
- [ ] Create new template with variables
- [ ] Edit existing template
- [ ] Duplicate template
- [ ] Delete template (check usage validation)
- [ ] Send test email
- [ ] View email logs with filters
- [ ] Retry failed emails
- [ ] Export email logs
- [ ] Create and send campaigns

#### Email Delivery Tests
- [ ] Welcome email sent on user registration
- [ ] Password reset email with valid link
- [ ] Payment confirmation email with transaction details
- [ ] Commission payout notification
- [ ] Marketing campaign respects opt-out preferences
- [ ] Multi-language templates work correctly

#### Security Tests
- [ ] Non-admin users cannot access admin routes
- [ ] Template variables are properly sanitized
- [ ] Rate limiting on bulk sends
- [ ] Audit trail maintained for all changes

### Responsiveness Tests

#### Mobile (360px)
- [ ] Template cards stack vertically
- [ ] Forms use full width
- [ ] Buttons are appropriately sized
- [ ] Tables are scrollable horizontally

#### Tablet (768px)
- [ ] Two-column layout where appropriate
- [ ] Forms use available space efficiently
- [ ] Cards maintain readable proportions

#### Desktop (1200px+)
- [ ] Full table/grid layouts
- [ ] Sidebar information panels
- [ ] Advanced filtering options

## Integration Points

### User Registration
```php
// In AuthController or User model
use App\Services\EmailService;

public function register(Request $request)
{
    // ... user creation logic ...
    
    // Send welcome email
    $emailService = app(EmailService::class);
    $emailService->sendWelcomeEmail($user->id);
}
```

### Password Reset
```php
// In password reset logic
$emailService->sendPasswordResetEmail($user->id, $resetToken);
```

### Payment Confirmation
```php
// In payment webhook or confirmation logic
$emailService->sendPaymentConfirmationEmail($user->id, $transaction);
```

### Commission Payout
```php
// In payout processing
$emailService->sendCommissionPayoutEmail($user->id, $payout);
```

## Configuration Options

### Email Provider Settings
The system supports multiple email providers:
- SMTP (default)
- SendGrid
- Mailgun
- Amazon SES

### Rate Limiting
```php
// In EmailService
// Configure rate limits for bulk sends
const MAX_BULK_EMAILS_PER_HOUR = 1000;
const MAX_EMAILS_PER_MINUTE = 100;
```

### Template Variables
Predefined variables available:
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
- `{support_email}` - Support email

## Troubleshooting

### Common Issues

#### 1. Emails Not Sending
- Check mail configuration in `.env`
- Verify SMTP credentials
- Check email logs for error messages
- Ensure queue worker is running (if using queues)

#### 2. Template Variables Not Replacing
- Verify variables are defined in template
- Check variable names match exactly
- Ensure data is passed correctly to EmailService

#### 3. Admin Access Issues
- Verify admin authentication
- Check middleware configuration
- Ensure admin role permissions

#### 4. Performance Issues
- Monitor email log table size
- Implement email queue for bulk sends
- Consider archiving old logs

### Debug Mode
Enable debug logging in `.env`:
```env
LOG_LEVEL=debug
MAIL_LOG_CHANNEL=mail
```

## Performance Considerations

### Database Optimization
- Index on `template_name`, `user_id`, `status`
- Regular cleanup of old email logs
- Partition large log tables by date

### Email Delivery
- Use job queues for bulk sends
- Implement retry mechanisms
- Monitor delivery rates and bounce handling

### Caching
- Cache frequently used templates
- Cache user notification preferences
- Implement template compilation caching

## Future Enhancements

### Planned Features
- **Advanced Segmentation**: User behavior-based targeting
- **A/B Testing**: Template and subject line testing
- **Scheduling**: Time-based campaign delivery
- **Analytics**: Detailed engagement metrics
- **Webhook Integration**: External service notifications
- **Mobile Push Notifications**: App notification support

### API Endpoints
- RESTful API for template management
- Webhook endpoints for delivery status
- Integration with marketing automation tools

## Support

For technical support or questions about this module:
- Check the Laravel documentation
- Review the email service logs
- Contact the development team

---

**Module Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: Laravel 10+, PHP 8.1+
