<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contract;
use App\Models\ContractAcceptance;
use App\Models\User;

class ContractController extends Controller
{
    /**
     * Show the contract for the user's language
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $language = $user->language ?? 'en';
        
        // Use the new fallback method
        $contract = Contract::getLatestActiveWithFallback($language);
        
        if (!$contract) {
            return redirect()->back()->with('error', 'No contract available.');
        }

        $hasAccepted = ContractAcceptance::hasAcceptedLatest($user, $language);
        
        return view('contract.show', compact('contract', 'hasAccepted', 'user'));
    }

    /**
     * Accept the current contract
     */
    public function accept(Request $request)
    {
        $user = Auth::user();
        $language = $user->language ?? 'en';
        
        // Check if user has already accepted
        if (ContractAcceptance::hasAcceptedLatest($user, $language)) {
            return redirect()->back()->with('info', 'You have already accepted this contract.');
        }

        // Accept the contract
        $success = ContractAcceptance::acceptLatest(
            $user, 
            $language, 
            $request->ip(), 
            $request->userAgent()
        );

        if ($success) {
            return redirect()->back()->with('success', 'Contract accepted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to accept contract. Please try again.');
    }

    /**
     * Show contract acceptance status
     */
    public function status()
    {
        $user = Auth::user();
        $language = $user->language ?? 'en';
        
        // Use the new fallback method
        $contract = Contract::getLatestActiveWithFallback($language);
        $hasAccepted = ContractAcceptance::hasAcceptedLatest($user, $language);
        $acceptanceHistory = $user->contractAcceptances()
            ->with('contract')
            ->orderBy('accepted_at', 'desc')
            ->get();

        return view('contract.status', compact('user', 'contract', 'hasAccepted', 'acceptanceHistory'));
    }

    /**
     * Check if user can proceed with payment (has accepted contract or special access)
     */
    public function canProceedWithPayment(User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Users with special access can proceed without contract acceptance
        if ($user->hasSpecialAccess()) {
            return true;
        }

        // Other users must accept the contract
        return $user->hasAcceptedLatestContract();
    }
}
