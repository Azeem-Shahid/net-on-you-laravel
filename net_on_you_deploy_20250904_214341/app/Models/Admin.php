<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_ip',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if admin is editor
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if admin is accountant
     */
    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    /**
     * Check if admin is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if admin can manage users
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['super_admin', 'editor']);
    }

    /**
     * Check if admin can manage finances
     */
    public function canManageFinances(): bool
    {
        return in_array($this->role, ['super_admin', 'accountant']);
    }

    /**
     * Check if admin can manage magazines
     */
    public function canManageMagazines(): bool
    {
        return in_array($this->role, ['super_admin', 'editor']);
    }

    /**
     * Check if admin has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check role-based permissions
        switch ($permission) {
            case 'settings.manage':
            case 'security.manage':
            case 'roles.manage':
            case 'api_keys.manage':
            case 'sessions.manage':
                return $this->isSuperAdmin();
            
            case 'users.manage':
            case 'users.view':
                return in_array($this->role, ['super_admin', 'editor']);
            
            case 'magazines.manage':
            case 'magazines.view':
                return in_array($this->role, ['super_admin', 'editor']);
            
            case 'transactions.manage':
            case 'transactions.view':
                return in_array($this->role, ['super_admin', 'accountant']);
            
            case 'subscriptions.manage':
            case 'subscriptions.view':
                return in_array($this->role, ['super_admin', 'editor']);
            
            case 'commissions.manage':
            case 'commissions.view':
                return in_array($this->role, ['super_admin', 'accountant']);
            
            case 'payouts.manage':
            case 'payouts.view':
                return in_array($this->role, ['super_admin', 'accountant']);
            
            case 'referrals.manage':
            case 'referrals.view':
                return in_array($this->role, ['super_admin', 'editor']);
            
            case 'analytics.view':
                return in_array($this->role, ['super_admin', 'editor', 'accountant']);
            
            case 'email_templates.manage':
            case 'email_logs.view':
            case 'campaigns.manage':
                return in_array($this->role, ['super_admin', 'editor']);
            
            case 'languages.manage':
            case 'translations.manage':
                return in_array($this->role, ['super_admin', 'editor']);
            
            default:
                return false;
        }
    }

    /**
     * Check if admin has any of the given permissions
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
     * Check if admin has all of the given permissions
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
     * Update last login information
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_ip' => $ip,
            'last_login_at' => now(),
        ]);
    }

    /**
     * Get admin activity logs
     */
    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    /**
     * Get magazines uploaded by this admin
     */
    public function magazines()
    {
        return $this->hasMany(Magazine::class, 'uploaded_by');
    }

    /**
     * Get admin sessions
     */
    public function sessions()
    {
        return $this->hasMany(AdminSession::class, 'user_id');
    }

    /**
     * Get active sessions
     */
    public function activeSessions()
    {
        return $this->sessions()->where('is_revoked', false);
    }

    /**
     * Revoke all sessions for this admin
     */
    public function revokeAllSessions(): int
    {
        return $this->activeSessions()->update(['is_revoked' => true]);
    }
}
