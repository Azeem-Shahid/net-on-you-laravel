<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'earner_user_id',
        'source_user_id',
        'transaction_id',
        'level',
        'amount',
        'month',
        'eligibility',
        'payout_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function earner()
    {
        return $this->belongsTo(User::class, 'earner_user_id');
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the payout batch items that include this commission
     */
    public function payoutBatchItems()
    {
        return $this->belongsToMany(PayoutBatchItem::class, 'payout_batch_items', 'commission_ids', 'id');
    }

    /**
     * Get the audit logs for this commission
     */
    public function audits()
    {
        return $this->hasMany(CommissionAudit::class);
    }

    /**
     * Check if commission is eligible
     */
    public function isEligible(): bool
    {
        return $this->eligibility === 'eligible';
    }

    /**
     * Check if commission is ineligible
     */
    public function isIneligible(): bool
    {
        return $this->eligibility === 'ineligible';
    }

    /**
     * Check if commission is pending payout
     */
    public function isPendingPayout(): bool
    {
        return $this->payout_status === 'pending';
    }

    /**
     * Check if commission is paid
     */
    public function isPaid(): bool
    {
        return $this->payout_status === 'paid';
    }

    /**
     * Check if commission is void
     */
    public function isVoid(): bool
    {
        return $this->payout_status === 'void';
    }

    /**
     * Scope for eligible commissions
     */
    public function scopeEligible($query)
    {
        return $query->where('eligibility', 'eligible');
    }

    /**
     * Scope for ineligible commissions
     */
    public function scopeIneligible($query)
    {
        return $query->where('eligibility', 'ineligible');
    }

    /**
     * Scope for pending payout commissions
     */
    public function scopePendingPayout($query)
    {
        return $query->where('payout_status', 'pending');
    }

    /**
     * Scope for paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('payout_status', 'paid');
    }

    /**
     * Scope for void commissions
     */
    public function scopeVoid($query)
    {
        return $query->where('payout_status', 'void');
    }

    /**
     * Scope for commissions by month
     */
    public function scopeByMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Scope for commissions by level
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }
}

