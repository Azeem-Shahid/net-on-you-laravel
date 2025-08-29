# Module 4: Payments & Subscription - Implementation Guide

## Overview

This module implements a complete payment and subscription system for the Laravel application, supporting both cryptocurrency payments (via NowPayments) and manual payment methods. The system is designed with mobile-first responsive design and comprehensive testing.

## Features Implemented

### ✅ Core Payment Features
- **Payment Methods**: Crypto (USDT/BTC) via NowPayments API + Manual payment uploads
- **Subscription Plans**: Monthly ($29.99) and Annual ($299.99) with 17% savings
- **Transaction Tracking**: Complete audit trail with status updates
- **Webhook Processing**: Automatic payment confirmation and subscription activation
- **Manual Payment Review**: Admin approval system for manual payments

### ✅ Subscription Management
- **Automatic Activation**: Subscriptions activate upon successful payment
- **Renewal Handling**: Extends existing subscriptions or creates new ones
- **Expiry Management**: Tracks subscription status and expiry dates
- **Admin Controls**: Full CRUD operations for subscription management

### ✅ Security Features
- **User Isolation**: Users can only access their own transactions
- **Webhook Validation**: Secure payment confirmation via webhooks
- **File Upload Security**: Secure payment proof uploads with validation
- **Admin Activity Logging**: Complete audit trail for all admin actions

### ✅ Mobile-First Design
- **Responsive Layout**: Optimized for 360px+ mobile devices
- **Touch-Friendly**: Large buttons and form elements
- **Progressive Enhancement**: Enhanced experience on larger screens
- **Accessibility**: Proper ARIA labels and keyboard navigation

## Database Structure

### New Tables Created

#### `subscriptions`
```sql
- id (bigint, PK, auto)
- user_id (bigint, index)
- plan_name (varchar 100)
- start_date (date)
- end_date (date)
- status (enum: active, expired, cancelled)
- last_renewed_at (timestamp, nullable)
- created_at, updated_at
```

#### `payment_notifications`
```sql
- id (bigint, PK, auto)
- payload (json)
- received_at (timestamp)
- processed (tinyint 0/1)
- created_at, updated_at
```

### Updated Tables

#### `transactions` (enhanced)
```sql
- Added: notes (text, nullable)
- Updated: status enum includes 'refunded'
- Updated: transaction_hash is now nullable
```

## API Integration

### NowPayments Integration
- **API Endpoint**: `https://api.nowpayments.io/v1/payment`
- **Supported Currencies**: USDT, BTC
- **Webhook URL**: `/payment/webhook`
- **Configuration**: Set `nowpayments_api_key` in settings table

### Manual Payment Flow
1. User selects manual payment method
2. Uploads payment proof (JPG, PNG, PDF up to 2MB)
3. Admin reviews and approves
4. Subscription activated manually

## Routes

### User Routes (Protected)
```php
GET  /payment/checkout          # Payment plan selection
POST /payment/initiate          # Initiate payment
GET  /payment/status/{id}       # View payment status
GET  /payment/manual/{id}       # Manual payment upload
POST /payment/upload-proof/{id} # Upload payment proof
GET  /payment/history           # Payment history
```

### Admin Routes (Protected)
```php
GET    /admin/subscriptions              # List subscriptions
GET    /admin/subscriptions/create       # Create subscription form
POST   /admin/subscriptions              # Store subscription
GET    /admin/subscriptions/{id}         # View subscription
GET    /admin/subscriptions/{id}/edit    # Edit subscription form
PUT    /admin/subscriptions/{id}         # Update subscription
POST   /admin/subscriptions/{id}/extend  # Extend subscription
POST   /admin/subscriptions/{id}/cancel  # Cancel subscription
GET    /admin/subscriptions/export       # Export to CSV
```

### Public Routes
```php
POST /payment/webhook           # Payment webhook (NowPayments)
```

## Controllers

### PaymentController
- Handles user payment initiation and management
- Integrates with NowPayments API
- Processes payment webhooks
- Manages manual payment uploads

### Admin\SubscriptionController
- Manages user subscriptions from admin panel
- Provides CRUD operations
- Handles subscription extensions and cancellations
- Exports subscription data

## Models

### Subscription
- Manages subscription lifecycle
- Provides status checking methods
- Handles renewal and cancellation logic

### PaymentNotification
- Logs webhook notifications
- Tracks processing status
- Provides payload access methods

### Transaction (Enhanced)
- Added subscription relationship
- Enhanced status management
- Improved metadata handling

## Views

### User Views
- **checkout.blade.php**: Plan selection and payment method
- **status.blade.php**: Payment status and instructions
- **manual.blade.php**: Payment proof upload
- **history.blade.php**: Transaction history with responsive table

### Admin Views
- **subscriptions/index.blade.php**: Subscription management
- **subscriptions/show.blade.php**: Subscription details
- **subscriptions/create.blade.php**: Create subscription
- **subscriptions/edit.blade.php**: Edit subscription

