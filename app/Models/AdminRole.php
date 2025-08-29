<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'permissions',
        'created_by_admin_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get admin who created this role
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if role has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all available permissions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'dashboard.view' => 'View Dashboard',
            'users.manage' => 'Manage Users',
            'users.view' => 'View Users',
            'magazines.manage' => 'Manage Magazines',
            'magazines.view' => 'View Magazines',
            'transactions.manage' => 'Manage Transactions',
            'transactions.view' => 'View Transactions',
            'subscriptions.manage' => 'Manage Subscriptions',
            'subscriptions.view' => 'View Subscriptions',
            'commissions.manage' => 'Manage Commissions',
            'commissions.view' => 'View Commissions',
            'payouts.manage' => 'Manage Payouts',
            'payouts.view' => 'View Payouts',
            'referrals.manage' => 'Manage Referrals',
            'referrals.view' => 'View Referrals',
            'analytics.view' => 'View Analytics',
            'email_templates.manage' => 'Manage Email Templates',
            'email_logs.view' => 'View Email Logs',
            'campaigns.manage' => 'Manage Campaigns',
            'languages.manage' => 'Manage Languages',
            'translations.manage' => 'Manage Translations',
            'settings.manage' => 'Manage Settings',
            'security.manage' => 'Manage Security',
            'roles.manage' => 'Manage Roles',
            'api_keys.manage' => 'Manage API Keys',
            'sessions.manage' => 'Manage Sessions',
        ];
    }
}

