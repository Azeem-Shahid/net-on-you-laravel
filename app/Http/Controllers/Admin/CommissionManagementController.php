<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use App\Models\Transaction;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CommissionManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    public function index()
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $currentMonth = now()->format('Y-m');
            $previousMonth = now()->subMonth()->format('Y-m');

            $data = [
                'currentMonthStats' => $this->getMonthlyStats($currentMonth),
                'previousMonthStats' => $this->getMonthlyStats($previousMonth),
                'totalStats' => $this->getTotalStats(),
                'pendingPayouts' => $this->getPendingPayouts(),
                'recentCommissions' => $this->getRecentCommissions(),
                'monthlyPaymentSummary' => $this->getMonthlyPaymentSummary($currentMonth),
                'commissionEligibilityReport' => $this->getCommissionEligibilityReport($currentMonth),
            ];

            return view('admin.commission-management.index', $data);
        } catch (\Exception $e) {
            \Log::error('Commission management dashboard error: ' . $e->getMessage());
            abort(500, 'Internal server error: ' . $e->getMessage());
        }
    }

    public function monthlyBreakdown(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $month = $request->get('month', now()->format('Y-m'));
        
        try {
            // Get commissions for the month
            $commissions = Commission::whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->with('earner')
                ->get();

            // Group by user
            $commissionBreakdown = [];
            $eligibleUsers = collect();
            $ineligibleUsers = collect();
            $companyEarnings = 0;

            foreach ($commissions as $commission) {
                $userId = $commission->earner_user_id;
                
                if (!isset($commissionBreakdown[$userId])) {
                    $commissionBreakdown[$userId] = [
                        'user_name' => $commission->earner->name,
                        'user_email' => $commission->earner->email,
                        'wallet_address' => $commission->earner->wallet_address,
                        'total_commission' => 0,
                        'eligibility' => 'eligible',
                        'payout_status' => 'pending'
                    ];
                }

                $commissionBreakdown[$userId]['total_commission'] += $commission->amount;

                if ($commission->eligibility === 'ineligible') {
                    $commissionBreakdown[$userId]['eligibility'] = 'ineligible';
                    $companyEarnings += $commission->amount;
                }

                // Add user to appropriate collection
                if ($commission->eligibility === 'eligible') {
                    if (!$eligibleUsers->contains('id', $userId)) {
                        $eligibleUsers->push($commission->earner);
                    }
                } else {
                    if (!$ineligibleUsers->contains('id', $userId)) {
                        $ineligibleUsers->push($commission->earner);
                    }
                }
            }

            $data = [
                'month' => $month,
                'monthly_breakdown' => array_values($commissionBreakdown),
                'total_stats' => [
                    'total_commissions' => $commissions->sum('amount'),
                    'eligible_commissions' => $commissions->where('eligibility', 'eligible')->sum('amount'),
                    'ineligible_commissions' => $commissions->where('eligibility', 'ineligible')->sum('amount'),
                    'total_users' => count($commissionBreakdown),
                    'eligible_users' => $eligibleUsers->count(),
                    'ineligible_users' => $ineligibleUsers->count(),
                ],
                'company_earnings' => $companyEarnings,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Monthly breakdown error: ' . $e->getMessage());
            abort(500, 'Internal server error: ' . $e->getMessage());
        }
    }

    public function processMonthlyEligibility(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $month = $request->input('month');
            $reason = $request->input('reason');
            $admin = auth('admin')->user();

            // Get all users with commissions for the month
            $usersWithCommissions = Commission::whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->distinct('earner_user_id')
                ->pluck('earner_user_id');

            foreach ($usersWithCommissions as $userId) {
                // Get all commissions for this user in the month
                $userCommissions = Commission::where('earner_user_id', $userId)
                    ->whereYear('created_at', Carbon::parse($month)->year)
                    ->whereMonth('created_at', Carbon::parse($month)->month)
                    ->get();

                // Check if user has more than one commission (1-sale-per-month rule)
                if ($userCommissions->count() > 1) {
                    // Mark all commissions as ineligible
                    Commission::whereIn('id', $userCommissions->pluck('id'))
                        ->update(['eligibility' => 'ineligible']);
                } else {
                    // Mark commission as eligible
                    Commission::whereIn('id', $userCommissions->pluck('id'))
                        ->update(['eligibility' => 'eligible']);
                }
            }

            // Log admin activity
            AdminActivityLog::create([
                'admin_id' => $admin->id,
                'action' => 'processed_monthly_eligibility',
                'details' => "Processed monthly eligibility for {$month}. Reason: {$reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.commission-management.index')
                ->with('success', 'Monthly eligibility processed successfully for ' . $month);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Process monthly eligibility error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to process monthly eligibility: ' . $e->getMessage()])->withInput();
        }
    }

    public function createPayoutBatch(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $month = $request->input('month');
            $reason = $request->input('reason');
            $admin = auth('admin')->user();

            // Check if payout batch already exists for this month
            $existingBatch = PayoutBatch::where('period', $month)->first();
            if ($existingBatch) {
                return back()->withErrors(['month' => 'Payout batch already exists for this month'])->withInput();
            }

            // Get eligible commissions for the month
            $eligibleCommissions = Commission::where('eligibility', 'eligible')
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->where('payout_status', 'pending')
                ->get();

            if ($eligibleCommissions->isEmpty()) {
                return back()->withErrors(['month' => 'No eligible commissions found for this month'])->withInput();
            }

            // Calculate total amount
            $totalAmount = $eligibleCommissions->sum('amount');

            // Create payout batch
            $payoutBatch = PayoutBatch::create([
                'period' => $month,
                'total_amount' => $totalAmount,
                'status' => 'processing',
                'notes' => $reason,
                'created_by_admin_id' => $admin->id,
            ]);

            // Create payout batch items
            foreach ($eligibleCommissions as $commission) {
                PayoutBatchItem::create([
                    'batch_id' => $payoutBatch->id,
                    'earner_user_id' => $commission->earner_user_id,
                    'commission_ids' => json_encode([$commission->id]),
                    'amount' => $commission->amount,
                    'status' => 'queued',
                ]);

                // Update commission payout status
                $commission->update(['payout_status' => 'processing']);
            }

            // Log admin activity
            AdminActivityLog::create([
                'admin_id' => $admin->id,
                'action' => 'created_payout_batch',
                'details' => "Created payout batch for {$month}. Total amount: {$totalAmount}. Reason: {$reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.commission-management.index')
                ->with('success', 'Payout batch created successfully for ' . $month);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Create payout batch error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create payout batch: ' . $e->getMessage()])->withInput();
        }
    }

    public function markPayoutSent(Request $request, PayoutBatchItem $payoutItem)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $payoutItem->update(['status' => 'sent']);
            
            return response()->json([
                'success' => true,
                'message' => 'Payout marked as sent successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Mark payout sent error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark payout as sent',
            ], 500);
        }
    }

    public function markPayoutPaid(Request $request, PayoutBatchItem $payoutItem)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $payoutItem->update(['status' => 'paid']);
            
            // Update commission payout status
            $payoutItem->commission->update(['payout_status' => 'paid']);
            
            return response()->json([
                'success' => true,
                'message' => 'Payout marked as paid successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Mark payout paid error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark payout as paid',
            ], 500);
        }
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats(string $month): array
    {
        $commissions = Commission::whereYear('created_at', Carbon::parse($month)->year)
            ->whereMonth('created_at', Carbon::parse($month)->month)
            ->get();

        $eligibleCommissions = $commissions->where('eligibility', 'eligible');
        $ineligibleCommissions = $commissions->where('eligibility', 'ineligible');

        $uniqueUsers = $commissions->pluck('earner_user_id')->unique();
        $eligibleUsers = $commissions->where('eligibility', 'eligible')->pluck('earner_user_id')->unique();
        $ineligibleUsers = $commissions->where('eligibility', 'ineligible')->pluck('earner_user_id')->unique();

        return [
            'month' => $month,
            'eligible_commissions' => $eligibleCommissions->sum('amount'),
            'ineligible_commissions' => $ineligibleCommissions->sum('amount'),
            'total_users' => $uniqueUsers->count(),
            'eligible_users' => $eligibleUsers->count(),
            'ineligible_users' => $ineligibleUsers->count(),
            'company_earnings' => $ineligibleCommissions->sum('amount'),
            'total_commissions' => $commissions->sum('amount'),
        ];
    }

    /**
     * Get total statistics
     */
    private function getTotalStats(): array
    {
        $totalCommissions = Commission::sum('amount');
        $paidCommissions = Commission::where('payout_status', 'paid')->sum('amount');
        $pendingCommissions = Commission::where('payout_status', 'pending')->sum('amount');
        $sentCommissions = Commission::where('payout_status', 'sent')->sum('amount');
        $eligibleCommissions = Commission::where('eligibility', 'eligible')->sum('amount');
        $ineligibleCommissions = Commission::where('eligibility', 'ineligible')->sum('amount');

        return [
            'total_commissions' => $totalCommissions,
            'paid' => $paidCommissions,
            'pending' => $pendingCommissions,
            'sent' => $sentCommissions,
            'eligible' => $eligibleCommissions,
            'ineligible' => $ineligibleCommissions,
            'company_earnings' => $ineligibleCommissions,
            'total_users' => Commission::distinct('earner_user_id')->count(),
        ];
    }

    /**
     * Get pending payouts
     */
    private function getPendingPayouts()
    {
        return PayoutBatch::with(['items.earner'])
            ->where('status', 'processing')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get recent commissions
     */
    private function getRecentCommissions()
    {
        return Commission::with(['earner', 'sourceUser', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get monthly payment summary
     */
    private function getMonthlyPaymentSummary(string $month): array
    {
        $commissions = Commission::whereYear('created_at', Carbon::parse($month)->year)
            ->whereMonth('created_at', Carbon::parse($month)->month)
            ->with(['earner'])
            ->get();

        $eligibleCommissions = $commissions->where('eligibility', 'eligible');
        $ineligibleCommissions = $commissions->where('eligibility', 'ineligible');

        // Group by user for detailed breakdown
        $userBreakdown = [];
        foreach ($commissions as $commission) {
            $userId = $commission->earner_user_id;
            if (!isset($userBreakdown[$userId])) {
                $userBreakdown[$userId] = [
                    'user' => $commission->earner,
                    'total_commission' => 0,
                    'eligible_commission' => 0,
                    'ineligible_commission' => 0,
                    'has_direct_sales' => $this->checkDirectSales($commission->earner, $month),
                    'payout_status' => $commission->payout_status,
                ];
            }

            $userBreakdown[$userId]['total_commission'] += $commission->amount;
            
            if ($commission->eligibility === 'eligible') {
                $userBreakdown[$userId]['eligible_commission'] += $commission->amount;
            } else {
                $userBreakdown[$userId]['ineligible_commission'] += $commission->amount;
            }
        }

        return [
            'month' => $month,
            'total_balance' => $eligibleCommissions->sum('amount'),
            'company_earnings' => $ineligibleCommissions->sum('amount'),
            'total_commissions' => $commissions->sum('amount'),
            'eligible_users_count' => $eligibleCommissions->pluck('earner_user_id')->unique()->count(),
            'ineligible_users_count' => $ineligibleCommissions->pluck('earner_user_id')->unique()->count(),
            'user_breakdown' => array_values($userBreakdown),
        ];
    }

    /**
     * Get commission eligibility report
     */
    private function getCommissionEligibilityReport(string $month): array
    {
        $commissions = Commission::whereYear('created_at', Carbon::parse($month)->year)
            ->whereMonth('created_at', Carbon::parse($month)->month)
            ->with(['earner'])
            ->get();

        $report = [
            'month' => $month,
            'users_with_sales' => [],
            'users_without_sales' => [],
            'total_users_with_commissions' => $commissions->pluck('earner_user_id')->unique()->count(),
        ];

        foreach ($commissions->groupBy('earner_user_id') as $userId => $userCommissions) {
            $user = $userCommissions->first()->earner;
            $hasDirectSales = $this->checkDirectSales($user, $month);
            $totalCommission = $userCommissions->sum('amount');
            $eligibleCommission = $userCommissions->where('eligibility', 'eligible')->sum('amount');

            $userData = [
                'user' => $user,
                'total_commission' => $totalCommission,
                'eligible_commission' => $eligibleCommission,
                'has_direct_sales' => $hasDirectSales,
                'commission_count' => $userCommissions->count(),
            ];

            if ($hasDirectSales) {
                $report['users_with_sales'][] = $userData;
            } else {
                $report['users_without_sales'][] = $userData;
            }
        }

        return $report;
    }

    /**
     * Check if user has direct sales in a specific month
     */
    private function checkDirectSales(User $user, string $month): bool
    {
        return $user->hasDirectSalesInMonth($month);
    }

    /**
     * Admin override: Mark user as eligible regardless of sales
     */
    public function overrideEligibility(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $userId = $request->input('user_id');
            $month = $request->input('month');
            $reason = $request->input('reason');
            $admin = auth('admin')->user();

            // Get user's commissions for the month
            $commissions = Commission::where('earner_user_id', $userId)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->get();

            if ($commissions->isEmpty()) {
                return back()->withErrors(['user_id' => 'No commissions found for this user in this month'])->withInput();
            }

            // Update all commissions to eligible
            Commission::whereIn('id', $commissions->pluck('id'))
                ->update(['eligibility' => 'eligible']);

            // Log admin activity
            AdminActivityLog::create([
                'admin_id' => $admin->id,
                'action' => 'override_eligibility',
                'details' => "Override eligibility for user {$userId} in {$month}. Reason: {$reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.commission-management.index')
                ->with('success', 'Eligibility override applied successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Override eligibility error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to override eligibility: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Admin override: Mark user as ineligible
     */
    public function markIneligible(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $userId = $request->input('user_id');
            $month = $request->input('month');
            $reason = $request->input('reason');
            $admin = auth('admin')->user();

            // Get user's commissions for the month
            $commissions = Commission::where('earner_user_id', $userId)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->get();

            if ($commissions->isEmpty()) {
                return back()->withErrors(['user_id' => 'No commissions found for this user in this month'])->withInput();
            }

            // Update all commissions to ineligible
            Commission::whereIn('id', $commissions->pluck('id'))
                ->update(['eligibility' => 'ineligible']);

            // Log admin activity
            AdminActivityLog::create([
                'admin_id' => $admin->id,
                'action' => 'mark_ineligible',
                'details' => "Mark ineligible for user {$userId} in {$month}. Reason: {$reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.commission-management.index')
                ->with('success', 'User marked as ineligible successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mark ineligible error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to mark user as ineligible: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Get detailed user commission report
     */
    public function userCommissionReport(Request $request)
    {
        if (!auth('admin')->user()->hasPermission('commissions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $month = $request->get('month', now()->format('Y-m'));
        $userId = $request->get('user_id');

        $query = Commission::whereYear('created_at', Carbon::parse($month)->year)
            ->whereMonth('created_at', Carbon::parse($month)->month)
            ->with(['earner', 'sourceUser', 'transaction']);

        if ($userId) {
            $query->where('earner_user_id', $userId);
        }

        $commissions = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'month' => $month,
            'commissions' => $commissions,
            'summary' => [
                'total_amount' => $commissions->sum('amount'),
                'eligible_amount' => $commissions->where('eligibility', 'eligible')->sum('amount'),
                'ineligible_amount' => $commissions->where('eligibility', 'ineligible')->sum('amount'),
                'total_count' => $commissions->count(),
            ]
        ]);
    }
}
