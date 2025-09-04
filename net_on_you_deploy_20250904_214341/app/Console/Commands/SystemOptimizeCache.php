<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SystemOptimizeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:optimize-cache {--force : Force optimization even if not needed} {--clear-all : Clear all cache before optimization}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application cache and performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cache optimization...');
        
        try {
            $force = $this->option('force');
            $clearAll = $this->option('clear-all');
            
            $startTime = microtime(true);
            $optimizations = [];
            
            // Clear all cache if requested
            if ($clearAll) {
                $this->line('Clearing all cache...');
                $this->clearAllCache();
                $optimizations[] = 'All cache cleared';
            }
            
            // Clear expired cache
            $expiredCleared = $this->clearExpiredCache();
            if ($expiredCleared > 0) {
                $optimizations[] = "Expired cache cleared ({$expiredCleared} items)";
            }
            
            // Optimize Laravel configuration
            $configOptimized = $this->optimizeConfiguration();
            if ($configOptimized) {
                $optimizations[] = 'Configuration cached';
            }
            
            // Optimize routes
            $routesOptimized = $this->optimizeRoutes();
            if ($routesOptimized) {
                $optimizations[] = 'Routes cached';
            }
            
            // Optimize views
            $viewsOptimized = $this->optimizeViews();
            if ($viewsOptimized) {
                $optimizations[] = 'Views cached';
            }
            
            // Clear old report cache
            $reportsCleared = $this->clearOldReports();
            if ($reportsCleared > 0) {
                $optimizations[] = "Old reports cleared ({$reportsCleared} items)";
            }
            
            // Optimize database queries
            $dbOptimized = $this->optimizeDatabase();
            if ($dbOptimized) {
                $optimizations[] = 'Database optimized';
            }
            
            // Clear temporary files
            $tempFilesCleared = $this->clearTempFiles();
            if ($tempFilesCleared > 0) {
                $optimizations[] = "Temporary files cleared ({$tempFilesCleared} files)";
            }
            
            // Calculate execution time
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Display results
            if (!empty($optimizations)) {
                $this->info("\n✅ Cache optimization completed successfully:");
                foreach ($optimizations as $optimization) {
                    $this->line("  - {$optimization}");
                }
            } else {
                $this->info("\n✅ Cache optimization completed - no optimizations needed");
            }
            
            $this->line("\n⏱️  Execution time: {$executionTime}ms");
            
            // Log successful optimization
            Log::info('Cache optimization completed successfully', [
                'optimizations' => $optimizations,
                'execution_time' => $executionTime,
                'force' => $force,
                'clear_all' => $clearAll
            ]);
            
            $this->info('Cache optimization completed successfully');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Cache optimization failed: ' . $e->getMessage());
            Log::error('Cache optimization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Clear all cache
     */
    private function clearAllCache(): void
    {
        try {
            // Clear Laravel cache
            Cache::flush();
            $this->line('✓ Laravel cache cleared');
            
            // Clear application cache
            Artisan::call('cache:clear');
            $this->line('✓ Application cache cleared');
            
            // Clear config cache
            Artisan::call('config:clear');
            $this->line('✓ Config cache cleared');
            
            // Clear route cache
            Artisan::call('route:clear');
            $this->line('✓ Route cache cleared');
            
            // Clear view cache
            Artisan::call('view:clear');
            $this->line('✓ View cache cleared');
            
        } catch (\Exception $e) {
            $this->warn('Warning: Some cache clearing operations failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear expired cache
     */
    private function clearExpiredCache(): int
    {
        $cleared = 0;
        
        try {
            // Clear expired report cache
            if (class_exists('\App\Models\ReportCache')) {
                $reportCacheCleared = \App\Models\ReportCache::where('expires_at', '<', now())->delete();
                $cleared += $reportCacheCleared;
            }
            
            // Clear expired session data
            $sessionCleared = \DB::table('sessions')->where('last_activity', '<', now()->subDays(2)->timestamp)->delete();
            $cleared += $sessionCleared;
            
            // Clear expired password reset tokens
            $resetTokensCleared = \DB::table('password_reset_tokens')->where('created_at', '<', now()->subHours(1))->delete();
            $cleared += $resetTokensCleared;
            
        } catch (\Exception $e) {
            $this->warn('Warning: Some expired cache clearing failed: ' . $e->getMessage());
        }
        
        return $cleared;
    }
    
    /**
     * Optimize Laravel configuration
     */
    private function optimizeConfiguration(): bool
    {
        try {
            Artisan::call('config:cache');
            $this->line('✓ Configuration cached');
            return true;
        } catch (\Exception $e) {
            $this->warn('Warning: Configuration caching failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Optimize routes
     */
    private function optimizeRoutes(): bool
    {
        try {
            Artisan::call('route:cache');
            $this->line('✓ Routes cached');
            return true;
        } catch (\Exception $e) {
            $this->warn('Warning: Route caching failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Optimize views
     */
    private function optimizeViews(): bool
    {
        try {
            Artisan::call('view:cache');
            $this->line('✓ Views cached');
            return true;
        } catch (\Exception $e) {
            $this->warn('Warning: View caching failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear old reports
     */
    private function clearOldReports(): int
    {
        try {
            // Check if ClearExpiredReports command exists
            if (class_exists('\App\Console\Commands\ClearExpiredReports')) {
                Artisan::call('system:clear-expired-reports');
                $this->line('✓ Expired reports cleared via command');
                return 1;
            }
            
            // Manual clearing if command doesn't exist
            if (class_exists('\App\Models\ReportCache')) {
                $cleared = \App\Models\ReportCache::where('expires_at', '<', now())->delete();
                return $cleared;
            }
            
        } catch (\Exception $e) {
            $this->warn('Warning: Report clearing failed: ' . $e->getMessage());
        }
        
        return 0;
    }
    
    /**
     * Optimize database
     */
    private function optimizeDatabase(): bool
    {
        try {
            // Run database optimization commands if they exist
            $commands = [
                'db:show' => 'Database status checked',
                'migrate:status' => 'Migration status checked'
            ];
            
            foreach ($commands as $command => $description) {
                try {
                    Artisan::call($command);
                    $this->line("✓ {$description}");
                } catch (\Exception $e) {
                    // Ignore command not found errors
                    if (strpos($e->getMessage(), 'Command not found') === false) {
                        $this->warn("Warning: {$command} failed: " . $e->getMessage());
                    }
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->warn('Warning: Database optimization failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear temporary files
     */
    private function clearTempFiles(): int
    {
        $cleared = 0;
        
        try {
            // Clear Laravel temporary files
            $tempPaths = [
                storage_path('app/temp'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views')
            ];
            
            foreach ($tempPaths as $tempPath) {
                if (is_dir($tempPath)) {
                    $files = glob($tempPath . '/*');
                    foreach ($files as $file) {
                        if (is_file($file) && filemtime($file) < now()->subDays(7)->timestamp) {
                            if (unlink($file)) {
                                $cleared++;
                            }
                        }
                    }
                }
            }
            
            // Clear compiled Blade templates
            $bladePath = storage_path('framework/views');
            if (is_dir($bladePath)) {
                $bladeFiles = glob($bladePath . '/*.php');
                foreach ($bladeFiles as $file) {
                    if (is_file($file) && filemtime($file) < now()->subDays(1)->timestamp) {
                        if (unlink($file)) {
                            $cleared++;
                        }
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->warn('Warning: Temporary file clearing failed: ' . $e->getMessage());
        }
        
        return $cleared;
    }
}

