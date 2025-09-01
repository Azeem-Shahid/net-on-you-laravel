<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:cleanup {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old logs, cache, and temporary files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = (int) $this->option('days');
        $cutoffDate = now()->subDays($daysToKeep);
        
        $this->info("Starting system cleanup (keeping logs from last {$daysToKeep} days)...");
        
        try {
            $totalCleaned = 0;
            
            // Clean up old email logs
            $emailLogsCleaned = $this->cleanupEmailLogs($cutoffDate);
            $totalCleaned += $emailLogsCleaned;
            
            // Clean up old admin activity logs
            $adminLogsCleaned = $this->cleanupAdminLogs($cutoffDate);
            $totalCleaned += $adminLogsCleaned;
            
            // Clean up old audit logs
            $auditLogsCleaned = $this->cleanupAuditLogs($cutoffDate);
            $totalCleaned += $auditLogsCleaned;
            
            // Clean up old commission audits
            $commissionAuditsCleaned = $this->cleanupCommissionAudits($cutoffDate);
            $totalCleaned += $commissionAuditsCleaned;
            
            // Clean up old payment notifications
            $paymentNotificationsCleaned = $this->cleanupPaymentNotifications($cutoffDate);
            $totalCleaned += $paymentNotificationsCleaned;
            
            // Clear expired cache
            $cacheCleared = $this->clearExpiredCache();
            
            // Clear old temporary files
            $tempFilesCleared = $this->clearTempFiles();
            
            $this->info("System cleanup completed successfully:");
            $this->info("  - Email logs cleaned: {$emailLogsCleaned}");
            $this->info("  - Admin logs cleaned: {$adminLogsCleaned}");
            $this->info("  - Audit logs cleaned: {$auditLogsCleaned}");
            $this->info("  - Commission audits cleaned: {$commissionAuditsCleaned}");
            $this->info("  - Payment notifications cleaned: {$paymentNotificationsCleaned}");
            $this->info("  - Cache cleared: {$cacheCleared}");
            $this->info("  - Temp files cleared: {$tempFilesCleared}");
            $this->info("  - Total records cleaned: {$totalCleaned}");
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("System cleanup failed: " . $e->getMessage());
            $this->error("Command failed: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Clean up old email logs
     */
    private function cleanupEmailLogs(Carbon $cutoffDate): int
    {
        $deleted = \App\Models\EmailLog::where('created_at', '<', $cutoffDate)
            ->where('status', 'sent')
            ->delete();
        
        $this->line("Cleaned up {$deleted} old email logs");
        return $deleted;
    }
    
    /**
     * Clean up old admin activity logs
     */
    private function cleanupAdminLogs(Carbon $cutoffDate): int
    {
        $deleted = \App\Models\AdminActivityLog::where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->line("Cleaned up {$deleted} old admin activity logs");
        return $deleted;
    }
    
    /**
     * Clean up old audit logs
     */
    private function cleanupAuditLogs(Carbon $cutoffDate): int
    {
        $deleted = \App\Models\AuditLog::where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->line("Cleaned up {$deleted} old audit logs");
        return $deleted;
    }
    
    /**
     * Clean up old commission audits
     */
    private function cleanupCommissionAudits(Carbon $cutoffDate): int
    {
        $deleted = \App\Models\CommissionAudit::where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->line("Cleaned up {$deleted} old commission audits");
        return $deleted;
    }
    
    /**
     * Clean up old payment notifications
     */
    private function cleanupPaymentNotifications(Carbon $cutoffDate): int
    {
        $deleted = \App\Models\PaymentNotification::where('created_at', '<', $cutoffDate)
            ->where('processed', true)
            ->delete();
        
        $this->line("Cleaned up {$deleted} old payment notifications");
        return $deleted;
    }
    
    /**
     * Clear expired cache
     */
    private function clearExpiredCache(): int
    {
        $cleared = 0;
        
        // Clear old report cache (older than 60 minutes)
        $reportCacheCleared = \App\Models\ReportCache::clearExpired(60);
        $cleared += $reportCacheCleared;
        
        // Clear Laravel cache
        Cache::flush();
        $cleared++;
        
        $this->line("Cleared expired cache ({$cleared} items)");
        return $cleared;
    }
    
    /**
     * Clear temporary files
     */
    private function clearTempFiles(): int
    {
        $cleared = 0;
        
        // Clear old storage files (keep last 30 days)
        $storagePath = storage_path('app/temp');
        if (is_dir($storagePath)) {
            $files = glob($storagePath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < now()->subDays(30)->timestamp) {
                    unlink($file);
                    $cleared++;
                }
            }
        }
        
        $this->line("Cleared {$cleared} temporary files");
        return $cleared;
    }
}

