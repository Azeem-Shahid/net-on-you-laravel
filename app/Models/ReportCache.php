<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCache extends Model
{
    use HasFactory;

    protected $table = 'report_cache';

    protected $fillable = [
        'report_name',
        'filters',
        'data_snapshot',
        'generated_at',
        'created_by_admin_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'data_snapshot' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the admin who created this report
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    /**
     * Check if report is still valid (within cache duration)
     */
    public function isValid(int $cacheDurationMinutes = 60): bool
    {
        return $this->generated_at->addMinutes($cacheDurationMinutes)->isFuture();
    }

    /**
     * Clear expired reports
     */
    public static function clearExpired(int $cacheDurationMinutes = 60): int
    {
        $cutoff = now()->subMinutes($cacheDurationMinutes);
        return static::where('generated_at', '<', $cutoff)->delete();
    }
}
