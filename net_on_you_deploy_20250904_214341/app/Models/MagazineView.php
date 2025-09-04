<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagazineView extends Model
{
    use HasFactory;

    protected $fillable = [
        'magazine_id',
        'user_id',
        'action',
        'ip_address',
        'device',
        'user_agent',
    ];

    /**
     * Get the magazine that was viewed
     */
    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for views by action type
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for views by device type
     */
    public function scopeByDevice($query, string $device)
    {
        return $query->where('device', $device);
    }

    /**
     * Scope for views in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get total views for a magazine
     */
    public static function getTotalViews(int $magazineId): int
    {
        return self::where('magazine_id', $magazineId)->count();
    }

    /**
     * Get total downloads for a magazine
     */
    public static function getTotalDownloads(int $magazineId): int
    {
        return self::where('magazine_id', $magazineId)
            ->where('action', 'downloaded')
            ->count();
    }

    /**
     * Get popular magazines by views
     */
    public static function getPopularMagazines(int $limit = 10): array
    {
        return self::selectRaw('magazine_id, COUNT(*) as view_count')
            ->groupBy('magazine_id')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
