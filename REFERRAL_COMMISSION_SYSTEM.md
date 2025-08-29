# Referral & Commission System

## Overview

The Referral & Commission System is a comprehensive solution for managing multi-level referral programs with automatic commission calculation and payout management. The system tracks referrals up to 6 levels deep and enforces monthly eligibility rules.

## Features

### Core Functionality
- **6-Level Referral Tracking**: Automatically builds referral uplines when users register
- **Automatic Commission Generation**: Creates commissions when payments are completed
- **Monthly Eligibility Rules**: Users must have ≥1 direct L1 sale in the month to earn commissions
- **Commission Management**: Admin tools for adjusting, voiding, and restoring commissions
- **Payout System**: Batch-based payout processing with status tracking
- **Audit Trail**: Complete history of all commission modifications

### Commission Structure
- **Level 1**: 15 USDT
- **Level 2**: 10 USDT  
- **Level 3**: 5 USDT
- **Level 4-6**: 1 USDT each

## Database Structure

### Core Tables

#### `referrals`
- Tracks referral relationships between users
- Pre-computed 6-level upline for fast reads
- Indexed on `user_id`, `referred_user_id`, and `level`

#### `commissions`
- Records all commission transactions
- Tracks eligibility and payout status
- Indexed on `earner_user_id`, `month`, `eligibility`, and `payout_status`

#### `payout_batches`
- Groups commissions by month for processing
- Tracks batch status (open, processing, closed)
- Unique constraint on period (YYYY-MM)

#### `payout_batch_items`
- Individual payout items within each batch
- Links to specific commissions via JSON array
- Tracks payout status (queued, sent, failed, paid)

#### `commission_audits`
- Complete audit trail of all changes
- Stores before/after snapshots
- Links to admin user who made the change

## Implementation Details

### Referral Upline Building
When a new user registers with a referrer:
1. System identifies the referrer
2. Builds 6-level upline by walking up the referral chain
3. Creates referral records for each level
4. Stores in `referrals` table for fast access

### Commission Generation
When a transaction is completed:
1. System identifies the source user
2. Finds all users in their upline (up to 6 levels)
3. Checks monthly eligibility for each upline user
4. Creates commission records with appropriate amounts
5. Sets eligibility based on direct L1 sales in the month

### Eligibility Rules
- **Eligible**: User has ≥1 direct L1 completed sale in the same month
- **Ineligible**: User has 0 direct L1 sales in the month
- **No Retroactive Credit**: Past ineligible months cannot be made eligible

### Payout Process
1. **Create Batch**: Admin creates payout batch for specific month
2. **Process Items**: Mark items as sent, paid, or failed
3. **Close Batch**: Lock batch when all items are processed
4. **Update Commissions**: Mark underlying commissions as paid

## Admin Tools

### Referral Management
- View all referral relationships
- Filter by level, referrer, or referred user
- Export referral data to CSV
- View individual user referral trees

### Commission Management
- List all commissions with filtering
- View commission details and audit history
- Adjust commission amounts
- Void or restore commissions
- Create payout batches

### Payout Management
- View all payout batches
- Process individual payout items
- Track payout status
- Export payout data to CSV

## API Endpoints

### Referral Management
```
GET  /admin/referrals              - List referrals
GET  /admin/referrals/{user}       - Show referral tree
GET  /admin/referrals/export       - Export referrals
```

### Commission Management
```
GET  /admin/commissions                    - List commissions
GET  /admin/commissions/{commission}       - Show commission details
POST /admin/commissions/{commission}/adjust - Adjust commission amount
POST /admin/commissions/{commission}/void   - Void commission
POST /admin/commissions/{commission}/restore - Restore voided commission
POST /admin/commissions/create-payout-batch - Create payout batch
GET  /admin/commissions/export             - Export commissions
```

### Payout Management
```
GET  /admin/payouts                    - List payout batches
GET  /admin/payouts/{batch}            - Show batch details
POST /admin/payouts/{batch}/start-processing - Start processing batch
POST /admin/payouts/items/{item}/mark-sent - Mark item as sent
POST /admin/payouts/items/{item}/mark-paid - Mark item as paid
POST /admin/payouts/items/{item}/mark-failed - Mark item as failed
POST /admin/payouts/{batch}/close      - Close batch
GET  /admin/payouts/{batch}/export     - Export batch data
```

## Console Commands

### Re-evaluate Commission Eligibility
```bash
php artisan commissions:re-evaluate-eligibility [month]
```
- Re-evaluates commission eligibility for a specific month
- Can be run manually or scheduled as a nightly job
- Useful for handling edge cases and manual corrections

## Testing

The system includes comprehensive tests covering:
- Referral upline building
- Commission generation
- Eligibility rule enforcement
- Commission amount calculations by level

Run tests with:
```bash
php artisan test tests/Feature/ReferralSystemTest.php
```

## Security Features

### Access Control
- All admin endpoints protected by `auth:admin` middleware
- Users can only view their own referral data
- Commission modifications require admin authentication

### Audit Trail
- All commission changes logged with before/after snapshots
- Admin actions tracked with timestamps and reasons
- Complete history maintained for compliance

### Data Integrity
- Database transactions ensure consistency
- Foreign key relationships maintained in code
- Unique constraints prevent duplicate records

## Mobile-First Design

### Responsive Layout
- Cards-based design for mobile devices
- Horizontal scrolling tables on small screens
- Large touch targets for mobile users

### Color Scheme
- Primary: #1d003f (Dark Purple)
- Success: #00ff00 (Green)
- Warning: #ff0000 (Red)
- Consistent color coding throughout

## Performance Considerations

### Database Indexes
- All foreign keys indexed
- Level and month columns indexed for fast queries
- Composite indexes for common query patterns

### Query Optimization
- Eager loading of relationships
- Pagination for large datasets
- Efficient referral tree building

### Caching Strategy
- Referral statistics cached at user level
- Commission totals cached by month
- Batch processing for large operations

## Future Enhancements

### Planned Features
- Real-time commission notifications
- Advanced analytics and reporting
- Automated payout processing
- Multi-currency support
- Referral program customization

### Scalability Improvements
- Queue-based commission processing
- Redis caching for high-traffic scenarios
- Database partitioning for large datasets
- API rate limiting and optimization

## Troubleshooting

### Common Issues

#### Commission Not Generated
- Check if transaction status is 'completed'
- Verify referral upline exists
- Confirm user has referrer relationship

#### Eligibility Issues
- Verify direct L1 sales in the month
- Check transaction completion dates
- Run eligibility re-evaluation command

#### Payout Problems
- Ensure batch status is correct
- Check individual item statuses
- Verify commission payout statuses

### Debug Commands
```bash
# Check referral structure for user
php artisan tinker
$user = App\Models\User::find(1);
$user->referralRecords;

# Check commission eligibility
$user->getCommissionEligibility('2025-01');

# View commission breakdown
$referralService = app(App\Services\ReferralService::class);
$referralService->getCommissionBreakdown($user->id, '2025-01');
```

## Support

For technical support or questions about the Referral & Commission System:
- Check the test files for usage examples
- Review the ReferralService class for business logic
- Examine the admin controllers for implementation details
- Run the test suite to verify functionality
