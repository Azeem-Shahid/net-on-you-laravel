<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\CommissionAudit;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    /**
     * Display a listing of commissions
     */
    public function index(Request $request)
    {
        $query = Commission::with(['earner', 'sourceUser', 'transaction']);

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by eligibility
        if ($request->filled('eligibility')) {
            $query->where('eligibility', $request->eligibility);
        }

        // Filter by payout status
        if ($request->filled('payout_status')) {
            $query->where('payout_status', $request->payout_status);
        }

        // Filter by earner
        if ($request->filled('earner_id')) {
            $query->where('earner_user_id', $request->earner_id);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get commission statistics
        $stats = [
            'total_commissions' => Commission::count(),
            'total_amount' => Commission::sum('amount'),
            'eligible_amount' => Commission::where('eligibility', 'eligible')->sum('amount'),
            'ineligible_amount' => Commission::where('eligibility', 'ineligible')->sum('amount'),
            'pending_payout' => Commission::where('eligibility', 'eligible')
                ->where('payout_status', 'pending')
                ->sum('amount'),
            'paid_amount' => Commission::where('payout_status', 'paid')->sum('amount'),
            'void_amount' => Commission::where('payout_status', 'void')->sum('amount'),
        ];

        return view('admin.commissions.index', compact('commissions', 'stats'));
    }

    /**
     * Display the specified commission
     */
    public function show(Commission $commission)
    {
        $commission->load(['earner', 'sourceUser', 'transaction', 'audits.admin']);
        
        return view('admin.commissions.show', compact('commission'));
    }

    /**
     * Adjust commission amount
     */
    public function adjust(Request $request, Commission $commission)
    {
        $request->validate([
            'new_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $oldAmount = $commission->amount;
            $newAmount = $request->new_amount;

            // Create audit log
            CommissionAudit::create([
                'admin_user_id' => Auth::guard('admin')->id(),
                'commission_id' => $commission->id,
                'action' => 'adjust',
                'before_payload' => $commission->toArray(),
                'after_payload' => array_merge($commission->toArray(), ['amount' => $newAmount]),
                'reason' => $request->reason,
            ]);

            // Update commission
            $commission->update(['amount' => $newAmount]);

            DB::commit();

            Log::info("Commission {$commission->id} adjusted from {$oldAmount} to {$newAmount} by admin " . Auth::guard('admin')->id());

            return redirect()->back()->with('success', 'Commission amount adjusted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to adjust commission {$commission->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to adjust commission amount.');
        }
    }

    /**
     * Void commission
     */
    public function void(Request $request, Commission $commission)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Create audit log
            CommissionAudit::create([
                'admin_user_id' => Auth::guard('admin')->id(),
                'commission_id' => $commission->id,
                'action' => 'void',
                'before_payload' => $commission->toArray(),
                'after_payload' => array_merge($commission->toArray(), ['payout_status' => 'void']),
                'reason' => $request->reason,
            ]);

            // Update commission
            $commission->update(['payout_status' => 'void']);

            DB::commit();

            Log::info("Commission {$commission->id} voided by admin " . Auth::guard('admin')->id());

            return redirect()->back()->with('success', 'Commission voided successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to void commission {$commission->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to void commission.');
        }
    }

    /**
     * Restore voided commission
     */
    public function restore(Request $request, Commission $commission)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($commission->payout_status !== 'void') {
            return redirect()->back()->with('error', 'Commission is not voided.');
        }

        try {
            DB::beginTransaction();

            // Create audit log
            CommissionAudit::create([
                'admin_user_id' => Auth::guard('admin')->id(),
                'commission_id' => $commission->id,
                'action' => 'restore',
                'before_payload' => $commission->toArray(),
                'after_payload' => array_merge($commission->toArray(), ['payout_status' => 'pending']),
                'reason' => $request->reason,
            ]);

            // Update commission
            $commission->update(['payout_status' => 'pending']);

            DB::commit();

            Log::info("Commission {$commission->id} restored by admin " . Auth::guard('admin')->id());

            return redirect()->back()->with('success', 'Commission restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to restore commission {$commission->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restore commission.');
        }
    }

    /**
     * Create payout batch for a specific month
     */
    public function createPayoutBatch(Request $request)
    {
        $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'notes' => 'nullable|string|max:1000',
        ]);

        $month = $request->month;

        try {
            DB::beginTransaction();

            // Check if batch already exists
            if (PayoutBatch::where('period', $month)->exists()) {
                return redirect()->back()->with('error', 'Payout batch for this month already exists.');
            }

            // Get eligible commissions for the month
            $eligibleCommissions = Commission::where('month', $month)
                ->where('eligibility', 'eligible')
                ->where('payout_status', 'pending')
                ->get();

            if ($eligibleCommissions->isEmpty()) {
                return redirect()->back()->with('error', 'No eligible commissions found for this month.');
            }

            // Group commissions by earner
            $earnerCommissions = $eligibleCommissions->groupBy('earner_user_id');

            // Calculate total amount
            $totalAmount = $eligibleCommissions->sum('amount');

            // Create payout batch
            $payoutBatch = PayoutBatch::create([
                'period' => $month,
                'status' => 'open',
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Create payout batch items
            foreach ($earnerCommissions as $earnerId => $commissions) {
                $amount = $commissions->sum('amount');
                $commissionIds = $commissions->pluck('id')->toArray();

                PayoutBatchItem::create([
                    'batch_id' => $payoutBatch->id,
                    'earner_user_id' => $earnerId,
                    'commission_ids' => $commissionIds,
                    'amount' => $amount,
                    'status' => 'queued',
                ]);
            }

            DB::commit();

            Log::info("Payout batch created for month {$month} with {$eligibleCommissions->count()} commissions");

            return redirect()->back()->with('success', 'Payout batch created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create payout batch for month {$month}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create payout batch.');
        }
    }

    /**
     * Export commissions to CSV
     */
    public function export(Request $request)
    {
        $query = Commission::with(['earner', 'sourceUser', 'transaction']);

        // Apply filters
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('eligibility')) {
            $query->where('eligibility', $request->eligibility);
        }
        if ($request->filled('payout_status')) {
            $query->where('payout_status', $request->payout_status);
        }

        $commissions = $query->get();

        $filename = 'commissions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($commissions) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Earner ID', 'Earner Name', 'Earner Email', 'Source User ID', 'Source User Name', 'Transaction ID', 'Level', 'Amount', 'Month', 'Eligibility', 'Payout Status', 'Created At']);
            
            // Data
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->earner->id,
                    $commission->earner->name,
                    $commission->earner->email,
                    $commission->sourceUser->id,
                    $commission->sourceUser->name,
                    $commission->transaction_id,
                    $commission->level,
                    $commission->amount,
                    $commission->month,
                    $commission->eligibility,
                    $commission->payout_status,
                    $commission->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
