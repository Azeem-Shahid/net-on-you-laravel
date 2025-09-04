<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class ScheduledCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'command',
        'frequency',
        'next_run_at',
        'status',
        'description'
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'status' => 'string'
    ];

    /**
     * Get the logs for this command
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CommandLog::class, 'command', 'command');
    }

    /**
     * Get the latest log entry
     */
    public function latestLog()
    {
        return $this->hasOne(CommandLog::class, 'command', 'command')->latest('executed_at');
    }

    /**
     * Check if command is due to run
     */
    public function isDue(): bool
    {
        if ($this->status !== 'active' || !$this->next_run_at) {
            return false;
        }

        return $this->next_run_at->isPast();
    }

    /**
     * Calculate next run time based on frequency
     */
    public function calculateNextRun(): void
    {
        if ($this->status !== 'active') {
            $this->next_run_at = null;
            return;
        }

        $now = Carbon::now();

        switch ($this->frequency) {
            case 'daily':
                $this->next_run_at = $now->addDay()->startOfDay()->addHours(6); // 6 AM
                break;
            case 'weekly':
                $this->next_run_at = $now->addWeek()->startOfWeek()->addDays(1)->addHours(6); // Monday 6 AM
                break;
            case 'monthly':
                $this->next_run_at = $now->addMonth()->startOfMonth()->addDays(1)->addHours(6); // 1st of month 6 AM
                break;
            default:
                $this->next_run_at = null;
        }
    }

    /**
     * Get available commands for scheduling
     */
    public static function getAvailableCommands(): array
    {
        return [
            'system:core-maintenance' => 'Run comprehensive system core maintenance (subscription checks, commission eligibility, commission processing, commission re-evaluation, magazine reminders, system cleanup, health checks, database backup, cache optimization, report generation, clear expired reports)',
            'subscriptions:check-expiry' => 'Check for expired subscriptions and send notifications',
            'commissions:process-monthly' => 'Process monthly commissions for eligible users',
            'commissions:check-eligibility' => 'Check commission eligibility for all users',
            'commissions:re-evaluate-eligibility' => 'Re-evaluate commission eligibility for changes',
            'magazines:release-reminder' => 'Send reminder to admins for bimonthly magazine release',
            'system:cleanup' => 'Clean up old logs, cache, and temporary files',
            'system:health-check' => 'Check system health and send alerts',
            'system:backup-database' => 'Create database backup',
            'system:optimize-cache' => 'Optimize application cache and performance',
            'system:generate-reports' => 'Generate monthly reports and analytics',
            'reports:clear-expired' => 'Clear expired report cache entries',
            'system:clear-expired-reports' => 'Clear expired report cache',
            'queue:work' => 'Process queued jobs',
            'queue:restart' => 'Restart queue workers',
            'cache:clear' => 'Clear application cache',
            'config:cache' => 'Cache configuration files',
            'route:cache' => 'Cache route files',
            'view:cache' => 'Cache view files',
            'storage:link' => 'Create symbolic link for storage',
            'migrate' => 'Run database migrations',
            'migrate:rollback' => 'Rollback database migrations',
            'db:seed' => 'Seed database with data',
            'backup:run' => 'Run database backup',
            'backup:clean' => 'Clean old backups',
            'notifications:table' => 'Create notifications table',
            'vendor:publish' => 'Publish vendor assets',
            'make:command' => 'Create new command',
            'make:controller' => 'Create new controller',
            'make:model' => 'Create new model',
            'make:migration' => 'Create new migration',
            'make:seeder' => 'Create new seeder',
            'make:middleware' => 'Create new middleware',
            'make:provider' => 'Create new service provider',
            'make:request' => 'Create new form request',
            'make:resource' => 'Create new resource',
            'make:test' => 'Create new test',
            'make:job' => 'Create new job',
            'make:listener' => 'Create new event listener',
            'make:event' => 'Create new event',
            'make:mail' => 'Create new mail class',
            'make:notification' => 'Create new notification',
            'make:policy' => 'Create new policy',
            'make:rule' => 'Create new validation rule',
            'make:exception' => 'Create new exception',
            'make:factory' => 'Create new factory',
            'make:channel' => 'Create new broadcasting channel',
            'make:component' => 'Create new component',
            'make:console' => 'Create new console command',
            'make:contract' => 'Create new contract',
            'make:criteria' => 'Create new criteria',
            'make:enum' => 'Create new enum',
            'make:interface' => 'Create new interface',
            'make:observer' => 'Create new observer',
            'make:repository' => 'Create new repository',
            'make:service' => 'Create new service',
            'make:trait' => 'Create new trait',
            'make:value-object' => 'Create new value object'
        ];
    }

    /**
     * Get frequency options
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'manual' => 'Manual Only',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly'
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ];
    }

    /**
     * Get next run time as formatted string
     */
    public function getNextRunTime(): ?string
    {
        if (!$this->next_run_at) {
            return null;
        }

        return $this->next_run_at->format('Y-m-d H:i:s');
    }

    /**
     * Get last run time as formatted string
     */
    public function getLastRunTime(): ?string
    {
        $latestLog = $this->latestLog;
        if (!$latestLog) {
            return null;
        }

        return $latestLog->executed_at->format('Y-m-d H:i:s');
    }
}
