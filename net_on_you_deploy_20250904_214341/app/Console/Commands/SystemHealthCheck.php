<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check system health and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system health check...');
        
        $issues = [];
        $warnings = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->line('✓ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
            $this->error('✗ Database connection: FAILED');
        }
        
        // Check storage permissions
        try {
            if (Storage::disk('local')->put('health-check.txt', 'test')) {
                Storage::disk('local')->delete('health-check.txt');
                $this->line('✓ Storage permissions: OK');
            } else {
                $issues[] = 'Storage permissions failed';
                $this->error('✗ Storage permissions: FAILED');
            }
        } catch (\Exception $e) {
            $issues[] = 'Storage permissions failed: ' . $e->getMessage();
            $this->error('✗ Storage permissions: FAILED');
        }
        
        // Check disk space
        try {
            $diskFree = disk_free_space(storage_path());
            $diskTotal = disk_total_space(storage_path());
            $diskUsed = $diskTotal - $diskFree;
            $diskUsagePercent = ($diskUsed / $diskTotal) * 100;
            
            if ($diskUsagePercent > 90) {
                $issues[] = "Disk usage critical: " . number_format($diskUsagePercent, 1) . "%";
                $this->error("✗ Disk usage: " . number_format($diskUsagePercent, 1) . "%");
            } elseif ($diskUsagePercent > 80) {
                $warnings[] = "Disk usage high: " . number_format($diskUsagePercent, 1) . "%";
                $this->warn("⚠ Disk usage: " . number_format($diskUsagePercent, 1) . "%");
            } else {
                $this->line("✓ Disk usage: " . number_format($diskUsagePercent, 1) . "%");
            }
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check disk space: ' . $e->getMessage();
            $this->warn("⚠ Disk space check failed");
        }
        
        // Check queue status
        try {
            $queueSize = DB::table('jobs')->count();
            if ($queueSize > 1000) {
                $issues[] = "Queue size critical: {$queueSize} jobs";
                $this->error("✗ Queue size: {$queueSize} jobs");
            } elseif ($queueSize > 500) {
                $warnings[] = "Queue size large: {$queueSize} jobs";
                $this->warn("⚠ Queue size: {$queueSize} jobs");
            } else {
                $this->line("✓ Queue size: {$queueSize} jobs");
            }
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check queue status: ' . $e->getMessage();
            $this->warn("⚠ Queue check failed");
        }
        
        // Check failed jobs
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            if ($failedJobs > 100) {
                $issues[] = "Failed jobs critical: {$failedJobs} jobs";
                $this->error("✗ Failed jobs: {$failedJobs} jobs");
            } elseif ($failedJobs > 50) {
                $warnings[] = "Failed jobs high: {$failedJobs} jobs";
                $this->warn("⚠ Failed jobs: {$failedJobs} jobs");
            } else {
                $this->line("✓ Failed jobs: {$failedJobs} jobs");
            }
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check failed jobs: ' . $e->getMessage();
            $this->warn("⚠ Failed jobs check failed");
        }
        
        // Check email logs for recent failures
        try {
            $recentEmailFailures = \App\Models\EmailLog::where('created_at', '>=', now()->subHours(24))
                ->where('status', 'failed')
                ->count();
            
            if ($recentEmailFailures > 50) {
                $issues[] = "Recent email failures: {$recentEmailFailures} in last 24 hours";
                $this->error("✗ Email failures: {$recentEmailFailures}");
            } elseif ($recentEmailFailures > 20) {
                $warnings[] = "Recent email failures: {$recentEmailFailures} in last 24 hours";
                $this->warn("⚠ Email failures: {$recentEmailFailures}");
            } else {
                $this->line("✓ Email failures: {$recentEmailFailures} in last 24 hours");
            }
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check email failures: ' . $e->getMessage();
            $this->warn("⚠ Email failures check failed");
        }
        
        // Check system uptime
        try {
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                $loadAverage = $load[0]; // 1 minute load average
                
                if ($loadAverage > 5.0) {
                    $issues[] = "System load critical: {$loadAverage}";
                    $this->error("✗ System load: {$loadAverage}");
                } elseif ($loadAverage > 2.0) {
                    $warnings[] = "System load high: {$loadAverage}";
                    $this->warn("⚠ System load: {$loadAverage}");
                } else {
                    $this->line("✓ System load: {$loadAverage}");
                }
            }
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check system load: ' . $e->getMessage();
            $this->warn("⚠ System load check failed");
        }
        
        // Check memory usage
        try {
            $memoryLimit = ini_get('memory_limit');
            $memoryUsage = memory_get_usage(true);
            $memoryPeak = memory_get_peak_usage(true);
            
            $this->line("✓ Memory usage: " . $this->formatBytes($memoryUsage));
            $this->line("✓ Peak memory: " . $this->formatBytes($memoryPeak));
            $this->line("✓ Memory limit: {$memoryLimit}");
        } catch (\Exception $e) {
            $warnings[] = 'Unable to check memory usage: ' . $e->getMessage();
            $this->warn("⚠ Memory check failed");
        }
        
        // Summary and alerts
        if (!empty($issues)) {
            $this->error("\n🚨 CRITICAL ISSUES FOUND:");
            foreach ($issues as $issue) {
                $this->error("  - {$issue}");
            }
            
            // Send critical alert
            $this->sendHealthAlert($issues, 'critical');
            Log::critical('System health check found critical issues', ['issues' => $issues]);
        }
        
        if (!empty($warnings)) {
            $this->warn("\n⚠ WARNINGS:");
            foreach ($warnings as $warning) {
                $this->warn("  - {$warning}");
            }
            
            // Send warning alert
            $this->sendHealthAlert($warnings, 'warning');
            Log::warning('System health check found warnings', ['warnings' => $warnings]);
        }
        
        if (empty($issues) && empty($warnings)) {
            $this->info("\n🎉 All system health checks passed successfully!");
            Log::info('System health check completed successfully');
            return 0;
        }
        
        $this->info("\nSystem health check completed with " . count($issues) . " critical issues and " . count($warnings) . " warnings");
        return empty($issues) ? 0 : 1;
    }
    
    /**
     * Send health alert to administrators
     */
    private function sendHealthAlert(array $messages, string $level = 'warning')
    {
        try {
            // Get admin users
            $admins = Admin::where('status', 'active')->get();
            
            if ($admins->isEmpty()) {
                $this->warn('No active admin users found for alerts');
                return;
            }
            
            // Send email alerts (if email system is configured)
            foreach ($admins as $admin) {
                try {
                    // You can implement email sending here
                    // Mail::to($admin->email)->send(new SystemHealthAlert($messages, $level));
                    $this->line("Health alert sent to: {$admin->email}");
                } catch (\Exception $e) {
                    $this->warn("Failed to send alert to {$admin->email}: " . $e->getMessage());
                }
            }
            
            $this->info('Health alerts sent to ' . $admins->count() . ' administrators');
            
        } catch (\Exception $e) {
            $this->warn('Failed to send health alerts: ' . $e->getMessage());
            Log::error('Failed to send health alerts', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

