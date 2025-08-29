<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    /**
     * Display a listing of email logs
     */
    public function index(Request $request)
    {
        $query = EmailLog::with(['user', 'template']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by template
        if ($request->filled('template')) {
            $query->where('template_name', $request->template);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')
                     ->paginate(20);

        // Get filter options
        $statuses = ['sent', 'failed', 'queued'];
        $templates = EmailTemplate::distinct()->pluck('name')->sort();
        $users = User::select('id', 'name', 'email')
                    ->orderBy('name')
                    ->get();

        // Get statistics
        $stats = $this->getEmailStats($request);

        return view('admin.email-logs.index', compact('logs', 'statuses', 'templates', 'users', 'stats'));
    }

    /**
     * Display the specified email log
     */
    public function show(EmailLog $emailLog)
    {
        $emailLog->load(['user', 'template']);
        
        return view('admin.email-logs.show', compact('emailLog'));
    }

    /**
     * Retry failed email
     */
    public function retry(EmailLog $emailLog)
    {
        if ($emailLog->status !== 'failed') {
            return back()->with('error', 'Only failed emails can be retried.');
        }

        try {
            // Get the template
            $template = EmailTemplate::where('name', $emailLog->template_name)
                ->where('language', 'en') // Default to English for retry
                ->first();

            if (!$template) {
                return back()->with('error', 'Template not found for retry.');
            }

            // Update status to queued
            $emailLog->update([
                'status' => 'queued',
                'error_message' => null
            ]);

            // Send email using the service
            $emailService = app(\App\Services\EmailService::class);
            $result = $emailService->sendTemplateEmail(
                $emailLog->template_name,
                $emailLog->user_id,
                [], // No additional data for retry
                'en'
            );

            if ($result) {
                return back()->with('success', 'Email queued for retry.');
            } else {
                $emailLog->update(['status' => 'failed']);
                return back()->with('error', 'Failed to retry email.');
            }

        } catch (Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return back()->with('error', 'Error retrying email: ' . $e->getMessage());
        }
    }

    /**
     * Export email logs
     */
    public function export(Request $request)
    {
        $query = EmailLog::with(['user', 'template']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('template')) {
            $query->where('template_name', $request->template);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'email_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Template', 'User', 'Email', 'Subject', 'Status', 
                'Sent At', 'Created At', 'Error Message'
            ]);

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->template_name,
                    $log->user ? $log->user->name : 'N/A',
                    $log->email,
                    $log->subject,
                    $log->status,
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : 'N/A',
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->error_message ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get email statistics
     */
    private function getEmailStats(Request $request)
    {
        $query = EmailLog::query();

        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $stats = [
            'total' => $query->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'queued' => (clone $query)->where('status', 'queued')->count(),
        ];

        // Get daily stats for the last 30 days
        $dailyStats = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $stats['daily'] = $dailyStats;

        return $stats;
    }

    /**
     * Clear old email logs
     */
    public function clearOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = EmailLog::where('created_at', '<', $cutoffDate)->delete();

        return back()->with('success', "Deleted {$deletedCount} email logs older than {$request->days} days.");
    }
}
