<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('login');
    }

    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        // Check if admin is already logged in
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/dashboard');
        }
        
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        // Check if admin is already logged in
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/dashboard');
        }

        // Debug: Log the request data
        \Log::info('Admin login attempt', [
            'email' => $request->email,
            'has_password' => $request->has('password'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::info('Admin login validation failed', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Debug: Check if admin exists
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin) {
            \Log::info('Admin found', [
                'id' => $admin->id,
                'name' => $admin->name,
                'status' => $admin->status
            ]);
        } else {
            \Log::info('Admin not found for email: ' . $credentials['email']);
        }

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $admin = Auth::guard('admin')->user();
            \Log::info('Admin authentication successful', ['id' => $admin->id]);

            if (!$admin->isActive()) {
                Auth::guard('admin')->logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Your admin account has been blocked. Please contact support.']);
            }

            $request->session()->regenerate();

            // Update last login information
            $admin->updateLastLogin($request->ip());

            // Log admin login
            AdminActivityLog::log($admin->id, 'admin_login', 'admin_login', null, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Force redirect to admin dashboard
            return redirect('/admin/dashboard');
        }

        \Log::info('Admin authentication failed for email: ' . $credentials['email']);
        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput();
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        if ($admin) {
            // Log admin logout
            AdminActivityLog::log($admin->id, 'admin_logout', 'admin_logout', null, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
