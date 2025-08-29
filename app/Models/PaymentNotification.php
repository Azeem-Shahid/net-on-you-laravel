<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'payload',
        'received_at',
        'processed',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed' => 'boolean',
    ];

    /**
     * Scope for unprocessed notifications
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    /**
     * Scope for processed notifications
     */
    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    /**
     * Mark notification as processed
     */
    public function markAsProcessed(): void
    {
        $this->update(['processed' => true]);
    }

    /**
     * Get notification type from payload
     */
    public function getType(): ?string
    {
        return $this->payload['type'] ?? null;
    }

    /**
     * Get transaction hash from payload
     */
    public function getTransactionHash(): ?string
    {
        return $this->payload['transaction_hash'] ?? null;
    }

    /**
     * Get payment status from payload
     */
    public function getStatus(): ?string
    {
        return $this->payload['status'] ?? null;
    }
}
