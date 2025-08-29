# Email Templates Admin Guide

## Overview
The Email Templates system allows super admins to create, manage, and customize email templates for various purposes like welcome emails, password resets, notifications, and more. Templates support multiple languages and dynamic variables for personalization.

## Accessing Email Templates
- **URL**: `http://localhost:8000/admin/email-templates`
- **Navigation**: Admin Dashboard → Email Templates
- **Required Role**: Super Admin

## Main Features

### 1. Template Management
- **Create**: Build new email templates from scratch
- **Edit**: Modify existing templates
- **Duplicate**: Copy templates for variations
- **Delete**: Remove unused templates (soft delete)
- **View**: Preview template details and usage statistics

### 2. Multi-Language Support
Supported languages include:
- English (en)
- Urdu (ur)
- French (fr)
- Spanish (es)
- Arabic (ar)
- Hindi (hi)
- Bengali (bn)
- Portuguese (pt)
- Russian (ru)
- Chinese (zh)

### 3. Dynamic Variables
Templates support these built-in variables:
- `{name}` - User's full name
- `{email}` - User's email address
- `{plan}` - Subscription plan name
- `{expiry}` - Subscription expiry date
- `{amount}` - Transaction amount
- `{transaction_id}` - Payment transaction ID
- `{reset_link}` - Password reset link
- `{payout_id}` - Payout reference ID
- `{date}` - Current date
- `{company_name}` - Company name
- `{support_email}` - Support email address

## Creating Email Templates

### Step 1: Access Create Form
1. Go to Email Templates page
2. Click "Create Template" button
3. Fill in the required information

### Step 2: Basic Information
- **Template Name**: Unique identifier (e.g., `welcome_email`, `password_reset`)
- **Language**: Select target language
- **Subject**: Email subject line (keep under 50 characters)

### Step 3: Email Content
- **Body**: Write email content using HTML formatting
- **Variables**: Select which variables this template will use
- **HTML Support**: Use tags like `<strong>`, `<em>`, `<br>`, `<p>`

### Step 4: Variables Selection
- Check the variables you want to use
- Variables appear as `{variable_name}` in the template
- Users can customize these values when sending emails

### Example Template
```
Subject: Welcome {name} to Net On You!

Hello {name},

Welcome to Net On You! Your account has been successfully created.

Account Details:
- Email: {email}
- Plan: {plan}
- Expires: {expiry}

If you have any questions, contact us at {support_email}.

Best regards,
The Net On You Team
```

## Managing Templates

### Viewing Templates
- **List View**: See all templates with basic info
- **Detail View**: Full template content and statistics
- **Filtering**: Search by name or filter by language

### Editing Templates
1. Click "Edit" button on any template
2. Modify content, subject, or variables
3. Save changes
4. Template updates are applied immediately

### Duplicating Templates
1. Click "Duplicate" button
2. Creates a copy with "(Copy)" suffix
3. Edit the duplicate as needed
4. Useful for creating language variations

### Testing Templates
1. Click "Test Template" button
2. Enter test email address
3. Provide sample variable values
4. Send test email to verify formatting

## Best Practices

### Template Design
- **Subject Lines**: Keep under 50 characters for mobile compatibility
- **Content Length**: Aim for concise, scannable content
- **Call-to-Action**: Include clear next steps for users
- **Branding**: Maintain consistent company voice and style

### Variable Usage
- **Personalization**: Use {name} and {email} for engagement
- **Context**: Include relevant information like {plan} or {expiry}
- **Action Items**: Use {reset_link} or {support_email} for user actions
- **Validation**: Test templates with various variable combinations

### Language Considerations
- **Localization**: Adapt content for cultural preferences
- **Character Limits**: Some languages require more space
- **Right-to-Left**: Support for Arabic and similar languages
- **Formal vs Informal**: Adjust tone based on language conventions

## Template Examples

### Welcome Email Template
```
Subject: Welcome {name}! Your Net On You Account is Ready

Hello {name},

Welcome to Net On You! We're excited to have you on board.

Your subscription details:
- Plan: {plan}
- Start Date: {date}
- Expiry: {expiry}

Get started by exploring our features and let us know if you need help.

Best regards,
The Net On You Team
```

### Password Reset Template
```
Subject: Password Reset Request - Net On You

Hello {name},

You requested a password reset for your Net On You account.

Click the link below to reset your password:
{reset_link}

This link will expire in 24 hours for security.

If you didn't request this reset, please ignore this email.

Best regards,
The Net On You Team
```

### Subscription Expiry Reminder
```
Subject: Your {plan} Plan Expires Soon - {expiry}

Hello {name},

Your {plan} subscription will expire on {expiry}.

Renew now to continue enjoying:
- Unlimited access to premium content
- Priority customer support
- Exclusive member benefits

Click here to renew: {renewal_link}

Best regards,
The Net On You Team
```

## Troubleshooting

### Common Issues

#### Template Not Sending
- Check if variables are properly formatted
- Verify email content doesn't exceed limits
- Ensure all required fields are filled

#### Variables Not Replacing
- Confirm variable names match exactly (case-sensitive)
- Check for extra spaces in variable names
- Verify variables are selected in template settings

#### Formatting Issues
- Use proper HTML tags
- Test with different email clients
- Avoid complex CSS styling

### Support
- **Technical Issues**: Check Laravel logs for errors
- **Template Questions**: Review this guide
- **System Problems**: Contact development team

## Advanced Features

### Email Logs
- Track all emails sent using each template
- Monitor success/failure rates
- Analyze user engagement patterns

### Bulk Operations
- Send emails to multiple users
- Schedule email campaigns
- Track delivery status

### Template Analytics
- View usage statistics
- Monitor performance metrics
- Identify popular templates

## Security Considerations

### Data Protection
- Never include sensitive information in templates
- Use variables for dynamic content
- Validate all user inputs

### Access Control
- Only super admins can manage templates
- Audit trail for template changes
- Secure template storage

### Compliance
- Follow email marketing best practices
- Include unsubscribe options
- Respect user privacy preferences

## Conclusion

The Email Templates system provides powerful tools for creating professional, personalized communications with your users. By following this guide and best practices, you can create effective email templates that enhance user experience and drive engagement.

Remember to:
- Test templates before sending to users
- Keep content concise and actionable
- Use variables for personalization
- Maintain consistent branding
- Monitor template performance

For additional support or questions, refer to the system documentation or contact the development team.

