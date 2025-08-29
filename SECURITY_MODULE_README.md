# Security Module - Module 10

## Overview

The Security Module provides comprehensive security controls and settings management for the NetOnYou admin panel. It includes centralized system settings, security policies, role-based access control, API key management, session management, and comprehensive audit logging.

## Features

### 1. System Settings Management
- **Global Application Settings**: Site title, currency, language, timezone
- **Payment Gateway Configuration**: Stripe, PayPal, manual payment settings
- **Email Configuration**: From address, sender name, SMTP settings
- **Secure Storage**: Sensitive settings are stored with audit trails
- **Cache Management**: Built-in cache clearing for performance

### 2. Security Policies
- **Password Policies**: Minimum length, complexity requirements
- **Session Management**: Timeout settings, max concurrent sessions
- **Authentication Controls**: 2FA requirements, login attempt limits
- **Maintenance Mode**: Site-wide access control with custom messages
- **Real-time Enforcement**: Policies applied immediately across the system

### 3. Role-Based Access Control (RBAC)
- **Granular Permissions**: 25+ permission types across all modules
- **Role Management**: Create, edit, and delete admin roles
- **Permission Matrix**: Visual checkbox interface for easy management
- **Audit Trail**: All role changes logged with reasons
- **Hierarchical Access**: Super admin, editor, accountant roles

### 4. API Key Management
- **Secure Generation**: Cryptographically secure API key generation
- **Scope Control**: Granular permission scopes for each key
- **Usage Tracking**: Last used timestamps and metadata
- **Key Rotation**: Regenerate keys without downtime
- **Masked Display**: Keys shown only once, then masked for security

### 5. Session Management
- **Active Session Monitoring**: View all active admin sessions
- **Selective Revocation**: Revoke individual or all sessions for a user
- **Maintenance Mode**: Force logout all users during maintenance
- **Session Cleanup**: Automatic cleanup of expired sessions
- **Real-time Control**: Immediate session termination

### 6. Comprehensive Logging
- **Sensitive Changes Log**: Dedicated table for security-relevant changes
- **Admin Activity Log**: Integration with existing audit system
- **Change Tracking**: Old vs. new values for all modifications
- **IP Address Logging**: Track origin of all changes
- **Reason Requirements**: Mandatory reason for all security changes

## Database Schema

### New Tables Created

#### `security_policies`
```sql
CREATE TABLE security_policies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    policy_name VARCHAR(100) NOT NULL,
    policy_value VARCHAR(191) NOT NULL,
    description VARCHAR(255),
    updated_by_admin_id BIGINT INDEX,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `admin_roles`
```sql
CREATE TABLE admin_roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(100) NOT NULL,
    permissions JSON NOT NULL,
    created_by_admin_id BIGINT INDEX,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `api_keys`
```sql
CREATE TABLE api_keys (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    key VARCHAR(191) NOT NULL,
    scopes JSON NOT NULL,
    is_active TINYINT DEFAULT 1,
    created_by_admin_id BIGINT INDEX,
    last_used_at TIMESTAMP NULL,
    metadata JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `admin_sessions`
```sql
CREATE TABLE admin_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT INDEX,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_revoked TINYINT DEFAULT 0
);
```

#### `sensitive_changes_log`
```sql
CREATE TABLE sensitive_changes_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    admin_user_id BIGINT INDEX,
    change_type VARCHAR(191) NOT NULL,
    target VARCHAR(191) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Updated Tables

#### `settings` (Enhanced)
```sql
ALTER TABLE settings ADD COLUMN type VARCHAR(50) DEFAULT 'string';
ALTER TABLE settings ADD COLUMN updated_by_admin_id BIGINT INDEX;
```

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Run Seeders
```bash
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=SecurityPolicySeeder
```

### 3. Verify Routes
The following routes are automatically registered:
- `/admin/settings` - System settings management
- `/admin/security` - Security policies
- `/admin/roles` - Role and permission management
- `/admin/api-keys` - API key management
- `/admin/sessions` - Session management

## Usage Examples

### Managing System Settings
```php
// Get a setting value
$siteTitle = Setting::getValue('site_title', 'Default Title');

// Update a setting with audit trail
Setting::setValue('site_title', 'New Title', 'string', 'Updated branding', $adminId);

// Get multiple settings
$settings = Setting::getMultiple(['site_title', 'default_currency']);
```

### Working with Security Policies
```php
// Get policy value
$minLength = SecurityPolicy::getPolicyValue('password_min_length', '8');

// Update policy
SecurityPolicy::setPolicyValue('password_min_length', '10', 'Increased security', $adminId);

// Check if maintenance mode is enabled
$maintenanceMode = SecurityPolicy::getPolicyValue('maintenance_mode', 'false') === 'true';
```

### Role-Based Access Control
```php
// Check admin permissions
if ($admin->hasPermission('settings.manage')) {
    // Allow access to settings
}

// Check multiple permissions
if ($admin->hasAnyPermission(['users.manage', 'users.view'])) {
    // Allow access to user management
}
```

