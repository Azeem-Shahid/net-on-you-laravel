<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    /**
     * Display a listing of payout batches
     */
    public function index()
    {
        $payoutBatches = PayoutBatch::with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get payout statistics
        $stats = [
            'total_batches' => PayoutBatch::count(),
            'open_batches' => PayoutBatch::where('status', 'open')->count(),
            'processing_batches' => PayoutBatch::where('status', 'processing')->count(),
            'closed_batches' => PayoutBatch::where('status', 'closed')->count(),
            'total_paid' => PayoutBatch::where('status', 'closed')->sum('total_amount'),
        ];

        return view('admin.payouts.index', compact('payoutBatches', 'stats'));
    }

    /**
     * Display the specified payout batch
     */
    public function show(PayoutBatch $payoutBatch)
    {
        $payoutBatch->load(['items.earner']);
        
        return view('admin.payouts.show', compact('payoutBatch'));
    }

    /**
     * Start processing a payout batch
     */
    public function startProcessing(PayoutBatch $payoutBatch)
    {
        if (!$payoutBatch->isOpen()) {
            return redirect()->back()->with('error', 'Payout batch is not open.');
        }

        try {
            $payoutBatch->update(['status' => 'processing']);
            
            Log::info("Payout batch {$payoutBatch->id} started processing by admin " . Auth::guard('admin')->id());
            
            return redirect()->back()->with('success', 'Payout batch processing started.');
        } catch (\Exception $e) {
            Log::error("Failed to start processing payout batch {$payoutBatch->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to start processing.');
        }
    }

    /**
     * Mark payout batch item as sent
     */
    public function markAsSent(PayoutBatchItem $payoutBatchItem)
    {
        if (!$payoutBatchItem->isQueued()) {
            return redirect()->back()->with('error', 'Payout item is not queued.');
        }

        try {
            $payoutBatchItem->update(['status' => 'sent']);
            
            Log::info("Payout batch item {$payoutBatchItem->id} marked as sent by admin " . Auth::guard('admin')->id());
            
            return redirect()->back()->with('success', 'Payout item marked as sent.');
        } catch (\Exception $e) {
            Log::error("Failed to mark payout item {$payoutBatchItem->id} as sent: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark as sent.');
        }
    }

    /**
     * Mark payout batch item as paid
     */
    public function markAsPaid(PayoutBatchItem $payoutBatchItem)
    {
        if (!$payoutBatchItem->isSent()) {
            return redirect()->back()->with('error', 'Payout item is not sent.');
        }

        try {
            DB::beginTransaction();

            // Update payout item status
            $payoutBatchItem->update(['status' => 'paid']);

            // Update underlying commissions
            Commission::whereIn('id', $payoutBatchItem->commission_ids)
                ->update(['payout_status' => 'paid']);

            DB::commit();
            
            Log::info("Payout batch item {$payoutBatchItem->id} marked as paid by admin " . Auth::guard('admin')->id());
            
            return redirect()->back()->with('success', 'Payout item marked as paid.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to mark payout item {$payoutBatchItem->id} as paid: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark as paid.');
        }
    }

    /**
     * Mark payout batch item as failed
     */
    public function markAsFailed(Request $request, PayoutBatchItem $payoutBatchItem)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$payoutBatchItem->isSent()) {
            return redirect()->back()->with('error', 'Payout item is not sent.');
        }

        try {
            $payoutBatchItem->update([
                'status' => 'failed',
                'notes' => $request->reason,
            ]);
            
            Log::info("Payout batch item {$payoutBatchItem->id} marked as failed by admin " . Auth::guard('admin')->id());
            
            return redirect()->back()->with('success', 'Payout item marked as failed.');
        } catch (\Exception $e) {
            Log::error("Failed to mark payout item {$payoutBatchItem->id} as failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark as failed.');
        }
    }

    /**
     * Close a payout batch
     */
    public function close(PayoutBatch $payoutBatch)
    {
        if (!$payoutBatch->isProcessing()) {
            return redirect()->back()->with('error', 'Payout batch is not processing.');
        }

        // Check if all items are processed
        $pendingItems = $payoutBatch->items()
            ->whereNotIn('status', ['paid', 'failed'])
            ->count();

        if ($pendingItems > 0) {
            return redirect()->back()->with('error', "Cannot close batch. {$pendingItems} items still pending.");
        }

        try {
            $payoutBatch->update(['status' => 'closed']);
            
            Log::info("Payout batch {$payoutBatch->id} closed by admin " . Auth::guard('admin')->id());
            
            return redirect()->back()->with('success', 'Payout batch closed successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to close payout batch {$payoutBatch->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to close batch.');
        }
    }

    /**
     * Export payout batch to CSV
     */
    public function export(PayoutBatch $payoutBatch)
    {
        $payoutBatch->load(['items.earner']);

        $filename = "payout_batch_{$payoutBatch->period}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payoutBatch) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Batch ID', 'Period', 'Status', 'Total Amount', 'Notes', 'Created At']);
            fputcsv($file, [
                $payoutBatch->id,
                $payoutBatch->period,
                $payoutBatch->status,
                $payoutBatch->total_amount,
                $payoutBatch->notes,
                $payoutBatch->created_at->format('Y-m-d H:i:s'),
            ]);
            
            fputcsv($file, []); // Empty row
            
            // Items headers
            fputcsv($file, ['Item ID', 'Earner ID', 'Earner Name', 'Earner Email', 'Amount', 'Status', 'Commission IDs', 'Created At']);
            
            // Items data
            foreach ($payoutBatch->items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->earner->id,
                    $item->earner->name,
                    $item->earner->email,
                    $item->amount,
                    $item->status,
                    implode(',', $item->commission_ids),
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
