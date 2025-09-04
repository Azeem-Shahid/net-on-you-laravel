<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensitiveChangesLog extends Model
{
    use HasFactory;

    protected $table = 'sensitive_changes_log';

    protected $fillable = [
        'admin_user_id',
        'change_type',
        'target',
        'old_value',
        'new_value',
        'ip_address',
    ];

    /**
     * Get admin who made the change
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_user_id');
    }

    /**
     * Log a sensitive change
     */
    public static function log(
        int $adminId,
        string $changeType,
        string $target,
        $oldValue = null,
        $newValue = null,
        string $ipAddress = null
    ): void {
        static::create([
            'admin_user_id' => $adminId,
            'change_type' => $changeType,
            'target' => $target,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Get changes by type
     */
    public static function getByType(string $changeType)
    {
        return static::where('change_type', $changeType)
            ->with('admin')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get changes for a specific target
     */
    public static function getByTarget(string $target)
    {
        return static::where('target', $target)
            ->with('admin')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get recent changes
     */
    public static function getRecent(int $limit = 50)
    {
        return static::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