## Testing

### Test Coverage
- **Payment Flow**: Complete payment initiation and processing
- **Security**: User isolation and access control
- **Webhooks**: Payment confirmation and subscription activation
- **File Uploads**: Payment proof uploads and validation
- **Subscription Management**: CRUD operations and status changes

### Running Tests
```bash
# Run all payment tests
php artisan test tests/Feature/PaymentTest.php

# Run with coverage
php artisan test --coverage --filter=PaymentTest
```

## Configuration

### Environment Variables
```env
# NowPayments API (optional - can be set via admin panel)
NOWPAYMENTS_API_KEY=your_api_key_here

# File upload settings
FILESYSTEM_DISK=public
MAX_FILE_SIZE=2048
```

### Settings Table
```sql
INSERT INTO settings (key, value, type) VALUES 
('nowpayments_api_key', 'your_api_key', 'string');
```

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Create Storage Link
```bash
php artisan storage:link
```

### 3. Set API Key (Optional)
```bash
php artisan tinker
Setting::create(['key' => 'nowpayments_api_key', 'value' => 'your_key', 'type' => 'string']);
```

### 4. Test the System
```bash
php artisan test tests/Feature/PaymentTest.php
```

## Usage Examples

### Creating a Payment
```php
// User initiates payment
$response = $this->post('/payment/initiate', [
    'plan' => 'monthly',
    'payment_method' => 'crypto'
]);
```

### Checking Subscription Status
```php
$user = auth()->user();
if ($user->hasActiveSubscription()) {
    echo "Active until: " . $user->activeSubscription->end_date;
}
```

### Admin Subscription Management
```php
// Extend subscription
$subscription->extend(30); // Add 30 days

// Cancel subscription
$subscription->cancel();
```

## Security Considerations

### Payment Security
- All payment routes require authentication and email verification
- Users can only access their own transactions
- Webhook signatures should be validated in production
- File uploads are validated and stored securely

### Admin Security
- Admin routes require admin authentication
- All admin actions are logged for audit purposes
- Subscription modifications are tracked with before/after data

## Mobile Responsiveness

### Design Principles
- **Mobile-First**: Designed for 360px+ mobile devices
- **Touch-Friendly**: Minimum 44px touch targets
- **Progressive Enhancement**: Enhanced experience on larger screens
- **Accessibility**: Proper contrast ratios and keyboard navigation

### Breakpoints
- **Mobile**: 360px - 767px (single column, large buttons)
- **Tablet**: 768px - 1023px (two column layout)
- **Desktop**: 1024px+ (full table layout with advanced features)

## Troubleshooting

### Common Issues

#### Payment Not Processing
1. Check NowPayments API key configuration
2. Verify webhook URL is accessible
3. Check server logs for webhook errors
4. Ensure transaction exists in database

#### Subscription Not Activating
1. Verify payment status is 'completed'
2. Check transaction metadata for plan information
3. Ensure user doesn't have existing active subscription
4. Review webhook processing logs

#### File Upload Issues
1. Check storage permissions
2. Verify file size limits
3. Ensure correct file types
4. Check storage disk configuration

### Debug Mode
Enable debug logging in `config/logging.php`:
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'daily'],
        'ignore_exceptions' => false,
    ],
],
```

## Performance Optimization

### Database Indexes
- `user_id` on transactions and subscriptions
- `status` on transactions and subscriptions
- `end_date` on subscriptions for expiry queries

### Caching
- Consider caching subscription status for active users
- Cache plan pricing information
- Use Redis for webhook processing queue

### File Storage
- Use CDN for payment proof files
- Implement file cleanup for old proofs
- Consider image optimization for screenshots

## Future Enhancements

### Planned Features
- **Recurring Payments**: Automatic subscription renewals
- **Multiple Payment Gateways**: Stripe, PayPal integration
- **Subscription Tiers**: Different magazine access levels
- **Payment Analytics**: Advanced reporting and insights
- **Email Notifications**: Payment confirmations and reminders

### Scalability Improvements
- **Queue Processing**: Background webhook processing
- **Microservices**: Separate payment service
- **API Rate Limiting**: Protect against abuse
- **Multi-currency**: Support for different currencies

## Support & Maintenance

### Regular Tasks
- Monitor webhook processing logs
- Review failed payment attempts
- Clean up old payment notifications
- Update API keys and configurations

### Monitoring
- Set up alerts for failed webhooks
- Monitor subscription expiry rates
- Track payment success rates
- Monitor file storage usage

---

## Quick Start Checklist

- [ ] Run database migrations
- [ ] Create storage link
- [ ] Configure NowPayments API key
- [ ] Test payment flow
- [ ] Verify webhook processing
- [ ] Test admin subscription management
- [ ] Run comprehensive tests
- [ ] Configure production environment

For additional support or questions, refer to the test files or contact the development team.
