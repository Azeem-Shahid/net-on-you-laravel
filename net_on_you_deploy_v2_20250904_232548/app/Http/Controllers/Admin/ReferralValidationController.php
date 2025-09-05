<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReferralValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show referral validation dashboard
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('referrals.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'referralStats' => $this->getReferralStats(),
            'levelBreakdown' => $this->getLevelBreakdown(),
            'recentReferrals' => $this->getRecentReferrals(),
            'referralTree' => $this->getReferralTree(),
        ];

        return view('admin.referral-validation.index', $data);
    }

    /**
     * Show referral tree for a specific user
     */
    public function showReferralTree(User $user)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('referrals.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'user' => $user,
            'referralTree' => $this->buildReferralTree($user),
            'commissionBreakdown' => $this->getUserCommissionBreakdown($user),
        ];

        return view('admin.referral-validation.referral-tree', $data);
    }

    /**
     * Validate referral system for a specific month
     */
    public function validateReferralSystem(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('referrals.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $month = $request->month;
        $reason = $request->reason;

        try {
            DB::beginTransaction();

            // Validate and recalculate referral commissions for the month
            $validationResults = $this->validateReferralCommissions($month);

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'validate_referral_system',
                'referrals',
                null,
                [
                    'month' => $month,
                    'reason' => $reason,
                    'validation_results' => $validationResults,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Referral system validation completed successfully',
                'validation_results' => $validationResults,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate referral system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test referral system with sample data
     */
    public function testReferralSystem(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('referrals.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'test_user_id' => 'required|exists:users,id',
            'test_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $testUserId = $request->test_user_id;
            $testAmount = $request->test_amount;
            $reason = $request->reason;

            // Create test transaction
            $testTransaction = Transaction::create([
                'user_id' => $testUserId,
                'amount' => $testAmount,
                'currency' => 'USDT',
                'status' => 'completed',
                'payment_method' => 'test',
                'transaction_type' => 'subscription',
                'reference' => 'TEST_' . strtoupper(uniqid()),
                'metadata' => [
                    'test' => true,
                    'reason' => $reason,
                ],
            ]);

            // Calculate referral commissions
            $commissionResults = $this->calculateReferralCommissions($testTransaction);

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'test_referral_system',
                'referrals',
                null,
                [
                    'test_user_id' => $testUserId,
                    'test_amount' => $testAmount,
                    'reason' => $reason,
                    'commission_results' => $commissionResults,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Referral system test completed successfully',
                'test_transaction' => $testTransaction,
                'commission_results' => $commissionResults,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to test referral system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral statistics
     */
    private function getReferralStats(): array
    {
        $totalReferrals = Referral::count();
        $activeReferrals = Referral::where('status', 'active')->count();
        $totalCommissions = Commission::sum('amount');
        $totalUsers = User::where('role', 'user')->count();

        // Calculate referral rates
        $referralRate = $totalUsers > 0 ? ($activeReferrals / $totalUsers) * 100 : 0;
        $averageCommission = $totalReferrals > 0 ? $totalCommissions / $totalReferrals : 0;

        return [
            'total_referrals' => $totalReferrals,
            'active_referrals' => $activeReferrals,
            'total_commissions' => $totalCommissions,
            'total_users' => $totalUsers,
            'referral_rate' => round($referralRate, 2),
            'average_commission' => round($averageCommission, 2),
        ];
    }

    /**
     * Get level breakdown
     */
    private function getLevelBreakdown(): array
    {
        $levels = [1, 2, 3, 4, 5, 6];
        $breakdown = [];

        foreach ($levels as $level) {
            $userCount = Referral::where('level', $level)->distinct('referred_user_id')->count('referred_user_id');
            $commissionAmount = Commission::where('level', $level)->sum('amount');

            $breakdown[] = [
                'level' => $level,
                'user_count' => $userCount,
                'commission_amount' => $commissionAmount,
            ];
        }

        return $breakdown;
    }

    /**
     * Get recent referrals
     */
    private function getRecentReferrals()
    {
        return Referral::with(['referrer', 'referredUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get referral tree overview
     */
    private function getReferralTree(): array
    {
        $users = User::where('role', 'user')
            ->whereNotNull('referrer_id')
            ->with(['referrer', 'referrals'])
            ->get();

        $tree = [];
        foreach ($users as $user) {
            $tree[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'referrer_id' => $user->referrer_id,
                'referrer_name' => $user->referrer->name ?? 'Unknown',
                'referral_count' => $user->referrals->count(),
                'level' => $this->calculateUserLevel($user),
            ];
        }

        return $tree;
    }

    /**
     * Build referral tree for a specific user
     */
    private function buildReferralTree(User $user, int $maxLevel = 6): array
    {
        $tree = [
            'user' => $user,
            'level' => 0,
            'referrals' => [],
        ];

        $this->buildReferralTreeRecursive($tree, $maxLevel, 0);

        return $tree;
    }

    /**
     * Build referral tree recursively
     */
    private function buildReferralTreeRecursive(array &$node, int $maxLevel, int $currentLevel): void
    {
        if ($currentLevel >= $maxLevel) {
            return;
        }

        $referrals = User::where('referrer_id', $node['user']->id)
            ->where('role', 'user')
            ->get();

        foreach ($referrals as $referral) {
            $referralNode = [
                'user' => $referral,
                'level' => $currentLevel + 1,
                'referrals' => [],
            ];

            $this->buildReferralTreeRecursive($referralNode, $maxLevel, $currentLevel + 1);
            $node['referrals'][] = $referralNode;
        }
    }

    /**
     * Get user commission breakdown
     */
    private function getUserCommissionBreakdown(User $user): array
    {
        $commissions = Commission::where('earner_user_id', $user->id)
            ->with(['sourceUser', 'transaction'])
            ->get()
            ->groupBy('level');

        $breakdown = [];
        foreach (range(1, 6) as $level) {
            $levelCommissions = $commissions->get($level, collect());
            $breakdown[$level] = [
                'count' => $levelCommissions->count(),
                'amount' => $levelCommissions->sum('amount'),
                'commissions' => $levelCommissions,
            ];
        }

        return $breakdown;
    }

    /**
     * Calculate user level in referral chain
     */
    private function calculateUserLevel(User $user): int
    {
        $level = 0;
        $currentUser = $user;

        while ($currentUser->referrer_id && $level < 6) {
            $currentUser = User::find($currentUser->referrer_id);
            if (!$currentUser) break;
            $level++;
        }

        return $level;
    }

    /**
     * Validate referral commissions for a specific month
     */
    private function validateReferralCommissions(string $month): array
    {
        $results = [
            'total_transactions' => 0,
            'total_commissions' => 0,
            'validation_errors' => [],
            'level_breakdown' => [],
        ];

        // Get all completed transactions for the month
        $transactions = Transaction::where('status', 'completed')
            ->where('transaction_type', 'subscription')
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->with('user')
            ->get();

        $results['total_transactions'] = $transactions->count();

        foreach ($transactions as $transaction) {
            $user = $transaction->user;
            
            // Calculate expected commissions for this transaction
            $expectedCommissions = $this->calculateExpectedCommissions($user, $transaction->amount);
            
            // Get actual commissions for this transaction
            $actualCommissions = Commission::where('transaction_id', $transaction->id)->get();
            
            // Validate commission amounts
            foreach ($expectedCommissions as $level => $expectedAmount) {
                $actualCommission = $actualCommissions->where('level', $level)->first();
                
                if (!$actualCommission) {
                    $results['validation_errors'][] = [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'level' => $level,
                        'error' => 'Missing commission',
                        'expected_amount' => $expectedAmount,
                    ];
                } elseif (abs($actualCommission->amount - $expectedAmount) > 0.01) {
                    $results['validation_errors'][] = [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'level' => $level,
                        'error' => 'Amount mismatch',
                        'expected_amount' => $expectedAmount,
                        'actual_amount' => $actualCommission->amount,
                    ];
                }
                
                $results['total_commissions'] += $actualCommission ? $actualCommission->amount : 0;
            }
        }

        // Get level breakdown
        $results['level_breakdown'] = Commission::where('month', $month)
            ->selectRaw('level, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('level')
            ->get()
            ->toArray();

        return $results;
    }

    /**
     * Calculate expected referral commissions
     */
    private function calculateExpectedCommissions(User $user, float $amount): array
    {
        $commissions = [];
        $currentUser = $user;
        $commissionRates = [0.10, 0.05, 0.03, 0.02, 0.01, 0.005]; // 10%, 5%, 3%, 2%, 1%, 0.5%

        for ($level = 1; $level <= 6; $level++) {
            if (!$currentUser->referrer_id) break;
            
            $currentUser = User::find($currentUser->referrer_id);
            if (!$currentUser) break;
            
            $commissions[$level] = $amount * $commissionRates[$level - 1];
        }

        return $commissions;
    }

    /**
     * Calculate referral commissions for a transaction
     */
    private function calculateReferralCommissions(Transaction $transaction): array
    {
        $results = [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'commissions_created' => [],
            'total_commission' => 0,
        ];

        $user = $transaction->user;
        $currentUser = $user;
        $commissionRates = [0.10, 0.05, 0.03, 0.02, 0.01, 0.005]; // 10%, 5%, 3%, 2%, 1%, 0.5%

        for ($level = 1; $level <= 6; $level++) {
            if (!$currentUser->referrer_id) break;
            
            $currentUser = User::find($currentUser->referrer_id);
            if (!$currentUser) break;
            
            $commissionAmount = $transaction->amount * $commissionRates[$level - 1];
            
            // Create commission record
            $commission = Commission::create([
                'earner_user_id' => $currentUser->id,
                'source_user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'level' => $level,
                'amount' => $commissionAmount,
                'month' => now()->format('Y-m'),
                'eligibility' => 'pending', // Will be determined monthly
                'payout_status' => 'pending',
            ]);

            $results['commissions_created'][] = [
                'level' => $level,
                'earner_user_id' => $currentUser->id,
                'amount' => $commissionAmount,
                'commission_id' => $commission->id,
            ];

            $results['total_commission'] += $commissionAmount;
        }

        return $results;
    }
}

