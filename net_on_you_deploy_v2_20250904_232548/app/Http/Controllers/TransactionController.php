<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's transactions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->transactions();
        
        // Apply filters
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
        
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        // Get unique values for filter dropdowns
        $statuses = $user->transactions()->distinct()->pluck('status')->filter();
        $gateways = $user->transactions()->distinct()->pluck('gateway')->filter();
        $currencies = $user->transactions()->distinct()->pluck('currency')->filter();
        
        // Get summary statistics
        $totalTransactions = $user->transactions()->count();
        $completedTransactions = $user->transactions()->where('status', 'completed')->count();
        $pendingTransactions = $user->transactions()->where('status', 'pending')->count();
        $failedTransactions = $user->transactions()->where('status', 'failed')->count();
        $totalAmount = $user->transactions()->where('status', 'completed')->sum('amount');
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('transactions.index', compact(
            'transactions',
            'statuses',
            'gateways', 
            'currencies',
            'totalTransactions',
            'completedTransactions',
            'pendingTransactions',
            'failedTransactions',
            'totalAmount'
        ));
    }

    /**
     * Display the specified transaction
     */
    public function show(Transaction $transaction)
    {
        // Ensure user can only view their own transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('transactions.show', compact('transaction'));
    }
}

