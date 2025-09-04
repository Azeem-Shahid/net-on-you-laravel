<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutBatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'earner_user_id',
        'commission_ids',
        'amount',
        'status',
    ];

    protected $casts = [
        'commission_ids' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the payout batch
     */
    public function batch()
    {
        return $this->belongsTo(PayoutBatch::class, 'batch_id');
    }

    /**
     * Get the earner user
     */
    public function earner()
    {
        return $this->belongsTo(User::class, 'earner_user_id');
    }

    /**
     * Get the commissions included in this payout
     */
    public function commissions()
    {
        return Commission::whereIn('id', $this->commission_ids);
    }

    /**
     * Get the first commission (for single commission payouts)
     */
    public function commission()
    {
        if (empty($this->commission_ids)) {
            return null;
        }
        return Commission::find($this->commission_ids[0]);
    }

    /**
     * Check if item is queued
     */
    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    /**
     * Check if item is sent
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if item failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if item is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Scope for queued items
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    /**
     * Scope for sent items
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed items
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for paid items
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
