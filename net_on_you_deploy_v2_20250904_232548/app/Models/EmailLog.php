<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'user_id',
        'email',
        'subject',
        'body_snapshot',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user this email was sent to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the email template used
     */
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_name', 'name');
    }

    /**
     * Scope to get logs by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get logs by template
     */
    public function scopeByTemplate($query, $templateName)
    {
        return $query->where('template_name', $templateName);
    }

    /**
     * Scope to get logs by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get logs by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sent' => '#00ff00',
            'failed' => '#ff0000',
            'queued' => '#1d003f',
            default => '#6c757d'
        };
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'sent' => 'badge-success',
            'failed' => 'badge-danger',
            'queued' => 'badge-secondary',
            default => 'badge-light'
        };
    }
}
