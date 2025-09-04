<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Commission;
use App\Models\Magazine;
use App\Models\MagazineView;
use App\Models\ReportCache;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    /**
     * Show analytics dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $data = [
            'filters' => $filters,
            'kpis' => $this->getKPIs($filters),
            'charts' => $this->getCharts($filters),
            'topEarners' => $this->getTopEarners($filters),
            'magazineEngagement' => $this->getMagazineEngagement($filters),
            'recentReports' => $this->getRecentReports(),
        ];

        // Log analytics access
        AuditLog::log(
            auth()->id(),
            'view_analytics',
            ['filters' => $filters]
        );

        return view('admin.analytics.index', $data);
    }

    /**
     * Generate and export report
     */
    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:users,transactions,subscriptions,commissions,magazines',
            'format' => 'required|in:csv,pdf',
            'filters' => 'array',
        ]);

        $filters = $request->get('filters', []);
        $reportType = $request->get('report_type');
        $format = $request->get('format');

        // Check cache first
        $cacheKey = $this->getCacheKey($reportType, $filters);
        $cachedReport = ReportCache::where('report_name', $cacheKey)->first();

        if ($cachedReport && $cachedReport->isValid()) {
            $data = $cachedReport->data_snapshot;
        } else {
            $data = $this->generateReportData($reportType, $filters);
            
            // Cache the report
            ReportCache::updateOrCreate(
                ['report_name' => $cacheKey],
                [
                    'filters' => $filters,
                    'data_snapshot' => $data,
                    'generated_at' => now(),
                    'created_by_admin_id' => auth()->id(),
                ]
            );
        }

        // Log export action
        AuditLog::log(
            auth()->id(),
            'export_report',
            [
                'report_type' => $reportType,
                'format' => $format,
                'filters' => $filters,
            ]
        );

        if ($format === 'csv') {
            return $this->exportCSV($data, $reportType);
        } else {
            return $this->exportPDF($data, $reportType);
        }
    }

    /**
     * Get real-time KPIs
     */
    public function getKPIsApi(Request $request)
    {
        $filters = $this->getFilters($request);
        
        $kpis = $this->getKPIs($filters);
        
        return response()->json($kpis);
    }

    /**
     * Get chart data
     */
    public function getChartData(Request $request)
    {
        $request->validate([
            'chart_type' => 'required|in:revenue,users,commissions,magazines',
            'period' => 'required|in:7,30,90,365',
        ]);

        $filters = $this->getFilters($request);
        $chartType = $request->get('chart_type');
        $period = $request->get('period');

        $data = $this->getChartDataByType($chartType, $period, $filters);

        return response()->json($data);
    }

    /**
     * Get filters from request
     */
    private function getFilters(Request $request): array
    {
        return [
            'date_from' => $request->get('date_from', now()->subDays(30)->format('Y-m-d')),
            'date_to' => $request->get('date_to', now()->format('Y-m-d')),
            'language' => $request->get('language'),
            'status' => $request->get('status'),
            'level' => $request->get('level'),
        ];
    }

    /**
     * Get KPIs based on filters
     */
    private function getKPIs(array $filters): array
    {
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        // Active users
        $activeUsers = User::where('role', 'user')
            ->where('status', 'active')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        // Active subscriptions
        $activeSubscriptions = User::where('role', 'user')
            ->where('subscription_end_date', '>', now())
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        // Total payments
        $totalPayments = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('amount');

        // Commission payouts
        $commissionPayouts = Commission::where('payout_status', 'paid')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('amount');

        return [
            'active_users' => $activeUsers,
            'active_subscriptions' => $activeSubscriptions,
            'total_payments' => $totalPayments,
            'commission_payouts' => $commissionPayouts,
        ];
    }

    /**
     * Get charts data
     */
    private function getCharts(array $filters): array
    {
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        return [
            'revenue_trend' => $this->getRevenueTrend($dateFrom, $dateTo),
            'user_growth' => $this->getUserGrowth($dateFrom, $dateTo),
            'commission_trend' => $this->getCommissionTrend($dateFrom, $dateTo),
        ];
    }

    /**
     * Get top earners
     */
    private function getTopEarners(array $filters): array
    {
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        return User::select('users.id', 'users.name', 'users.email', 'users.created_at', DB::raw('COALESCE(SUM(commissions.amount), 0) as total_earnings'))
            ->leftJoin('commissions', 'users.id', '=', 'commissions.earner_user_id')
            ->where('users.role', 'user')
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('commissions.created_at', [$dateFrom, $dateTo])
                      ->orWhereNull('commissions.created_at');
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get magazine engagement
     */
    private function getMagazineEngagement(array $filters): array
    {
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        return Magazine::select('magazines.id', 'magazines.title', 'magazines.description', 'magazines.created_at', DB::raw('COALESCE(COUNT(magazine_views.id), 0) as view_count'))
            ->leftJoin('magazine_views', 'magazines.id', '=', 'magazine_views.magazine_id')
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('magazine_views.created_at', [$dateFrom, $dateTo])
                      ->orWhereNull('magazine_views.created_at');
            })
            ->groupBy('magazines.id', 'magazines.title', 'magazines.description', 'magazines.created_at')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get recent reports
     */
    private function getRecentReports()
    {
        return ReportCache::with('admin')
            ->orderBy('generated_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Generate report data
     */
    private function generateReportData(string $reportType, array $filters): array
    {
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        switch ($reportType) {
            case 'users':
                return $this->generateUsersReport($dateFrom, $dateTo, $filters);
            case 'transactions':
                return $this->generateTransactionsReport($dateFrom, $dateTo, $filters);
            case 'subscriptions':
                return $this->generateSubscriptionsReport($dateFrom, $dateTo, $filters);
            case 'commissions':
                return $this->generateCommissionsReport($dateFrom, $dateTo, $filters);
            case 'magazines':
                return $this->generateMagazinesReport($dateFrom, $dateTo, $filters);
            default:
                return [];
        }
    }

    /**
     * Generate users report
     */
    private function generateUsersReport(Carbon $dateFrom, Carbon $dateTo, array $filters): array
    {
        $query = User::where('role', 'user')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->toArray();
    }

    /**
     * Generate transactions report
     */
    private function generateTransactionsReport(Carbon $dateFrom, Carbon $dateTo, array $filters): array
    {
        $query = Transaction::with('user')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->toArray();
    }

    /**
     * Generate subscriptions report
     */
    private function generateSubscriptionsReport(Carbon $dateFrom, Carbon $dateTo, array $filters): array
    {
        $query = User::where('role', 'user')
            ->whereNotNull('subscription_start_date')
            ->whereBetween('subscription_start_date', [$dateFrom, $dateTo]);

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('subscription_end_date', '>', now());
            } else {
                $query->where('subscription_end_date', '<=', now());
            }
        }

        return $query->get()->toArray();
    }

    /**
     * Generate commissions report
     */
    private function generateCommissionsReport(Carbon $dateFrom, Carbon $dateTo, array $filters): array
    {
        $query = Commission::with('user')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if (!empty($filters['status'])) {
            $query->where('payout_status', $filters['status']);
        }

        return $query->get()->toArray();
    }

    /**
     * Generate magazines report
     */
    private function generateMagazinesReport(Carbon $dateFrom, Carbon $dateTo, array $filters): array
    {
        $query = Magazine::withCount(['views', 'entitlements'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if (!empty($filters['language'])) {
            $query->where('language_code', $filters['language']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->toArray();
    }

    /**
     * Get cache key for report
     */
    private function getCacheKey(string $reportType, array $filters): string
    {
        return $reportType . '_' . md5(serialize($filters));
    }

    /**
     * Export to CSV
     */
    private function exportCSV(array $data, string $reportType): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $reportType . '_report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // Write headers
                fputcsv($file, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        }, $filename);
    }

    /**
     * Export to PDF
     */
    private function exportPDF(array $data, string $reportType): \Illuminate\Http\Response
    {
        $filename = $reportType . '_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = PDF::loadView('admin.analytics.reports.pdf', [
            'data' => $data,
            'reportType' => $reportType,
            'generatedAt' => now(),
        ]);

        return $pdf->download($filename);
    }

    /**
     * Get revenue trend data
     */
    private function getRevenueTrend(Carbon $dateFrom, Carbon $dateTo): array
    {
        $days = $dateFrom->diffInDays($dateTo);
        $data = [];

        for ($i = 0; $i <= $days; $i++) {
            $date = $dateFrom->copy()->addDays($i);
            $revenue = Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    /**
     * Get user growth data
     */
    private function getUserGrowth(Carbon $dateFrom, Carbon $dateTo): array
    {
        $days = $dateFrom->diffInDays($dateTo);
        $data = [];

        for ($i = 0; $i <= $days; $i++) {
            $date = $dateFrom->copy()->addDays($i);
            $count = User::where('role', 'user')
                ->whereDate('created_at', '<=', $date)
                ->count();

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ];
        }

        return $data;
    }

    /**
     * Get commission trend data
     */
    private function getCommissionTrend(Carbon $dateFrom, Carbon $dateTo): array
    {
        $days = $dateFrom->diffInDays($dateTo);
        $data = [];

        for ($i = 0; $i <= $days; $i++) {
            $date = $dateFrom->copy()->addDays($i);
            $commission = Commission::whereDate('created_at', $date)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'commission' => $commission,
            ];
        }

        return $data;
    }

    /**
     * Get chart data by type
     */
    private function getChartDataByType(string $chartType, int $period, array $filters): array
    {
        $dateFrom = now()->subDays($period);
        $dateTo = now();

        switch ($chartType) {
            case 'revenue':
                return $this->getRevenueTrend($dateFrom, $dateTo);
            case 'users':
                return $this->getUserGrowth($dateFrom, $dateTo);
            case 'commissions':
                return $this->getCommissionTrend($dateFrom, $dateTo);
            case 'magazines':
                return $this->getMagazineEngagement($filters);
            default:
                return [];
        }
    }
}
