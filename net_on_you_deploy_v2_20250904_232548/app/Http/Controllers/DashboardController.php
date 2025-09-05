<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\MagazineEntitlement;
use App\Services\ReferralService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user dashboard
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
        $commissionBreakdown = $referralService->getCommissionBreakdown($user->id);
        
        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get magazine entitlements
        $magazineEntitlements = $user->magazineEntitlements()
            ->with('magazine')
            ->orderBy('granted_at', 'desc')
            ->get();

        return view('dashboard', compact(
            'user',
            'referralStats',
            'commissionEarnings',
            'commissionBreakdown',
            'recentTransactions',
            'magazineEntitlements'
        ));
    }

    /**
     * Show profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'wallet_address' => 'nullable|string|max:191',
            // Language preference is now handled by GTranslate widget in header
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update($request->only(['name', 'wallet_address']));

        return redirect()->route('dashboard')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully!');
    }
}

