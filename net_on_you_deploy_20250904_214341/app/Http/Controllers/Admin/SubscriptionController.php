<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user']);

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan_name', $request->plan);
        }

        // Filter by subscription type
        if ($request->filled('type')) {
            $query->where('subscription_type', $request->type);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get unique plans for filter
        $plans = Subscription::distinct()->pluck('plan_name')->filter();
        $types = Subscription::distinct()->pluck('subscription_type')->filter();

        // Log subscription listing access
        AdminActivityLog::log(
            auth()->id(),
            'view_subscriptions',
            'subscription_list',
            null,
            ['filters' => $request->all()]
        );

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'types'));
    }

    /**
     * Show subscription details
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user']);

        // Log subscription view
        AdminActivityLog::log(
            auth()->id(),
            'view_subscription',
            'subscription',
            $subscription->id,
            ['user_email' => $subscription->user->email]
        );

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create()
    {
        $users = User::where('role', 'user')->where('status', 'active')->get();
        $plans = ['Basic', 'Premium', 'Pro', 'Annual Basic', 'Annual Premium'];
        $types = ['monthly', 'annual', 'lifetime'];

        return view('admin.subscriptions.create', compact('users', 'plans', 'types'));
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_name' => 'required|string|max:100',
            'subscription_type' => 'required|in:monthly,annual,lifetime',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,cancelled,expired',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate end date if not provided
        if (empty($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            switch ($validated['subscription_type']) {
                case 'monthly':
                    $validated['end_date'] = $startDate->copy()->addMonth();
                    break;
                case 'annual':
                    $validated['end_date'] = $startDate->copy()->addYear();
                    break;
                case 'lifetime':
                    $validated['end_date'] = $startDate->copy()->addYears(100);
                    break;
            }
        }

        $subscription = Subscription::create($validated);

        // Update user's subscription dates
        $user = User::find($validated['user_id']);
        $user->update([
            'subscription_start_date' => $validated['start_date'],
            'subscription_end_date' => $validated['end_date'],
        ]);

        // Log subscription creation
        AdminActivityLog::log(
            auth()->id(),
            'create_subscription',
            'subscription',
            $subscription->id,
            [
                'user_email' => $user->email,
                'plan_name' => $validated['plan_name'],
                'amount' => $validated['amount']
            ]
        );

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription created successfully');
    }

    /**
     * Show the form for editing a subscription
     */
    public function edit(Subscription $subscription)
    {
        $plans = ['Basic', 'Premium', 'Pro', 'Annual Basic', 'Annual Premium'];
        $types = ['monthly', 'annual', 'lifetime'];
        
        return view('admin.subscriptions.edit', compact('subscription', 'plans', 'types'));
    }

    /**
     * Update the specified subscription
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:100',
            'subscription_type' => 'required|in:monthly,annual,lifetime',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,cancelled,expired',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $subscription->status;
        $oldEndDate = $subscription->end_date;

        $subscription->update($validated);

        // Update user's subscription dates if they changed
        if ($oldEndDate != $validated['end_date']) {
            $subscription->user->update([
                'subscription_start_date' => $validated['start_date'],
                'subscription_end_date' => $validated['end_date'],
            ]);
        }

        // Log subscription update
        AdminActivityLog::log(
            auth()->id(),
            'update_subscription',
            'subscription',
            $subscription->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'user_email' => $subscription->user->email,
                'changes' => $validated
            ]
        );

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully');
    }

    /**
     * Toggle subscription status (enable/disable)
     */
    public function toggleStatus(Subscription $subscription)
    {
        $newStatus = $subscription->status === 'active' ? 'inactive' : 'active';
        $oldStatus = $subscription->status;

        $subscription->update(['status' => $newStatus]);

        // Update user's subscription status
        if ($newStatus === 'inactive') {
            $subscription->user->update(['subscription_end_date' => now()]);
        } else {
            // Reactivate subscription - extend end date if it's in the past
            if ($subscription->end_date && $subscription->end_date->isPast()) {
                $newEndDate = now()->addMonth();
                $subscription->update(['end_date' => $newEndDate]);
                $subscription->user->update(['subscription_end_date' => $newEndDate]);
            }
        }

        // Log status change
        AdminActivityLog::log(
            auth()->id(),
            $newStatus === 'active' ? 'activate_subscription' : 'deactivate_subscription',
            'subscription',
            $subscription->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_email' => $subscription->user->email
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription ' . $newStatus . 'd successfully',
            'new_status' => $newStatus
        ]);
    }

    /**
     * Extend subscription
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $request->validate([
            'extension_months' => 'required|integer|min:1|max:120',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldEndDate = $subscription->end_date;
        $newEndDate = $subscription->end_date->addMonths($request->extension_months);

        $subscription->update([
            'end_date' => $newEndDate,
            'notes' => $subscription->notes . "\nExtended by " . $request->extension_months . " months on " . now()->format('Y-m-d H:i:s') . ". " . $request->notes,
        ]);

        // Update user's subscription end date
        $subscription->user->update(['subscription_end_date' => $newEndDate]);

        // Log extension
        AdminActivityLog::log(
            auth()->id(),
            'extend_subscription',
            'subscription',
            $subscription->id,
            [
                'old_end_date' => $oldEndDate,
                'new_end_date' => $newEndDate,
                'extension_months' => $request->extension_months,
                'user_email' => $subscription->user->email
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription extended successfully',
            'new_end_date' => $newEndDate->format('Y-m-d')
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription)
    {
        $oldStatus = $subscription->status;
        
        $subscription->update([
            'status' => 'cancelled',
            'notes' => $subscription->notes . "\nCancelled on " . now()->format('Y-m-d H:i:s') . " by admin.",
        ]);

        // Update user's subscription end date to now
        $subscription->user->update(['subscription_end_date' => now()]);

        // Log cancellation
        AdminActivityLog::log(
            auth()->id(),
            'cancel_subscription',
            'subscription',
            $subscription->id,
            [
                'old_status' => $oldStatus,
                'user_email' => $subscription->user->email
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully'
        ]);
    }

    /**
     * Bulk update subscriptions
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'subscription_ids' => 'required|array',
            'subscription_ids.*' => 'exists:subscriptions,id',
            'action' => 'required|in:activate,deactivate,cancel,extend',
            'extension_months' => 'required_if:action,extend|integer|min:1|max:120',
        ]);

        $subscriptions = Subscription::whereIn('id', $request->subscription_ids)->get();
        $updatedCount = 0;

        foreach ($subscriptions as $subscription) {
            switch ($request->action) {
                case 'activate':
                    $subscription->update(['status' => 'active']);
                    if ($subscription->end_date && $subscription->end_date->isPast()) {
                        $newEndDate = now()->addMonth();
                        $subscription->update(['end_date' => $newEndDate]);
                        $subscription->user->update(['subscription_end_date' => $newEndDate]);
                    }
                    break;
                case 'deactivate':
                    $subscription->update(['status' => 'inactive']);
                    $subscription->user->update(['subscription_end_date' => now()]);
                    break;
                case 'cancel':
                    $subscription->update(['status' => 'cancelled']);
                    $subscription->user->update(['subscription_end_date' => now()]);
                    break;
                case 'extend':
                    $newEndDate = $subscription->end_date->addMonths($request->extension_months);
                    $subscription->update(['end_date' => $newEndDate]);
                    $subscription->user->update(['subscription_end_date' => $newEndDate]);
                    break;
            }
            $updatedCount++;
        }

        // Log bulk update
        AdminActivityLog::log(
            auth()->id(),
            'bulk_update_subscriptions',
            'subscription_bulk_update',
            null,
            [
                'action' => $request->action,
                'count' => $updatedCount,
                'subscription_ids' => $request->subscription_ids
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} subscriptions"
        ]);
    }

    /**
     * Export subscriptions to CSV
     */
    public function export(Request $request)
    {
        $query = Subscription::with(['user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan_name', $request->plan);
        }

        $subscriptions = $query->get();

        $filename = 'subscriptions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscriptions) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'User Name', 'User Email', 'Plan', 'Type', 'Amount',
                'Status', 'Start Date', 'End Date', 'Created At'
            ]);

            // Data
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->id,
                    $subscription->user->name,
                    $subscription->user->email,
                    $subscription->plan_name,
                    $subscription->subscription_type,
                    $subscription->amount,
                    $subscription->status,
                    $subscription->start_date,
                    $subscription->end_date,
                    $subscription->created_at
                ]);
            }

            fclose($file);
        };

        // Log export
        AdminActivityLog::log(
            auth()->id(),
            'export_subscriptions',
            'subscription_export',
            null,
            ['count' => $subscriptions->count(), 'filters' => $request->all()]
        );

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get subscription analytics
     */
    public function analytics()
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();
        $cancelledSubscriptions = Subscription::where('status', 'cancelled')->count();

        $monthlyRevenue = Subscription::where('status', 'active')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $yearlyRevenue = Subscription::where('status', 'active')
            ->where('created_at', '>=', now()->startOfYear())
            ->sum('amount');

        $planDistribution = Subscription::select('plan_name', DB::raw('count(*) as count'))
            ->groupBy('plan_name')
            ->get();

        $monthlyGrowth = Subscription::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'expired_subscriptions' => $expiredSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'monthly_revenue' => $monthlyRevenue,
            'yearly_revenue' => $yearlyRevenue,
            'plan_distribution' => $planDistribution,
            'monthly_growth' => $monthlyGrowth
        ]);
    }
}
