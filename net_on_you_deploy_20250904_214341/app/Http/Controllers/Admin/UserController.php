<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('wallet_address', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by subscription
        if ($request->filled('subscription')) {
            if ($request->subscription === 'active') {
                $query->where('subscription_end_date', '>', now());
            } elseif ($request->subscription === 'expired') {
                $query->where('subscription_end_date', '<=', now());
            }
        }

        $users = $query->with(['referrer', 'referrals'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Log user listing access
        AdminActivityLog::log(
            auth()->id(),
            'view_users',
            'user_list',
            null,
            ['filters' => $request->all()]
        );

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'language' => 'nullable|string|max:10',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after:subscription_start_date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'language' => $validated['language'] ?? 'en',
            'subscription_start_date' => $validated['subscription_start_date'],
            'subscription_end_date' => $validated['subscription_end_date'],
            'role' => 'user',
            'email_verified_at' => now(), // Auto-verify admin-created users
        ]);

        // Log user creation
        AdminActivityLog::log(
            auth()->id(),
            'create_user',
            'user',
            $user->id,
            [
                'user_email' => $user->email,
                'user_name' => $user->name
            ]
        );

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User created successfully');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load(['referrer', 'referrals', 'transactions', 'commissionsEarned', 'magazineEntitlements']);

        // Log user view
        AdminActivityLog::log(
            auth()->id(),
            'view_user',
            'user',
            $user->id,
            ['user_email' => $user->email]
        );

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show user edit form
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'status' => 'required|in:active,inactive,blocked',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date|after:subscription_start_date',
        ]);

        $oldStatus = $user->status;
        $user->update($validated);

        // Log user update
        AdminActivityLog::log(
            auth()->id(),
            'update_user',
            'user',
            $user->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changes' => $validated
            ]
        );

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    /**
     * Block/unblock user
     */
    public function toggleBlock(User $user)
    {
        $newStatus = $user->status === 'blocked' ? 'active' : 'blocked';
        $oldStatus = $user->status;
        
        $user->update(['status' => $newStatus]);

        // Log status change
        AdminActivityLog::log(
            auth()->id(),
            $newStatus === 'blocked' ? 'block_user' : 'unblock_user',
            'user',
            $user->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_email' => $user->email
            ]
        );

        $message = $newStatus === 'blocked' ? 'User blocked successfully' : 'User unblocked successfully';
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Log password reset
        AdminActivityLog::log(
            auth()->id(),
            'reset_user_password',
            'user',
            $user->id,
            ['user_email' => $user->email]
        );

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'User email is already verified'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        // Log verification email resend
        AdminActivityLog::log(
            auth()->id(),
            'resend_verification_email',
            'user',
            $user->id,
            ['user_email' => $user->email]
        );

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully'
        ]);
    }

    /**
     * Reset user's commission balance to zero after payments
     */
    public function resetBalance(User $user)
    {
        try {
            // Get current month
            $currentMonth = now()->format('Y-m');
            
            // Reset all pending commissions for the current month to 'paid' status
            $updatedCount = \App\Models\Commission::where('earner_user_id', $user->id)
                ->where('month', $currentMonth)
                ->where('payout_status', 'pending')
                ->update(['payout_status' => 'paid']);

            // Log balance reset
            AdminActivityLog::log(
                auth()->id(),
                'reset_user_balance',
                'user',
                $user->id,
                [
                    'user_email' => $user->email,
                    'month' => $currentMonth,
                    'commissions_updated' => $updatedCount
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "User balance reset successfully. {$updatedCount} commissions marked as paid."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset user balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->get();

        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Status', 'Language', 'Wallet Address',
                'Subscription Start', 'Subscription End', 'Referrer ID', 'Created At'
            ]);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->status,
                    $user->language,
                    $user->wallet_address,
                    $user->subscription_start_date,
                    $user->subscription_end_date,
                    $user->referrer_id,
                    $user->created_at
                ]);
            }

            fclose($file);
        };

        // Log export
        AdminActivityLog::log(
            auth()->id(),
            'export_users',
            'user_export',
            null,
            ['count' => $users->count(), 'filters' => $request->all()]
        );

        return response()->stream($callback, 200, $headers);
    }
}
