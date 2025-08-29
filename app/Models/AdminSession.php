<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'is_revoked',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * Get admin user for this session
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return !$this->is_revoked;
    }

    /**
     * Revoke this session
     */
    public function revoke(): void
    {
        $this->update(['is_revoked' => true]);
    }

    /**
     * Update last activity
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Get active sessions for a user
     */
    public static function getActiveSessions(int $userId)
    {
        return static::where('user_id', $userId)
            ->where('is_revoked', false)
            ->orderBy('last_activity_at', 'desc');
    }

    /**
     * Revoke all sessions for a user
     */
    public static function revokeAllForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('is_revoked', false)
            ->update(['is_revoked' => true]);
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupExpired(int $maxAgeHours = 24): int
    {
        $cutoff = now()->subHours($maxAgeHours);
        return static::where('last_activity_at', '<', $cutoff)->delete();
    }
}

