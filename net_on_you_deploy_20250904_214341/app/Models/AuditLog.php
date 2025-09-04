<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'admin_user_id',
        'action',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the admin who performed this action
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Log an admin action
     */
    public static function log(int $adminId, string $action, array $details = [], string $ipAddress = null): self
    {
        return static::create([
            'admin_user_id' => $adminId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Get logs for a specific admin
     */
    public static function forAdmin(int $adminId, int $limit = 50)
    {
        return static::where('admin_user_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get logs for a specific action
     */
    public static function forAction(string $action, int $limit = 50)
    {
        return static::where('action', $action)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
