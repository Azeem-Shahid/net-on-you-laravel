<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'subscription_type',
        'amount',
        'start_date',
        'end_date',
        'status',
        'notes',
        'last_renewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'last_renewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction that created this subscription
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }



    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->end_date->isFuture();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Check if subscription expires soon (within 7 days)
     */
    public function expiresSoon(): bool
    {
        return $this->isActive() && 
               $this->end_date->diffInDays(now()) <= 7;
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return $this->end_date->diffInDays(now());
    }

    /**
     * Extend subscription by given days
     */
    public function extend(int $days): void
    {
        $this->update([
            'end_date' => $this->end_date->addDays($days),
            'last_renewed_at' => now(),
            'status' => 'active'
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<=', now());
    }

    /**
     * Scope for expiring soon subscriptions
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays($days));
    }
}
