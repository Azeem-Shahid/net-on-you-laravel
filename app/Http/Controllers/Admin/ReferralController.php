<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Referral;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    /**
     * Display a listing of referrals
     */
    public function index(Request $request)
    {
        $query = Referral::with(['referrer', 'referredUser']);

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by referrer
        if ($request->filled('referrer_id')) {
            $query->where('user_id', $request->referrer_id);
        }

        // Filter by referred user
        if ($request->filled('referred_user_id')) {
            $query->where('referred_user_id', $request->referred_user_id);
        }

        $referrals = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get referral statistics
        $stats = [
            'total_referrals' => Referral::count(),
            'level_1_count' => Referral::where('level', 1)->count(),
            'level_2_count' => Referral::where('level', 2)->count(),
            'level_3_count' => Referral::where('level', 3)->count(),
            'level_4_count' => Referral::where('level', 4)->count(),
            'level_5_count' => Referral::where('level', 5)->count(),
            'level_6_count' => Referral::where('level', 6)->count(),
        ];

        return view('admin.referrals.index', compact('referrals', 'stats'));
    }

    /**
     * Display the specified referral tree for a user
     */
    public function show(User $user)
    {
        // Get referral tree (6 levels down)
        $referralTree = $this->buildReferralTree($user);

        // Get commission statistics for this user
        $commissionStats = [
            'total_earned' => Commission::where('earner_user_id', $user->id)->sum('amount'),
            'eligible_earned' => Commission::where('earner_user_id', $user->id)
                ->where('eligibility', 'eligible')
                ->sum('amount'),
            'ineligible_earned' => Commission::where('earner_user_id', $user->id)
                ->where('eligibility', 'ineligible')
                ->sum('amount'),
            'pending_payout' => Commission::where('earner_user_id', $user->id)
                ->where('eligibility', 'eligible')
                ->where('payout_status', 'pending')
                ->sum('amount'),
            'paid_out' => Commission::where('earner_user_id', $user->id)
                ->where('eligibility', 'eligible')
                ->where('payout_status', 'paid')
                ->sum('amount'),
        ];

        // Get monthly breakdown
        $monthlyBreakdown = Commission::where('earner_user_id', $user->id)
            ->selectRaw('month, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.referrals.show', compact('user', 'referralTree', 'commissionStats', 'monthlyBreakdown'));
    }

    /**
     * Build referral tree for a user (6 levels down)
     */
    private function buildReferralTree(User $user): array
    {
        $tree = [];

        // Level 1 (direct referrals)
        $level1Users = User::where('referrer_id', $user->id)->get();
        $tree[1] = $level1Users;

        // Level 2-6 (indirect referrals)
        for ($level = 2; $level <= 6; $level++) {
            $levelUsers = collect();
            
            if (isset($tree[$level - 1])) {
                foreach ($tree[$level - 1] as $parentUser) {
                    $children = User::where('referrer_id', $parentUser->id)->get();
                    $levelUsers = $levelUsers->merge($children);
                }
            }
            
            $tree[$level] = $levelUsers;
        }

        return $tree;
    }

    /**
     * Export referrals to CSV
     */
    public function export(Request $request)
    {
        $query = Referral::with(['referrer', 'referredUser']);

        // Apply filters
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $referrals = $query->get();

        $filename = 'referrals_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($referrals) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Referrer ID', 'Referrer Name', 'Referrer Email', 'Referred User ID', 'Referred User Name', 'Referred User Email', 'Level', 'Created At']);
            
            // Data
            foreach ($referrals as $referral) {
                fputcsv($file, [
                    $referral->id,
                    $referral->referrer->id,
                    $referral->referrer->name,
                    $referral->referrer->email,
                    $referral->referredUser->id,
                    $referral->referredUser->name,
                    $referral->referredUser->email,
                    $referral->level,
                    $referral->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
