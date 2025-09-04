<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'commission_id',
        'action',
        'before_payload',
        'after_payload',
        'reason',
    ];

    protected $casts = [
        'before_payload' => 'array',
        'after_payload' => 'array',
    ];

    /**
     * Get the admin user who made the change
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_user_id');
    }

    /**
     * Get the commission that was changed
     */
    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    /**
     * Check if action is adjust
     */
    public function isAdjust(): bool
    {
        return $this->action === 'adjust';
    }

    /**
     * Check if action is void
     */
    public function isVoid(): bool
    {
        return $this->action === 'void';
    }

    /**
     * Check if action is restore
     */
    public function isRestore(): bool
    {
        return $this->action === 'restore';
    }

    /**
     * Scope for adjust actions
     */
    public function scopeAdjust($query)
    {
        return $query->where('action', 'adjust');
    }

    /**
     * Scope for void actions
     */
    public function scopeVoid($query)
    {
        return $query->where('action', 'void');
    }

    /**
     * Scope for restore actions
     */
    public function scopeRestore($query)
    {
        return $query->where('action', 'restore');
    }
}
