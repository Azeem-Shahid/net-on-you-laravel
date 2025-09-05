<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSession;
use App\Models\Admin;
use App\Models\SensitiveChangesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show sessions index page
     */
    public function index()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = AdminSession::with('admin')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        $admins = Admin::where('status', 'active')->get();

        return view('admin.sessions.index', compact('sessions', 'admins'));
    }

    /**
     * Get sessions for a specific admin
     */
    public function getAdminSessions(Request $request, $adminId)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = AdminSession::where('user_id', $adminId)
            ->with('admin')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        return response()->json(['sessions' => $sessions]);
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, AdminSession $session)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $session->revoke();

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'revoke_admin_session',
            $session->admin->email ?? 'Unknown',
            'active',
            'revoked',
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'revoke_admin_session',
            'admin_session',
            $session->id,
            [
                'admin_email' => $session->admin->email ?? 'Unknown',
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully'
        ]);
    }

    /**
     * Revoke all sessions for a specific admin
     */
    public function revokeAllSessions(Request $request, $adminId)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::findOrFail($adminId);
        $sessionCount = AdminSession::revokeAllForUser($adminId);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'revoke_all_admin_sessions',
            $admin->email,
            $sessionCount . ' active sessions',
            'all revoked',
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'revoke_all_admin_sessions',
            'admin_sessions',
            $adminId,
            [
                'admin_email' => $admin->email,
                'session_count' => $sessionCount,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "All sessions for {$admin->email} revoked successfully",
            'session_count' => $sessionCount
        ]);
    }

    /**
     * Revoke all sessions for all admins (maintenance mode)
     */
    public function revokeAllSessionsMaintenance(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'confirmation' => 'required|string|in:MAINTENANCE_MODE',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $totalSessions = AdminSession::where('is_revoked', false)->count();
        AdminSession::where('is_revoked', false)->update(['is_revoked' => true]);

        // Log sensitive change
        SensitiveChangesLog::log(
            auth('admin')->id(),
            'revoke_all_sessions_maintenance',
            'all_admins',
            $totalSessions . ' active sessions',
            'all revoked',
            $request->ip()
        );

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'revoke_all_sessions_maintenance',
            'admin_sessions',
            null,
            [
                'total_sessions' => $totalSessions,
                'reason' => $request->input('reason'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'All admin sessions revoked successfully',
            'session_count' => $totalSessions
        ]);
    }

    /**
     * Clean up expired sessions
     */
    public function cleanupExpired(Request $request)
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $maxAgeHours = $request->input('max_age_hours', 24);
        $deletedCount = AdminSession::cleanupExpired($maxAgeHours);

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'cleanup_expired_sessions',
            'admin_sessions',
            null,
            [
                'deleted_count' => $deletedCount,
                'max_age_hours' => $maxAgeHours,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} expired sessions cleaned up successfully"
        ]);
    }

    /**
     * Get session statistics
     */
    public function getStats()
    {
        // Check permission
        if (!auth('admin')->user()->hasPermission('sessions.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $stats = [
            'total_sessions' => AdminSession::count(),
            'active_sessions' => AdminSession::where('is_revoked', false)->count(),
            'revoked_sessions' => AdminSession::where('is_revoked', true)->count(),
            'sessions_today' => AdminSession::whereDate('created_at', today())->count(),
            'unique_users' => AdminSession::where('is_revoked', false)->distinct('user_id')->count(),
        ];

        return response()->json($stats);
    }
}
