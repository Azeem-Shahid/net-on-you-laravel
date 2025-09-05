<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Magazine;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $data = [
            'totalUsers' => $this->getTotalUsers(),
            'activeUsers' => $this->getActiveUsers(),
            'blockedUsers' => $this->getBlockedUsers(),
            'subscriptionStats' => $this->getSubscriptionStats(),
            'recentTransactions' => $this->getRecentTransactions(),
            'commissionStats' => $this->getCommissionStats(),
            'magazineStats' => $this->getMagazineStats(),
            'recentActivity' => $this->getRecentActivity(),
        ];

        // Log dashboard access
        AdminActivityLog::log(
            auth('admin')->id(),
            'view_dashboard',
            'dashboard',
            null,
            ['page' => 'admin_dashboard']
        );

        return view('admin.dashboard', $data);
    }

    /**
     * Get total users count
     */
    private function getTotalUsers(): int
    {
        return User::where('role', 'user')->count();
    }

    /**
     * Get active users count
     */
    private function getActiveUsers(): int
    {
        return User::where('role', 'user')
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get blocked users count
     */
    private function getBlockedUsers(): int
    {
        return User::where('role', 'user')
            ->where('status', 'blocked')
            ->count();
    }

    /**
     * Get subscription statistics
     */
    private function getSubscriptionStats(): array
    {
        $activeSubscriptions = User::where('role', 'user')
            ->where('subscription_end_date', '>', now())
            ->count();

        $expiredSubscriptions = User::where('role', 'user')
            ->where('subscription_end_date', '<=', now())
            ->count();

        $totalRevenue = Transaction::where('status', 'completed')
            ->sum('amount');

        return [
            'active' => $activeSubscriptions,
            'expired' => $expiredSubscriptions,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        return Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get commission statistics
     */
    private function getCommissionStats(): array
    {
        $totalCommissions = Commission::sum('amount');
        $paidCommissions = Commission::where('payout_status', 'paid')->sum('amount');
        $pendingCommissions = Commission::where('payout_status', 'pending')->sum('amount');

        return [
            'total' => $totalCommissions,
            'paid' => $paidCommissions,
            'pending' => $pendingCommissions,
        ];
    }

    /**
     * Get magazine statistics
     */
    private function getMagazineStats(): array
    {
        $totalMagazines = Magazine::count();
        $activeMagazines = Magazine::where('status', 'active')->count();
        $totalDownloads = Magazine::withCount('entitlements')->get()->sum('entitlements_count');

        return [
            'total' => $totalMagazines,
            'active' => $activeMagazines,
            'downloads' => $totalDownloads,
        ];
    }

    /**
     * Get recent admin activity
     */
    private function getRecentActivity()
    {
        return AdminActivityLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
    }

    /**
     * Get dashboard analytics data
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $data = [
            'userGrowth' => $this->getUserGrowth($period),
            'revenueChart' => $this->getRevenueChart($period),
            'commissionChart' => $this->getCommissionChart($period),
        ];

        return response()->json($data);
    }

    /**
     * Get user growth data
     */
    private function getUserGrowth(int $days): array
    {
        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
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
     * Get revenue chart data
     */
    private function getRevenueChart(int $days): array
    {
        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
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
     * Get commission chart data
     */
    private function getCommissionChart(int $days): array
    {
        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $commission = Commission::whereDate('created_at', $date)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'commission' => $commission,
            ];
        }

        return $data;
    }
}
