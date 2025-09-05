<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SystemCoreMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:core-maintenance {--force : Force execution even if not scheduled} {--timeout=300 : Maximum execution time in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run comprehensive system core maintenance including all subscription, commission, cleanup, health, backup, cache, and report operations with proper error handling';

    /**
     * Maximum execution time in seconds
     */
    private $maxExecutionTime;

    /**
     * Start time of execution
     */
    private $startTime;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->startTime = microtime(true);
        $this->maxExecutionTime = (int) $this->option('timeout');

        $this->info('🚀 Starting System Core Maintenance...');
        $this->info("⏱️ Max execution time: {$this->maxExecutionTime} seconds");
        
        $results = [
            'subscription_checks' => false,
            'commission_eligibility' => false,
            'commission_processing' => false,
            'commission_re_evaluation' => false,
            'magazine_reminders' => false,
            'system_cleanup' => false,
            'health_check' => false,
            'database_backup' => false,
            'cache_optimization' => false,
            'report_generation' => false,
            'clear_expired_reports' => false
        ];

        try {
            // Set execution time limit
            set_time_limit($this->maxExecutionTime);

            // 1. Check Subscriptions
            $this->info('📋 Checking subscriptions...');
            $results['subscription_checks'] = $this->runSubscriptionChecks();

            // Check execution time
            if ($this->isExecutionTimeExceeded()) {
                $this->warn('⚠️ Execution time limit approaching, continuing with remaining tasks...');
            }

            // 2. Check Commission Eligibility
            $this->info('💰 Checking commission eligibility...');
            $results['commission_eligibility'] = $this->runCommissionEligibilityCheck();

            // 3. Process Monthly Commissions
            $this->info('💳 Processing monthly commissions...');
            $results['commission_processing'] = $this->runCommissionProcessing();

            // 4. Re-evaluate Commission Eligibility
            $this->info('🔄 Re-evaluating commission eligibility...');
            $results['commission_re_evaluation'] = $this->runCommissionReEvaluation();

            // 5. Magazine Release Reminders
            $this->info('📰 Sending magazine release reminders...');
            $results['magazine_reminders'] = $this->runMagazineReminders();

            // 6. System Cleanup
            $this->info('🧹 Running system cleanup...');
            $results['system_cleanup'] = $this->runSystemCleanup();

            // 7. Health Check
            $this->info('🏥 Running health check...');
            $results['health_check'] = $this->runHealthCheck();

            // 8. Database Backup
            $this->info('💾 Creating database backup...');
            $results['database_backup'] = $this->runDatabaseBackup();

            // 9. Cache Optimization
            $this->info('⚡ Optimizing cache...');
            $results['cache_optimization'] = $this->runCacheOptimization();

            // 10. Generate Reports
            $this->info('📊 Generating reports...');
            $results['report_generation'] = $this->runReportGeneration();

            // 11. Clear Expired Reports
            $this->info('🗑️ Clearing expired reports...');
            $results['clear_expired_reports'] = $this->runClearExpiredReports();

            $executionTime = round((microtime(true) - $this->startTime) * 1000);
            
            // Summary
            $this->displaySummary($results, $executionTime);
            
            // Log the execution
            $this->logExecution($results, $executionTime);
            
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ System Core Maintenance failed: ' . $e->getMessage());
            Log::error('System Core Maintenance failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'execution_time' => round((microtime(true) - $this->startTime) * 1000)
            ]);
            return 1;
        }
    }

    /**
     * Check if execution time limit is exceeded
     */
    private function isExecutionTimeExceeded(): bool
    {
        $elapsedTime = microtime(true) - $this->startTime;
        return $elapsedTime > ($this->maxExecutionTime * 0.8); // 80% of max time
    }

    /**
     * Run subscription checks
     */
    private function runSubscriptionChecks(): bool
    {
        try {
            $exitCode = Artisan::call('subscriptions:check-expiry');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Subscription checks completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Subscription checks completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Subscription checks failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run commission eligibility check
     */
    private function runCommissionEligibilityCheck(): bool
    {
        try {
            $exitCode = Artisan::call('commissions:check-eligibility');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Commission eligibility check completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Commission eligibility check completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Commission eligibility check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run commission processing
     */
    private function runCommissionProcessing(): bool
    {
        try {
            $exitCode = Artisan::call('commissions:process-monthly');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Commission processing completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Commission processing completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Commission processing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run commission re-evaluation
     */
    private function runCommissionReEvaluation(): bool
    {
        try {
            $exitCode = Artisan::call('commissions:re-evaluate-eligibility');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Commission re-evaluation completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Commission re-evaluation completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Commission re-evaluation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run magazine release reminders
     */
    private function runMagazineReminders(): bool
    {
        try {
            $exitCode = Artisan::call('magazines:release-reminder');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Magazine release reminders completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Magazine release reminders completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Magazine release reminders failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run system cleanup
     */
    private function runSystemCleanup(): bool
    {
        try {
            $exitCode = Artisan::call('system:cleanup');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ System cleanup completed successfully');
                return true;
            } else {
                $this->warn('⚠️ System cleanup completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ System cleanup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run health check
     */
    private function runHealthCheck(): bool
    {
        try {
            $exitCode = Artisan::call('system:health-check');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Health check completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Health check completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run database backup
     */
    private function runDatabaseBackup(): bool
    {
        try {
            $exitCode = Artisan::call('system:backup-database');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Database backup completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Database backup completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Database backup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run cache optimization
     */
    private function runCacheOptimization(): bool
    {
        try {
            $exitCode = Artisan::call('system:optimize-cache');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Cache optimization completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Cache optimization completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Cache optimization failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run report generation
     */
    private function runReportGeneration(): bool
    {
        try {
            $exitCode = Artisan::call('system:generate-reports');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Report generation completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Report generation completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Report generation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Run clear expired reports
     */
    private function runClearExpiredReports(): bool
    {
        try {
            $exitCode = Artisan::call('reports:clear-expired');
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                $this->info('✅ Clear expired reports completed successfully');
                return true;
            } else {
                $this->warn('⚠️ Clear expired reports completed with warnings');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('❌ Clear expired reports failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Display execution summary
     */
    private function displaySummary(array $results, int $executionTime): void
    {
        $this->newLine();
        $this->info('📋 System Core Maintenance Summary:');
        $this->info('=====================================');
        
        $successCount = 0;
        foreach ($results as $task => $success) {
            $status = $success ? '✅' : '❌';
            $taskName = ucwords(str_replace('_', ' ', $task));
            $this->line("{$status} {$taskName}");
            if ($success) $successCount++;
        }
        
        $this->newLine();
        $this->info("🎯 Success Rate: {$successCount}/" . count($results) . " (" . round(($successCount/count($results))*100, 1) . "%)");
        $this->info("⏱️ Total Execution Time: {$executionTime}ms");
        $this->info("⏰ Time Limit: {$this->maxExecutionTime} seconds");
        $this->info("📅 Completed at: " . Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * Log the execution results
     */
    private function logExecution(array $results, int $executionTime): void
    {
        $successCount = count(array_filter($results));
        $totalTasks = count($results);
        
        Log::info('System Core Maintenance completed', [
            'success_rate' => round(($successCount/$totalTasks)*100, 1),
            'successful_tasks' => $successCount,
            'total_tasks' => $totalTasks,
            'execution_time_ms' => $executionTime,
            'max_execution_time' => $this->maxExecutionTime,
            'results' => $results,
            'completed_at' => Carbon::now()->toISOString()
        ]);
    }
}
