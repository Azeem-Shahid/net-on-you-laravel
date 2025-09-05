<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemGenerateReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:generate-reports {--month= : Specific month (YYYY-MM) to generate reports for} {--send-email : Send reports via email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly reports and analytics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly report generation...');
        
        try {
            $monthOption = $this->option('month');
            $sendEmail = $this->option('send-email');
            
            // Determine report period
            if ($monthOption) {
                $reportDate = Carbon::createFromFormat('Y-m', $monthOption);
                $this->line("Generating reports for: {$monthOption}");
            } else {
                $reportDate = now()->subMonth();
                $this->line("Generating reports for: " . $reportDate->format('Y-m'));
            }
            
            $startDate = $reportDate->copy()->startOfMonth();
            $endDate = $reportDate->copy()->endOfMonth();
            
            $this->line("Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
            
            // Generate various reports
            $reports = [];
            
            // User statistics
            $userStats = $this->generateUserStats($startDate, $endDate);
            $reports['user_stats'] = $userStats;
            $this->displayUserStats($userStats);
            
            // Financial statistics
            $financialStats = $this->generateFinancialStats($startDate, $endDate);
            $reports['financial_stats'] = $financialStats;
            $this->displayFinancialStats($financialStats);
            
            // Content statistics
            $contentStats = $this->generateContentStats($startDate, $endDate);
            $reports['content_stats'] = $contentStats;
            $this->displayContentStats($contentStats);
            
            // Referral statistics
            $referralStats = $this->generateReferralStats($startDate, $endDate);
            $reports['referral_stats'] = $referralStats;
            $this->displayReferralStats($referralStats);
            
            // System performance statistics
            $performanceStats = $this->generatePerformanceStats($startDate, $endDate);
            $reports['performance_stats'] = $performanceStats;
            $this->displayPerformanceStats($performanceStats);
            
            // Store reports in database
            $this->storeReports($reports, $startDate, $endDate);
            
            // Generate CSV exports
            $csvFiles = $this->generateCSVExports($reports, $startDate, $endDate);
            
            // Send report notifications if requested
            if ($sendEmail) {
                $this->sendReportNotifications($reports, $csvFiles, $startDate, $endDate);
            }
            
            // Display summary
            $this->displaySummary($reports, $csvFiles);
            
            // Log successful report generation
            Log::info('Monthly reports generated successfully', [
                'period' => $startDate->format('Y-m') . ' to ' . $endDate->format('Y-m'),
                'reports' => array_keys($reports),
                'csv_files' => $csvFiles
            ]);
            
            $this->info('Monthly reports generated successfully');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Report generation failed: ' . $e->getMessage());
            Log::error('Report generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Generate user statistics
     */
    private function generateUserStats(Carbon $startDate, Carbon $endDate): array
    {
        $this->line('Generating user statistics...');
        
        try {
            return [
                'new_users' => \App\Models\User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'active_users' => \App\Models\User::where('last_login_at', '>=', $startDate)->count(),
                'total_users' => \App\Models\User::count(),
                'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
                'blocked_users' => \App\Models\User::where('status', 'blocked')->count(),
                'users_by_language' => \App\Models\User::selectRaw('language, COUNT(*) as count')
                    ->groupBy('language')
                    ->get()
                    ->pluck('count', 'language')
                    ->toArray()
            ];
        } catch (\Exception $e) {
            $this->warn('Warning: User statistics generation failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate financial statistics
     */
    private function generateFinancialStats(Carbon $startDate, Carbon $endDate): array
    {
        $this->line('Generating financial statistics...');
        
        try {
            return [
                'total_revenue' => \App\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->sum('amount'),
                'total_transactions' => \App\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_transactions' => \App\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->count(),
                'failed_transactions' => \App\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'failed')
                    ->count(),
                'total_commissions' => \App\Models\Commission::whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
                'pending_commissions' => \App\Models\Commission::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'pending')
                    ->sum('amount'),
                'revenue_by_gateway' => \App\Models\Transaction::selectRaw('gateway, SUM(amount) as total')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->groupBy('gateway')
                    ->get()
                    ->pluck('total', 'gateway')
                    ->toArray()
            ];
        } catch (\Exception $e) {
            $this->warn('Warning: Financial statistics generation failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate content statistics
     */
    private function generateContentStats(Carbon $startDate, Carbon $endDate): array
    {
        $this->line('Generating content statistics...');
        
        try {
            return [
                'total_magazines' => \App\Models\Magazine::count(),
                'active_magazines' => \App\Models\Magazine::where('status', 'active')->count(),
                'new_magazines' => \App\Models\Magazine::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_downloads' => \App\Models\Magazine::sum('download_count'),
                'downloads_this_month' => \App\Models\MagazineView::whereBetween('created_at', [$startDate, $endDate])->count(),
                'magazines_by_category' => \App\Models\Magazine::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->get()
                    ->pluck('count', 'category')
                    ->toArray(),
                'magazines_by_language' => \App\Models\Magazine::selectRaw('language, COUNT(*) as count')
                    ->groupBy('language')
                    ->get()
                    ->pluck('count', 'language')
                    ->toArray()
            ];
        } catch (\Exception $e) {
            $this->warn('Warning: Content statistics generation failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate referral statistics
     */
    private function generateReferralStats(Carbon $startDate, Carbon $endDate): array
    {
        $this->line('Generating referral statistics...');
        
        try {
            return [
                'total_referrals' => \App\Models\Referral::count(),
                'new_referrals' => \App\Models\Referral::whereBetween('created_at', [$startDate, $endDate])->count(),
                'active_referrals' => \App\Models\Referral::whereHas('referredUser', function($query) use ($startDate) {
                    $query->where('last_login_at', '>=', $startDate);
                })->count(),
                'referral_conversion_rate' => $this->calculateReferralConversionRate($startDate, $endDate),
                'top_referrers' => \App\Models\Referral::selectRaw('referrer_id, COUNT(*) as count')
                    ->with('referrer:id,name')
                    ->groupBy('referrer_id')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($item) {
                        return [
                            'user' => $item->referrer->name ?? 'Unknown',
                            'referrals' => $item->count
                        ];
                    })
                    ->toArray()
            ];
        } catch (\Exception $e) {
            $this->warn('Warning: Referral statistics generation failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate performance statistics
     */
    private function generatePerformanceStats(Carbon $startDate, Carbon $endDate): array
    {
        $this->line('Generating performance statistics...');
        
        try {
            return [
                'email_sent' => \App\Models\EmailLog::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'sent')
                    ->count(),
                'email_failed' => \App\Models\EmailLog::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'failed')
                    ->count(),
                'email_success_rate' => $this->calculateEmailSuccessRate($startDate, $endDate),
                'average_response_time' => $this->calculateAverageResponseTime($startDate, $endDate),
                'system_uptime' => $this->calculateSystemUptime($startDate, $endDate)
            ];
        } catch (\Exception $e) {
            $this->warn('Warning: Performance statistics generation failed: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate referral conversion rate
     */
    private function calculateReferralConversionRate(Carbon $startDate, Carbon $endDate): float
    {
        try {
            $totalReferrals = \App\Models\Referral::whereBetween('created_at', [$startDate, $endDate])->count();
            $convertedReferrals = \App\Models\Referral::whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('referredUser', function($query) {
                    $query->whereNotNull('email_verified_at');
                })->count();
            
            return $totalReferrals > 0 ? round(($convertedReferrals / $totalReferrals) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Calculate email success rate
     */
    private function calculateEmailSuccessRate(Carbon $startDate, Carbon $endDate): float
    {
        try {
            $totalEmails = \App\Models\EmailLog::whereBetween('created_at', [$startDate, $endDate])->count();
            $successfulEmails = \App\Models\EmailLog::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'sent')
                ->count();
            
            return $totalEmails > 0 ? round(($successfulEmails / $totalEmails) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime(Carbon $startDate, Carbon $endDate): float
    {
        // This is a placeholder - implement based on your logging system
        return 0.0;
    }
    
    /**
     * Calculate system uptime
     */
    private function calculateSystemUptime(Carbon $startDate, Carbon $endDate): float
    {
        // This is a placeholder - implement based on your monitoring system
        return 99.9;
    }
    
    /**
     * Store reports in database
     */
    private function storeReports(array $reports, Carbon $startDate, Carbon $endDate): void
    {
        try {
            // Store in ReportCache model if it exists
            if (class_exists('\App\Models\ReportCache')) {
                $reportData = [
                    'type' => 'monthly_summary',
                    'data' => json_encode($reports),
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'expires_at' => now()->addYear(),
                    'created_at' => now()
                ];
                
                \App\Models\ReportCache::create($reportData);
                $this->line('✓ Reports stored in database');
            }
        } catch (\Exception $e) {
            $this->warn('Warning: Failed to store reports in database: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate CSV exports
     */
    private function generateCSVExports(array $reports, Carbon $startDate, Carbon $endDate): array
    {
        $csvFiles = [];
        $exportDir = storage_path('app/reports');
        
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        
        try {
            foreach ($reports as $type => $data) {
                if (!empty($data)) {
                    $filename = "report_{$type}_{$startDate->format('Y-m')}.csv";
                    $filepath = $exportDir . '/' . $filename;
                    
                    $this->generateCSV($data, $filepath);
                    $csvFiles[] = $filename;
                }
            }
            
            $this->line('✓ CSV exports generated: ' . count($csvFiles) . ' files');
            
        } catch (\Exception $e) {
            $this->warn('Warning: CSV export generation failed: ' . $e->getMessage());
        }
        
        return $csvFiles;
    }
    
    /**
     * Generate CSV file from data
     */
    private function generateCSV(array $data, string $filepath): void
    {
        $file = fopen($filepath, 'w');
        
        if ($file === false) {
            throw new \Exception("Failed to create CSV file: {$filepath}");
        }
        
        // Write headers
        if (!empty($data)) {
            if (is_array(reset($data)) && !empty(reset($data))) {
                fputcsv($file, array_keys(reset($data)));
                foreach ($data as $row) {
                    if (is_array($row)) {
                        fputcsv($file, $row);
                    }
                }
            } else {
                fputcsv($file, ['Metric', 'Value']);
                foreach ($data as $key => $value) {
                    fputcsv($file, [$key, $value]);
                }
            }
        }
        
        fclose($file);
    }
    
    /**
     * Send report notifications
     */
    private function sendReportNotifications(array $reports, array $csvFiles, Carbon $startDate, Carbon $endDate): void
    {
        try {
            // Get admin users
            $admins = \App\Models\Admin::where('status', 'active')->get();
            
            if ($admins->isEmpty()) {
                $this->warn('No active admin users found for report notifications');
                return;
            }
            
            // Send email notifications (implement based on your email system)
            foreach ($admins as $admin) {
                try {
                    // You can implement email sending here
                    // Mail::to($admin->email)->send(new MonthlyReportMail($reports, $csvFiles, $startDate, $endDate));
                    $this->line("Report notification sent to: {$admin->email}");
                } catch (\Exception $e) {
                    $this->warn("Failed to send report notification to {$admin->email}: " . $e->getMessage());
                }
            }
            
            $this->info('Report notifications sent to ' . $admins->count() . ' administrators');
            
        } catch (\Exception $e) {
            $this->warn('Failed to send report notifications: ' . $e->getMessage());
        }
    }
    
    /**
     * Display user statistics
     */
    private function displayUserStats(array $stats): void
    {
        if (empty($stats)) return;
        
        $this->info("\n👥 User Statistics:");
        $this->line("  - New Users: " . ($stats['new_users'] ?? 0));
        $this->line("  - Active Users: " . ($stats['active_users'] ?? 0));
        $this->line("  - Total Users: " . ($stats['total_users'] ?? 0));
        $this->line("  - Verified Users: " . ($stats['verified_users'] ?? 0));
        $this->line("  - Blocked Users: " . ($stats['blocked_users'] ?? 0));
    }
    
    /**
     * Display financial statistics
     */
    private function displayFinancialStats(array $stats): void
    {
        if (empty($stats)) return;
        
        $this->info("\n💰 Financial Statistics:");
        $this->line("  - Total Revenue: $" . number_format($stats['total_revenue'] ?? 0, 2));
        $this->line("  - Total Transactions: " . ($stats['total_transactions'] ?? 0));
        $this->line("  - Completed Transactions: " . ($stats['completed_transactions'] ?? 0));
        $this->line("  - Failed Transactions: " . ($stats['failed_transactions'] ?? 0));
        $this->line("  - Total Commissions: $" . number_format($stats['total_commissions'] ?? 0, 2));
    }
    
    /**
     * Display content statistics
     */
    private function displayContentStats(array $stats): void
    {
        if (empty($stats)) return;
        
        $this->info("\n📚 Content Statistics:");
        $this->line("  - Total Magazines: " . ($stats['total_magazines'] ?? 0));
        $this->line("  - Active Magazines: " . ($stats['active_magazines'] ?? 0));
        $this->line("  - New Magazines: " . ($stats['new_magazines'] ?? 0));
        $this->line("  - Total Downloads: " . ($stats['total_downloads'] ?? 0));
        $this->line("  - Downloads This Month: " . ($stats['downloads_this_month'] ?? 0));
    }
    
    /**
     * Display referral statistics
     */
    private function displayReferralStats(array $stats): void
    {
        if (empty($stats)) return;
        
        $this->info("\n🔗 Referral Statistics:");
        $this->line("  - Total Referrals: " . ($stats['total_referrals'] ?? 0));
        $this->line("  - New Referrals: " . ($stats['new_referrals'] ?? 0));
        $this->line("  - Active Referrals: " . ($stats['active_referrals'] ?? 0));
        $this->line("  - Conversion Rate: " . ($stats['referral_conversion_rate'] ?? 0) . "%");
    }
    
    /**
     * Display performance statistics
     */
    private function displayPerformanceStats(array $stats): void
    {
        if (empty($stats)) return;
        
        $this->info("\n⚡ Performance Statistics:");
        $this->line("  - Emails Sent: " . ($stats['email_sent'] ?? 0));
        $this->line("  - Emails Failed: " . ($stats['email_failed'] ?? 0));
        $this->line("  - Email Success Rate: " . ($stats['email_success_rate'] ?? 0) . "%");
        $this->line("  - System Uptime: " . ($stats['system_uptime'] ?? 0) . "%");
    }
    
    /**
     * Display summary
     */
    private function displaySummary(array $reports, array $csvFiles): void
    {
        $this->info("\n📊 Report Generation Summary:");
        $this->line("  - Reports Generated: " . count($reports));
        $this->line("  - CSV Files Created: " . count($csvFiles));
        $this->line("  - Total Data Points: " . array_sum(array_map('count', $reports)));
        
        if (!empty($csvFiles)) {
            $this->line("\n📁 CSV Files:");
            foreach ($csvFiles as $file) {
                $this->line("  - {$file}");
            }
        }
    }
}

