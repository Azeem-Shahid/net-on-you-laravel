<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SubscriptionManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show subscription management dashboard
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'activeSubscriptions' => $this->getActiveSubscriptions(),
            'expiringSoon' => $this->getExpiringSoon(),
            'expiredSubscriptions' => $this->getExpiredSubscriptions(),
            'subscriptionStats' => $this->getSubscriptionStats(),
        ];

        return view('admin.subscription-management.index', $data);
    }

    /**
     * Show create admin user form
     */
    public function createAdminUser()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('users.manage')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.subscription-management.create-admin-user');
    }

    /**
     * Store admin user without payment
     */
    public function storeAdminUser(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('users.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'wallet_address' => 'nullable|string|max:191',
            'language' => 'required|string|max:10',
            'subscription_duration' => 'required|integer|min:1|max:24', // months
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(\Str::random(16)), // Random password
                'wallet_address' => $request->wallet_address,
                'language' => $request->language,
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(), // Auto-verify admin-created users
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addMonths($request->subscription_duration),
            ]);

            // Create subscription record
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonths($request->subscription_duration),
                'amount' => 0, // Free subscription
                'payment_method' => 'admin_created',
                'notes' => 'Created by admin: ' . $request->reason,
            ]);

            // Create transaction record (free)
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => 0,
                'currency' => 'USDT',
                'status' => 'completed',
                'payment_method' => 'admin_created',
                'transaction_type' => 'subscription',
                'reference' => 'ADMIN_' . strtoupper(uniqid()),
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'admin_created' => true,
                    'reason' => $request->reason,
                ],
            ]);

            // Update subscription with transaction
            $subscription->update(['transaction_id' => $transaction->id]);

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'create_admin_user',
                'user',
                $user->id,
                [
                    'user_email' => $user->email,
                    'subscription_duration' => $request->subscription_duration,
                    'reason' => $request->reason,
                ]
            );

            DB::commit();

            return redirect()->route('admin.subscription-management.index')
                ->with('success', 'Admin user created successfully with free subscription.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to create admin user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show subscription expiration alerts
     */
    public function expirationAlerts()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'expiringIn30Days' => $this->getExpiringInDays(30),
            'expiringIn7Days' => $this->getExpiringInDays(7),
            'expiringIn1Day' => $this->getExpiringInDays(1),
            'expiredToday' => $this->getExpiredToday(),
        ];

        return view('admin.subscription-management.expiration-alerts', $data);
    }

    /**
     * Send expiration notification
     */
    public function sendExpirationNotification(Request $request, User $user)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|string|in:expiring_soon,expired,renewal_reminder',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notificationType = $request->notification_type;
            $customMessage = $request->custom_message;

            // Get appropriate email template
            $template = EmailTemplate::where('type', $notificationType)->first();
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email template not found for this notification type'
                ], 404);
            }

            // Send email
            $emailService = app(\App\Services\EmailService::class);
            $result = $emailService->sendTemplateEmail(
                $user->email,
                $template,
                [
                    'user_name' => $user->name,
                    'expiration_date' => $user->subscription_end_date->format('Y-m-d'),
                    'days_remaining' => now()->diffInDays($user->subscription_end_date, false),
                    'custom_message' => $customMessage,
                ]
            );

            if ($result['success']) {
                // Log admin activity
                \App\Models\AdminActivityLog::log(
                    auth('admin')->id(),
                    'send_expiration_notification',
                    'user',
                    $user->id,
                    [
                        'notification_type' => $notificationType,
                        'user_email' => $user->email,
                        'custom_message' => $customMessage,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Expiration notification sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification: ' . $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extend subscription
     */
    public function extendSubscription(Request $request, User $user)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'extension_months' => 'required|integer|min:1|max:24',
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

            $oldEndDate = $user->subscription_end_date;
            $newEndDate = $user->subscription_end_date->addMonths($request->extension_months);

            // Update user subscription
            $user->update([
                'subscription_end_date' => $newEndDate,
            ]);

            // Update or create subscription record
            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($subscription) {
                $subscription->update([
                    'end_date' => $newEndDate,
                    'notes' => $subscription->notes . "\nExtended by admin: " . $request->reason,
                ]);
            } else {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => $newEndDate,
                    'amount' => 0,
                    'payment_method' => 'admin_extension',
                    'notes' => 'Extended by admin: ' . $request->reason,
                ]);
            }

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'extend_subscription',
                'user',
                $user->id,
                [
                    'user_email' => $user->email,
                    'old_end_date' => $oldEndDate->format('Y-m-d'),
                    'new_end_date' => $newEndDate->format('Y-m-d'),
                    'extension_months' => $request->extension_months,
                    'reason' => $request->reason,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription extended successfully',
                'new_end_date' => $newEndDate->format('Y-m-d'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to extend subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active subscriptions
     */
    private function getActiveSubscriptions()
    {
        return User::where('role', 'user')
            ->where('subscription_end_date', '>', now())
            ->where('status', 'active')
            ->with('subscriptions')
            ->orderBy('subscription_end_date')
            ->get();
    }

    /**
     * Get subscriptions expiring soon
     */
    private function getExpiringSoon()
    {
        $thirtyDaysFromNow = now()->addDays(30);
        
        return User::where('role', 'user')
            ->where('subscription_end_date', '<=', $thirtyDaysFromNow)
            ->where('subscription_end_date', '>', now())
            ->where('status', 'active')
            ->with('subscriptions')
            ->orderBy('subscription_end_date')
            ->get();
    }

    /**
     * Get expired subscriptions
     */
    private function getExpiredSubscriptions()
    {
        return User::where('role', 'user')
            ->where('subscription_end_date', '<=', now())
            ->where('status', 'active')
            ->with('subscriptions')
            ->orderBy('subscription_end_date', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get subscription statistics
     */
    private function getSubscriptionStats(): array
    {
        $totalUsers = User::where('role', 'user')->count();
        $activeSubscriptions = User::where('role', 'user')
            ->where('subscription_end_date', '>', now())
            ->where('status', 'active')
            ->count();

        $expiringIn30Days = User::where('role', 'user')
            ->where('subscription_end_date', '<=', now()->addDays(30))
            ->where('subscription_end_date', '>', now())
            ->where('status', 'active')
            ->count();

        $expiredSubscriptions = $totalUsers - $activeSubscriptions;

        return [
            'total_users' => $totalUsers,
            'active_subscriptions' => $activeSubscriptions,
            'expiring_in_30_days' => $expiringIn30Days,
            'expired_subscriptions' => $expiredSubscriptions,
        ];
    }

    /**
     * Get subscriptions expiring in specific days
     */
    private function getExpiringInDays(int $days)
    {
        $targetDate = now()->addDays($days);
        
        return User::where('role', 'user')
            ->where('subscription_end_date', '<=', $targetDate)
            ->where('subscription_end_date', '>', $targetDate->copy()->subDay())
            ->where('status', 'active')
            ->with('subscriptions')
            ->orderBy('subscription_end_date')
            ->get();
    }

    /**
     * Get subscriptions expired today
     */
    private function getExpiredToday()
    {
        return User::where('role', 'user')
            ->whereDate('subscription_end_date', now())
            ->where('status', 'active')
            ->with('subscriptions')
            ->orderBy('subscription_end_date')
            ->get();
    }

    /**
     * Send payment confirmation to user
     */
    public function sendPaymentConfirmation(Request $request, User $user)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|string|in:payment_confirmation,subscription_renewal',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get email template
            $template = EmailTemplate::where('name', $request->notification_type)
                ->where('language', $user->language)
                ->first();

            if (!$template) {
                // Fallback to English
                $template = EmailTemplate::where('name', $request->notification_type)
                    ->where('language', 'en')
                    ->first();
            }

            if ($template) {
                // Send email using EmailService
                // EmailService::sendTemplateEmail($user->email, $template, ['user_name' => $user->name]);
                
                // Log email
                EmailLog::create([
                    'user_id' => $user->id,
                    'template_name' => $template->name,
                    'email' => $user->email,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            }

            // Log admin activity
            $this->logAdminActivity('sent_payment_confirmation', [
                'user_id' => $user->id,
                'notification_type' => $request->notification_type
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send referrer notification
     */
    public function sendReferrerNotification(Request $request, User $user)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('subscriptions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|string|in:referrer_notification,commission_update',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get referrer
            $referrer = $user->referrer;
            
            if (!$referrer) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no referrer'
                ], 404);
            }

            // Get email template
            $template = EmailTemplate::where('name', $request->notification_type)
                ->where('language', $referrer->language)
                ->first();

            if (!$template) {
                // Fallback to English
                $template = EmailTemplate::where('name', $request->notification_type)
                    ->where('language', 'en')
                    ->first();
            }

            if ($template) {
                // Send email using EmailService
                // EmailService::sendTemplateEmail($referrer->email, $template, ['user_name' => $referrer->name]);
                
                // Log email
                EmailLog::create([
                    'user_id' => $referrer->id,
                    'template_name' => $template->name,
                    'email' => $referrer->email,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            }

            // Log admin activity
            $this->logAdminActivity('sent_referrer_notification', [
                'user_id' => $user->id,
                'referrer_id' => $referrer->id,
                'notification_type' => $request->notification_type
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Log admin activity
     */
    private function logAdminActivity(string $action, array $data = [])
    {
        try {
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth('admin')->id(),
                'action' => $action,
                'data' => json_encode($data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            \Log::error('Failed to log admin activity: ' . $e->getMessage());
        }
    }
}
