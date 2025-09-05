<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminActivityLog;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('throttle:6,1')->only('login');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        try {
            // Log registration attempt
            \Log::info('Registration attempt', [
                'email' => $request->email,
                'name' => $request->name,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:150',
                'email' => 'required|string|email|max:191|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'wallet_address' => 'nullable|string|max:191',
                'language' => 'nullable|string|max:10',
                'referrer_id' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                \Log::info('Registration validation failed', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check database connection
            if (!DB::connection()->getPdo()) {
                \Log::error('Database connection failed during registration');
                return redirect()->back()
                    ->withErrors(['email' => 'Database connection error. Please try again.'])
                    ->withInput();
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'wallet_address' => $request->wallet_address,
                'language' => $request->language ?? 'en',
                'referrer_id' => $request->referrer_id,
                'role' => 'user',
                'status' => 'active',
            ]);

            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Build referral upline if referrer exists
            if ($request->referrer_id) {
                $referrer = User::find($request->referrer_id);
                if ($referrer) {
                    $referralService = app(ReferralService::class);
                    $referralService->buildReferralUpline($user, $referrer);
                }
            }

            event(new Registered($user));

            Auth::login($user);

            \Log::info('User logged in successfully', ['user_id' => $user->id]);

            return redirect()->route('verification.notice');
        } catch (\Exception $e) {
            \Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors(['email' => 'Registration failed. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Your account has been blocked. Please contact support.']);
            }

            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Please verify your email address before logging in.']);
            }

            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput();
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
