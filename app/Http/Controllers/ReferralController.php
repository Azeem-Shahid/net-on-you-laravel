<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\User;
use App\Services\ReferralService;

class ReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of user's referrals
     */
    public function index()
    {
        $user = Auth::user();
        $referralService = app(ReferralService::class);
        
        // Get referral stats by level
        $referralStats = $referralService->getReferralStats($user->id);
        
        // Get commission earnings
        $commissionEarnings = $referralService->getCommissionEarnings($user->id);
        
        // Get commission breakdown for current month
        $currentMonth = now()->format('Y-m');
        $commissionBreakdown = [
            'current_month' => []
        ];
        
        // Get commissions by level for current month
        for ($level = 1; $level <= 6; $level++) {
            $amount = Commission::where('earner_user_id', $user->id)
                ->where('month', $currentMonth)
                ->where('level', $level)
                ->where('eligibility', 'eligible')
                ->sum('amount');
            
            if ($amount > 0) {
                $commissionBreakdown['current_month'][$level] = $amount;
            }
        }
        
        // Get recent referrals (last 10)
        $recentReferrals = Referral::where('user_id', $user->id)
            ->with('referredUser')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('referrals.index', compact(
            'user',
            'referralStats',
            'commissionEarnings',
            'commissionBreakdown',
            'recentReferrals'
        ));
    }

    /**
     * Display detailed referral information
     */
    public function details()
    {
        $user = Auth::user();
        $referralService = app(ReferralService::class);
        
        // Get detailed referral stats by level
        $referralStats = $referralService->getReferralStats($user->id);
        
        // Get all referrals by level
        $referralsByLevel = [];
        for ($level = 1; $level <= 6; $level++) {
            $referralsByLevel[$level] = Referral::where('user_id', $user->id)
                ->where('level', $level)
                ->with('referredUser')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Get commission earnings by month
        $commissionEarnings = $referralService->getCommissionEarnings($user->id);
        
        // Get monthly commission breakdown for the last 12 months
        $monthlyBreakdown = Commission::where('earner_user_id', $user->id)
            ->selectRaw('month, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Get pending commissions
        $pendingCommissions = Commission::where('earner_user_id', $user->id)
            ->where('payout_status', 'pending')
            ->with('sourceUser')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('referrals.details', compact(
            'user',
            'referralStats',
            'referralsByLevel',
            'commissionEarnings',
            'monthlyBreakdown',
            'pendingCommissions'
        ));
    }
}
