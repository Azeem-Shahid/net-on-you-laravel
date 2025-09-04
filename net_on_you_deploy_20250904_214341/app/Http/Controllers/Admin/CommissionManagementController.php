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
            $data = [
                'currentMonthStats' => [
                    'month' => now()->format('Y-m'),
                    'eligible_commissions' => 0,
                    'ineligible_commissions' => 0,
                    'total_users' => 0,
                    'eligible_users' => 0,
                    'ineligible_users' => 0,
                    'company_earnings' => 0,
                ],
                'previousMonthStats' => [
                    'month' => now()->subMonth()->format('Y-m'),
                    'eligible_commissions' => 0,
                    'ineligible_commissions' => 0,
                    'total_users' => 0,
                    'eligible_users' => 0,
                    'ineligible_users' => 0,
                    'company_earnings' => 0,
                ],
                'totalStats' => [
                    'total_commissions' => 0,
                    'paid' => 0,
                    'pending' => 0,
                    'sent' => 0,
                    'total_users' => 0,
                    'company_earnings' => 0,
                ],
                'pendingPayouts' => collect([]),
                'recentCommissions' => collect([]),
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
}
