<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show payment tracking dashboard
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('payments.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'recentPayments' => $this->getRecentPayments(),
            'pendingPayments' => $this->getPendingPayments(),
            'paymentStats' => $this->getPaymentStats(),
            'failedPayments' => $this->getFailedPayments(),
        ];

        return view('admin.payment-tracking.index', $data);
    }

    /**
     * Show payment details
     */
    public function show(Transaction $transaction)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('payments.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'transaction' => $transaction->load(['user', 'subscription']),
            'referralCommissions' => $this->getReferralCommissions($transaction),
            'paymentHistory' => $this->getPaymentHistory($transaction),
        ];

        return view('admin.payment-tracking.show', $data);
    }

    /**
     * Mark payment as reviewed
     */
    public function markAsReviewed(Request $request, Transaction $transaction)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('payments.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'review_notes' => 'required|string|max:1000',
            'status' => 'required|string|in:approved,rejected,needs_review',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldStatus = $transaction->status;
            
            $transaction->update([
                'status' => $request->status,
                'reviewed_at' => now(),
                'reviewed_by_admin_id' => auth('admin')->id(),
                'review_notes' => $request->review_notes,
            ]);

            // If payment is approved, process referral commissions
            if ($request->status === 'approved' && $oldStatus !== 'approved') {
                $this->processReferralCommissions($transaction);
            }

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'mark_payment_reviewed',
                'transaction',
                $transaction->id,
                [
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'review_notes' => $request->review_notes,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as reviewed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark payment as reviewed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment confirmation to user
     */
    public function sendPaymentConfirmation(Request $request, Transaction $transaction)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('payments.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
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
            // Get payment confirmation template
            $template = EmailTemplate::where('type', 'payment_confirmation')->first();
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment confirmation email template not found'
                ], 404);
            }

            // Send email
            $emailService = app(\App\Services\EmailService::class);
            $result = $emailService->sendTemplateEmail(
                $transaction->user->email,
                $template,
                [
                    'user_name' => $transaction->user->name,
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => $transaction->payment_method,
                    'payment_date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'custom_message' => $request->custom_message,
                ]
            );

            if ($result['success']) {
                // Log admin activity
                \App\Models\AdminActivityLog::log(
                    auth('admin')->id(),
                    'send_payment_confirmation',
                    'transaction',
                    $transaction->id,
                    [
                        'user_email' => $transaction->user->email,
                        'custom_message' => $request->custom_message,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmation sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send confirmation: ' . $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send confirmation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment notification to referrer
     */
    public function sendReferrerNotification(Request $request, Transaction $transaction)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('payments.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user = $transaction->user;
            if (!$user->referrer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no referrer'
                ], 400);
            }

            $referrer = User::find($user->referrer_id);
            if (!$referrer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referrer not found'
                ], 404);
            }

            // Get referrer notification template
            $template = EmailTemplate::where('type', 'referrer_notification')->first();
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referrer notification email template not found'
                ], 404);
            }

            // Calculate potential commission
            $potentialCommission = $transaction->amount * 0.10; // 10% for level 1

            // Send email
            $emailService = app(\App\Services\EmailService::class);
            $result = $emailService->sendTemplateEmail(
                $referrer->email,
                $template,
                [
                    'referrer_name' => $referrer->name,
                    'referred_user_name' => $user->name,
                    'referred_user_email' => $user->email,
                    'transaction_amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'potential_commission' => $potentialCommission,
                    'payment_date' => $transaction->created_at->format('Y-m-d H:i:s'),
                ]
            );

            if ($result['success']) {
                // Log admin activity
                \App\Models\AdminActivityLog::log(
                    auth('admin')->id(),
                    'send_referrer_notification',
                    'transaction',
                    $transaction->id,
                    [
                        'referrer_email' => $referrer->email,
                        'referred_user_email' => $user->email,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Referrer notification sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send referrer notification: ' . $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send referrer notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments
     */
    private function getRecentPayments()
    {
        return Transaction::with(['user', 'subscription'])
            ->where('transaction_type', 'subscription')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
    }

    /**
     * Get pending payments
     */
    private function getPendingPayments()
    {
        return Transaction::with(['user', 'subscription'])
            ->where('transaction_type', 'subscription')
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStats(): array
    {
        $totalPayments = Transaction::where('transaction_type', 'subscription')->count();
        $completedPayments = Transaction::where('transaction_type', 'subscription')
            ->where('status', 'completed')
            ->count();
        $pendingPayments = Transaction::where('transaction_type', 'subscription')
            ->whereIn('status', ['pending', 'processing'])
            ->count();
        $failedPayments = Transaction::where('transaction_type', 'subscription')
            ->whereIn('status', ['failed', 'cancelled'])
            ->count();

        $totalRevenue = Transaction::where('transaction_type', 'subscription')
            ->where('status', 'completed')
            ->sum('amount');

        return [
            'total_payments' => $totalPayments,
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'failed_payments' => $failedPayments,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Get failed payments
     */
    private function getFailedPayments()
    {
        return Transaction::with(['user', 'subscription'])
            ->where('transaction_type', 'subscription')
            ->whereIn('status', ['failed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get referral commissions for a transaction
     */
    private function getReferralCommissions(Transaction $transaction)
    {
        return Commission::where('transaction_id', $transaction->id)
            ->with(['earner', 'sourceUser'])
            ->orderBy('level')
            ->get();
    }

    /**
     * Get payment history
     */
    private function getPaymentHistory(Transaction $transaction)
    {
        return Transaction::where('user_id', $transaction->user_id)
            ->where('transaction_type', 'subscription')
            ->where('id', '!=', $transaction->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Process referral commissions for a transaction
     */
    private function processReferralCommissions(Transaction $transaction): void
    {
        $user = $transaction->user;
        $currentUser = $user;
        $commissionRates = [0.10, 0.05, 0.03, 0.02, 0.01, 0.005]; // 10%, 5%, 3%, 2%, 1%, 0.5%

        for ($level = 1; $level <= 6; $level++) {
            if (!$currentUser->referrer_id) break;
            
            $currentUser = User::find($currentUser->referrer_id);
            if (!$currentUser) break;
            
            $commissionAmount = $transaction->amount * $commissionRates[$level - 1];
            
            // Create commission record
            Commission::create([
                'earner_user_id' => $currentUser->id,
                'source_user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'level' => $level,
                'amount' => $commissionAmount,
                'month' => now()->format('Y-m'),
                'eligibility' => 'pending', // Will be determined monthly
                'payout_status' => 'pending',
            ]);
        }
    }
}

