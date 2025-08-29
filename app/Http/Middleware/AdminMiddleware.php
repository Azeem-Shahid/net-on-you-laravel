<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated as admin
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.'
                ], 401);
            }
            
            return redirect('/admin/login');
        }

        $admin = Auth::guard('admin')->user();

        // Check if admin is active
        if (!$admin->isActive()) {
            Auth::guard('admin')->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your admin account has been deactivated.'
                ], 403);
            }
            
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your admin account has been deactivated. Please contact support.']);
        }

        // Add admin to request for easy access in controllers
        $request->merge(['admin' => $admin]);

        return $next($request);
    }
}