### API Key Management
```php
// Create new API key
$apiKey = ApiKey::create([
    'name' => 'Reporting Service',
    'scopes' => ['analytics.read', 'reports.generate'],
    'created_by_admin_id' => $adminId
]);

// Check API key permissions
if ($apiKey->hasScope('analytics.read')) {
    // Allow analytics access
}
```

### Session Management
```php
// Revoke all sessions for a user
AdminSession::revokeAllForUser($adminId);

// Clean up expired sessions
$deletedCount = AdminSession::cleanupExpired(24); // 24 hours

// Get active sessions
$activeSessions = AdminSession::getActiveSessions($adminId);
```

## Security Features

### 1. Input Validation
- All inputs validated with Laravel's validation system
- XSS protection through proper output escaping
- SQL injection prevention through Eloquent ORM

### 2. CSRF Protection
- All forms include CSRF tokens
- AJAX requests include CSRF headers
- Automatic token validation

### 3. Rate Limiting
- Sensitive endpoints rate-limited
- Configurable limits per endpoint
- IP-based rate limiting

### 4. Audit Logging
- All security changes logged
- IP address tracking
- Timestamp and user attribution
- Immutable audit trail

### 5. Permission Enforcement
- Server-side permission checks
- Route-level access control
- UI-level permission hiding
- Graceful degradation for unauthorized access

## Testing

### Run Security Tests
```bash
php artisan test --filter=SecurityModuleTest
```

### Test Coverage
The security module includes comprehensive tests covering:
- Permission-based access control
- Setting and policy updates
- Reason requirement validation
- Role-based permission inheritance
- API endpoint security

## Configuration

### Environment Variables
```env
# Security Settings
SECURITY_SESSION_TIMEOUT=120
SECURITY_MAX_LOGIN_ATTEMPTS=5
SECURITY_LOCKOUT_DURATION=30
SECURITY_REQUIRE_2FA=false

# API Key Settings
API_KEY_PREFIX=nk_
API_KEY_LENGTH=32
```

### Cache Configuration
```php
// Settings cache duration (seconds)
'settings_cache_ttl' => 3600,

// Security policy cache
'security_cache_ttl' => 1800,
```

## Maintenance & Monitoring

### Regular Tasks
1. **Session Cleanup**: Run daily to remove expired sessions
2. **Audit Log Review**: Weekly review of sensitive changes
3. **Permission Audit**: Monthly review of role permissions
4. **API Key Review**: Quarterly review of active API keys

### Monitoring
- Track failed login attempts
- Monitor session creation/revocation
- Alert on suspicious activity patterns
- Log analysis for security insights

## Troubleshooting

### Common Issues

#### Permission Denied Errors
- Verify admin role has correct permissions
- Check if admin account is active
- Ensure middleware is properly configured

#### Settings Not Updating
- Clear settings cache: `php artisan settings:clear`
- Check database connection
- Verify admin permissions

#### API Keys Not Working
- Verify key is active
- Check key scopes match required permissions
- Ensure key hasn't expired

### Debug Mode
Enable debug logging for security operations:
```php
// In config/logging.php
'channels' => [
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'debug',
    ],
],
```

## API Reference

### Settings Endpoints
- `GET /admin/settings` - List all settings
- `PUT /admin/settings/{key}` - Update specific setting
- `POST /admin/settings/update-multiple` - Update multiple settings
- `POST /admin/settings/clear-cache` - Clear settings cache

### Security Endpoints
- `GET /admin/security` - List security policies
- `PUT /admin/security/{policyName}` - Update specific policy
- `POST /admin/security/update-multiple` - Update multiple policies

### Role Endpoints
- `GET /admin/roles` - List all roles
- `POST /admin/roles` - Create new role
- `PUT /admin/roles/{role}` - Update role
- `DELETE /admin/roles/{role}` - Delete role

### API Key Endpoints
- `GET /admin/api-keys` - List all API keys
- `POST /admin/api-keys` - Create new API key
- `PUT /admin/api-keys/{apiKey}` - Update API key
- `DELETE /admin/api-keys/{apiKey}` - Delete API key
- `POST /admin/api-keys/{apiKey}/regenerate` - Regenerate key

### Session Endpoints
- `GET /admin/sessions` - List all sessions
- `POST /admin/sessions/{session}/revoke` - Revoke specific session
- `POST /admin/sessions/admin/{adminId}/revoke-all` - Revoke all sessions for admin
- `POST /admin/sessions/revoke-all-maintenance` - Revoke all sessions (maintenance)

## Contributing

### Development Guidelines
1. All security changes must include audit logging
2. Permission checks required for all sensitive operations
3. Input validation mandatory for all user inputs
4. Reason required for all security policy changes
5. Comprehensive test coverage for new features

### Code Standards
- Follow PSR-12 coding standards
- Include PHPDoc comments for all methods
- Use type hints and return types
- Implement proper error handling
- Follow Laravel best practices

## License

This security module is part of the NetOnYou application and follows the same licensing terms.

## Support

For technical support or security concerns:
- Create an issue in the project repository
- Contact the development team
- Review the security documentation
- Check the troubleshooting guide

---

**Note**: This security module is designed to provide enterprise-grade security controls while maintaining ease of use for administrators. Regular security audits and updates are recommended to ensure continued protection.

