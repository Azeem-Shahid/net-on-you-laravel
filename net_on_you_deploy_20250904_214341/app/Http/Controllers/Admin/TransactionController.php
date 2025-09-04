<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_hash', 'like', "%{$search}%")
                  ->orWhere('gateway', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter by currency
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(25);

        // Get summary statistics
        $summary = $this->getTransactionSummary($request);

        // Log transaction listing access
        AdminActivityLog::log(
            auth()->id(),
            'view_transactions',
            'transaction_list',
            null,
            ['filters' => $request->all()]
        );

        return view('admin.transactions.index', compact('transactions', 'summary'));
    }

    /**
     * Show transaction details
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'commissions']);

        // Log transaction view
        AdminActivityLog::log(
            auth()->id(),
            'view_transaction',
            'transaction',
            $transaction->id,
            [
                'transaction_hash' => $transaction->transaction_hash,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency
            ]
        );

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Update transaction status
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled'
        ]);

        $oldStatus = $transaction->status;
        $transaction->update(['status' => $request->status]);

        // Log status update
        AdminActivityLog::log(
            auth()->id(),
            'update_transaction_status',
            'transaction',
            $transaction->id,
            [
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'transaction_hash' => $transaction->transaction_hash
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction status updated successfully'
        ]);
    }

    /**
     * Mark transaction as reviewed
     */
    public function markReviewed(Transaction $transaction)
    {
        $transaction->update(['reviewed_at' => now()]);

        // Log review
        AdminActivityLog::log(
            auth()->id(),
            'mark_transaction_reviewed',
            'transaction',
            $transaction->id,
            ['transaction_hash' => $transaction->transaction_hash]
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction marked as reviewed'
        ]);
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::with('user');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'User', 'Email', 'Amount', 'Currency', 'Gateway', 'Status',
                'Transaction Hash', 'Created At', 'Updated At'
            ]);

            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->user->name ?? 'N/A',
                    $transaction->user->email ?? 'N/A',
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->gateway,
                    $transaction->status,
                    $transaction->transaction_hash,
                    $transaction->created_at,
                    $transaction->updated_at
                ]);
            }

            fclose($file);
        };

        // Log export
        AdminActivityLog::log(
            auth()->id(),
            'export_transactions',
            'transaction_export',
            null,
            ['count' => $transactions->count(), 'filters' => $request->all()]
        );

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get transaction summary statistics
     */
    private function getTransactionSummary(Request $request): array
    {
        $query = Transaction::query();

        // Apply same filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalTransactions = $query->count();
        $totalAmount = $query->sum('amount');
        $completedTransactions = (clone $query)->where('status', 'completed')->count();
        $completedAmount = (clone $query)->where('status', 'completed')->sum('amount');
        $pendingTransactions = (clone $query)->where('status', 'pending')->count();
        $failedTransactions = (clone $query)->where('status', 'failed')->count();

        // Get gateway distribution
        $gatewayDistribution = (clone $query)
            ->selectRaw('gateway, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('gateway')
            ->get();

        // Get currency distribution
        $currencyDistribution = (clone $query)
            ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->get();

        return [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'completed_transactions' => $completedTransactions,
            'completed_amount' => $completedAmount,
            'pending_transactions' => $pendingTransactions,
            'failed_transactions' => $failedTransactions,
            'gateway_distribution' => $gatewayDistribution,
            'currency_distribution' => $currencyDistribution,
        ];
    }

    /**
     * Get transaction analytics data
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        
        $data = [
            'dailyTransactions' => $this->getDailyTransactions($period),
            'statusDistribution' => $this->getStatusDistribution(),
            'gatewayPerformance' => $this->getGatewayPerformance($period),
        ];

        return response()->json($data);
    }

    /**
     * Get daily transaction counts
     */
    private function getDailyTransactions(int $days): array
    {
        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $count = Transaction::whereDate('created_at', $date)->count();
            $amount = Transaction::whereDate('created_at', $date)->sum('amount');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
                'amount' => $amount,
            ];
        }

        return $data;
    }

    /**
     * Get transaction status distribution
     */
    private function getStatusDistribution(): array
    {
        return Transaction::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get gateway performance data
     */
    private function getGatewayPerformance(int $days): array
    {
        $startDate = now()->subDays($days);

        return Transaction::selectRaw('gateway, COUNT(*) as count, SUM(amount) as total_amount, AVG(amount) as avg_amount')
            ->where('created_at', '>=', $startDate)
            ->groupBy('gateway')
            ->get()
            ->toArray();
    }
}
