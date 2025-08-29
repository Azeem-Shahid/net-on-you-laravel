<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\PaymentNotification;
use App\Models\Setting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Show payment checkout form
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        
        // Check if user gets free access
        if ($user->getsFreeAccess()) {
            // Automatically grant free access
            $this->grantFreeAccess($user);
            return redirect()->route('dashboard')->with('success', 'Free access granted! You now have full access to the platform.');
        }
        
        // Check if user can proceed with payment (has accepted contract)
        $contractController = app(\App\Http\Controllers\ContractController::class);
        if (!$contractController->canProceedWithPayment($user)) {
            return redirect()->route('contract.show')->with('error', 'You must accept the contract before proceeding with payment.');
        }

        $plans = [
            'monthly' => [
                'name' => 'Monthly Plan',
                'price' => 29.99,
                'duration' => 30,
                'description' => 'Access to all magazines for 30 days'
            ],
            'annual' => [
                'name' => 'Annual Plan',
                'price' => 299.99,
                'duration' => 365,
                'description' => 'Access to all magazines for 1 year (Save 17%)'
            ]
        ];

        $selectedPlan = $request->get('plan', 'monthly');
        
        return view('payment.checkout', compact('plans', 'selectedPlan'));
    }

    /**
     * Grant free access to special users
     */
    private function grantFreeAccess(User $user): void
    {
        // Create a free subscription
        $subscription = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'plan_type' => 'free',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYears(2), // 2 years free access
            'amount' => 0.00,
            'currency' => 'USDT',
            'payment_method' => 'free_access',
            'notes' => $user->getFreeAccessReason(),
        ]);

        // Grant magazine entitlements
        $magazines = \App\Models\Magazine::where('is_active', true)->get();
        foreach ($magazines as $magazine) {
            \App\Models\MagazineEntitlement::create([
                'user_id' => $user->id,
                'magazine_id' => $magazine->id,
                'reason' => 'free_access',
                'granted_at' => now(),
                'expires_at' => now()->addYears(2),
            ]);
        }
    }

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,annual',
            'payment_method' => 'required|in:crypto,manual'
        ]);

        $plans = [
            'monthly' => ['price' => 29.99, 'duration' => 30],
            'annual' => ['price' => 299.99, 'duration' => 365]
        ];

        $plan = $plans[$request->plan];
        $user = auth()->user();

        // Check if user already has active subscription
        if ($user->hasActiveSubscription()) {
            return back()->with('error', 'You already have an active subscription.');
        }

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $plan['price'],
            'currency' => 'USD',
            'gateway' => $request->payment_method === 'crypto' ? 'coinpayments' : 'manual',
            'status' => 'pending',
            'notes' => "Subscription payment for {$request->plan} plan",
            'meta' => [
                'plan' => $request->plan,
                'duration_days' => $plan['duration'],
                'payment_method' => $request->payment_method
            ]
        ]);

        if ($request->payment_method === 'crypto') {
            return $this->initiateCryptoPayment($transaction);
        } else {
            return $this->initiateManualPayment($transaction);
        }
    }

    /**
     * Initiate crypto payment
     */
    private function initiateCryptoPayment(Transaction $transaction)
    {
        // Check if CoinPayments is enabled
        if (!config('services.coinpayments.enabled', false)) {
            Log::error('CoinPayments is not enabled');
            return back()->with('error', 'Crypto payments are temporarily unavailable.');
        }

        try {
            // Create payment request to CoinPayments
            $coinPaymentsService = app(\App\Services\CoinPaymentsService::class);
            $response = $coinPaymentsService->createTransaction(
                $transaction->amount,
                'USD',
                auth()->user()->email,
                [
                    'item_name' => $transaction->notes,
                    'invoice' => 'INV-' . $transaction->id . '-' . str()->random(6),
                    'custom' => json_encode([
                        'transaction_id' => $transaction->id,
                        'plan' => $transaction->meta['plan'] ?? 'unknown'
                    ])
                ]
            );
            
            if (isset($response['txn_id'])) {
                $transaction->update([
                    'meta' => array_merge($transaction->meta ?? [], [
                        'txn_id' => $response['txn_id'],
                        'payment_url' => $response['checkout_url'] ?? null,
                        'payment_address' => $response['address'] ?? null,
                        'coinpayments_response' => $response['raw'] ?? []
                    ])
                ]);

                return redirect()->away($response['checkout_url'])
                    ->with('success', 'Payment initiated successfully. Please complete the payment.');
            } else {
                Log::error('CoinPayments payment creation failed', $response);
                return back()->with('error', 'Failed to create payment. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('CoinPayments API error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Payment service error. Please try again.');
        }
    }



    /**
     * Initiate manual payment
     */
    private function initiateManualPayment(Transaction $transaction)
    {
        return redirect()->route('payment.manual', $transaction)
            ->with('success', 'Please upload your payment proof.');
    }

    /**
     * Show payment status
     */
    public function status(Transaction $transaction)
    {
        // Ensure user can only view their own transactions
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.status', compact('transaction'));
    }

    /**
     * Show manual payment form
     */
    public function manual(Transaction $transaction)
    {
        // Ensure user can only view their own transactions
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.manual', compact('transaction'));
    }

    /**
     * Handle manual payment proof upload
     */
    public function uploadProof(Request $request, Transaction $transaction)
    {
        // Ensure user can only upload for their own transactions
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:500'
        ]);

        $file = $request->file('proof_file');
        $filename = 'payment_proof_' . $transaction->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store file in storage/app/public/payment_proofs
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        $transaction->update([
            'notes' => $request->notes ?? 'Payment proof uploaded',
            'meta' => array_merge($transaction->meta ?? [], [
                'proof_file' => $path,
                'uploaded_at' => now()->toISOString()
            ])
        ]);

        return redirect()->route('payment.status', $transaction)
            ->with('success', 'Payment proof uploaded successfully. Admin will review and approve.');
    }

    /**
     * Handle payment webhook
     */
    public function webhook(Request $request)
    {
        Log::info('Payment webhook received', $request->all());

        // Store webhook notification
        PaymentNotification::create([
            'payload' => $request->all(),
            'received_at' => now(),
            'processed' => false
        ]);

        try {
            $this->processWebhook($request);
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Process webhook data
     */
    private function processWebhook(Request $request)
    {
        // This method is now deprecated in favor of coinPaymentsIPN
        // Keeping for backward compatibility with existing webhooks
        Log::info('Legacy webhook received - consider using CoinPayments IPN', $request->all());
        
        // Store webhook notification
        PaymentNotification::create([
            'payload' => $request->all(),
            'received_at' => now(),
            'processed' => false
        ]);

        return response()->json(['status' => 'ok']);
    }



    /**
     * Activate subscription for completed transaction
     */
    private function activateSubscription(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $plan = $transaction->meta['plan'] ?? 'monthly';
            $durationDays = $transaction->meta['duration_days'] ?? 30;

            // Cancel any existing active subscriptions
            Subscription::where('user_id', $transaction->user_id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            // Create new subscription
            Subscription::create([
                'user_id' => $transaction->user_id,
                'plan_name' => $plan,
                'start_date' => now(),
                'end_date' => now()->addDays($durationDays),
                'status' => 'active',
                'last_renewed_at' => now()
            ]);

            // Update transaction meta
            $transaction->update([
                'meta' => array_merge($transaction->meta ?? [], [
                    'subscription_activated' => true,
                    'subscription_activated_at' => now()->toISOString()
                ])
            ]);

            // Generate commissions for this transaction
            try {
                $referralService = app(ReferralService::class);
                $referralService->generateCommissions($transaction);
            } catch (\Exception $e) {
                Log::error("Failed to generate commissions for transaction {$transaction->id}: " . $e->getMessage());
                // Don't fail the subscription activation if commission generation fails
            }
        });
    }

    /**
     * Show payment history
     */
    public function history()
    {
        $transactions = auth()->user()->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payment.history', compact('transactions'));
    }

    /**
     * Create CoinPayments transaction
     */
    public function createCoinPayments(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'plan_id' => 'nullable|string',
        ]);

        $amount = (float)$request->input('amount');
        $user = $request->user();

        $meta = [
            'item_name' => 'Subscription',
            'invoice'   => 'INV-'.now()->format('YmdHis').'-'.str()->random(6),
            'plan_id'   => $request->string('plan_id')->toString(),
        ];

        try {
            $coinPaymentsService = app(\App\Services\CoinPaymentsService::class);
            $tx = $coinPaymentsService->createTransaction($amount, 'USD', $user->email, $meta);

            DB::table('transactions')->insert([
                'user_id'          => $user->id,
                'gateway'          => 'coinpayments',
                'txn_id'           => $tx['txn_id'],
                'status'           => 'pending',
                'amount'           => $amount,
                'currency'         => 'USD',
                'target_currency'  => config('services.coinpayments.currency2'),
                'meta'             => json_encode($tx['raw']),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return redirect()->away($tx['checkout_url']);
        } catch (\Exception $e) {
            Log::error('CoinPayments create transaction error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Handle CoinPayments IPN
     */
    public function coinPaymentsIPN(Request $request)
    {
        $coinPaymentsService = app(\App\Services\CoinPaymentsService::class);
        $verify = $coinPaymentsService->verifyIPN($request);
        
        if (!$verify['ok']) {
            Log::warning('CoinPayments IPN failed verify: '.$verify['error']);
            return response('Invalid', 400);
        }

        $p = $verify['payload'];
        $txnId   = $p['txn_id'] ?? null;
        $status  = (int)($p['status'] ?? 0);
        $amount1 = (float)($p['amount1'] ?? 0); // USD amount requested
        $amount2 = (float)($p['amount2'] ?? 0); // crypto amount paid
        $conf    = (int)($p['confirms'] ?? 0);

        if (!$txnId) return response('No txn_id', 400);

        $mapped = \App\Services\CoinPaymentsService::mapStatus($status);

        $row = DB::table('transactions')->where('txn_id', $txnId)->lockForUpdate()->first();
        if (!$row) return response('Not found', 404);

        DB::transaction(function () use ($row, $mapped, $conf, $amount2) {
            DB::table('transactions')
                ->where('id', $row->id)
                ->update([
                    'status'          => $mapped,
                    'confirmations'   => $conf,
                    'received_amount' => $amount2,
                    'updated_at'      => now(),
                    'processed_at'    => $mapped === 'completed' && is_null($row->processed_at) ? now() : $row->processed_at,
                ]);

            if ($mapped === 'completed' && is_null($row->processed_at)) {
                // TODO: activate subscription / credit wallet / mark order paid (idempotent)
                Log::info('CoinPayments payment completed', [
                    'transaction_id' => $row->id,
                    'txn_id' => $row->txn_id,
                    'amount' => $amount2
                ]);
            }
        });

        return response('OK', 200);
    }
}
