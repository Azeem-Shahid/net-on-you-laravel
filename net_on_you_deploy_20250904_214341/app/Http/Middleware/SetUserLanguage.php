<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetUserLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user has a language preference
            if (isset($user->language) && $user->language) {
                // Set the application locale to user's preferred language
                App::setLocale($user->language);
                Session::put('locale', $user->language);
                
                // Also set for admin users
                if (auth()->guard('admin')->check()) {
                    $admin = auth()->guard('admin')->user();
                    if (isset($admin->language) && $admin->language) {
                        App::setLocale($admin->language);
                        Session::put('locale', $admin->language);
                    }
                }
            }
        }
        
        // Check if admin is logged in
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
            
            // Check if admin has a language preference
            if (isset($admin->language) && $admin->language) {
                // Set the application locale to admin's preferred language
                App::setLocale($admin->language);
                Session::put('locale', $admin->language);
            }
        }

        return $next($request);
    }
}

