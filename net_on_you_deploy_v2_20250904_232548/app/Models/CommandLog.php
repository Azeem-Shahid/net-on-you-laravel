<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'command',
        'output',
        'status',
        'executed_by_admin_id',
        'executed_at',
        'error_message',
        'execution_time_ms'
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'status' => 'string',
        'execution_time_ms' => 'integer'
    ];

    /**
     * Get the admin who executed the command
     */
    public function executedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'executed_by_admin_id');
    }

    /**
     * Get the scheduled command
     */
    public function scheduledCommand(): BelongsTo
    {
        return $this->belongsTo(ScheduledCommand::class, 'command', 'command');
    }

    /**
     * Scope for successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for recent executions
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('executed_at', '>=', now()->subDays($days));
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return $this->status === 'success' ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get status icon
     */
    public function getStatusIcon(): string
    {
        return $this->status === 'success' ? '✅' : '❌';
    }

    /**
     * Get execution time in readable format
     */
    public function getExecutionTimeFormatted(): string
    {
        if (!$this->execution_time_ms) {
            return 'N/A';
        }

        if ($this->execution_time_ms < 1000) {
            return $this->execution_time_ms . 'ms';
        }

        return round($this->execution_time_ms / 1000, 2) . 's';
    }

    /**
     * Get truncated output for display
     */
    public function getTruncatedOutput($length = 100): string
    {
        if (!$this->output) {
            return 'No output';
        }

        if (strlen($this->output) <= $length) {
            return $this->output;
        }

        return substr($this->output, 0, $length) . '...';
    }
}
