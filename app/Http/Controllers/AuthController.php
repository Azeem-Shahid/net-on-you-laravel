<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminActivityLog;
use App\Services\ReferralService;
use App\Services\FallbackEmailService;
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
        $this->middleware('guest')->except(['logout', 'showEmailVerificationNotice', 'verifyEmail', 'resendEmailVerification']);
        $this->middleware('throttle:6,1')->only('login');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->get('ref') ?? $request->route('referralCode');
        $referrer = null;
        
        // Validate referral code if provided
        if ($referralCode) {
            // Try new format first, then legacy format
            $referrer = User::findByReferralCode($referralCode);
            if (!$referrer) {
                $referrer = User::findByReferralCodeLegacy($referralCode);
            }
        }
        
        return view('auth.register', [
            'referralCode' => $referralCode,
            'referrer' => $referrer,
            'isValidReferralCode' => $referrer !== null
        ]);
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

        // Determine if referral code is required based on referral link
        $isReferralLink = $request->has('ref') || $request->filled('referral_code');
        
        // Find referrer by referral code if provided
        $referrer = null;
        if ($request->filled('referral_code')) {
            // Try new format first, then legacy format
            $referrer = User::findByReferralCode($request->referral_code);
            if (!$referrer) {
                $referrer = User::findByReferralCodeLegacy($request->referral_code);
            }
        } elseif ($request->has('ref')) {
            // Try new format first, then legacy format
            $referrer = User::findByReferralCode($request->ref);
            if (!$referrer) {
                $referrer = User::findByReferralCodeLegacy($request->ref);
            }
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'wallet_address' => 'nullable|string|max:191',
            'language' => 'nullable|string|max:10',
            'referral_code' => $isReferralLink ? 'required|string' : 'nullable|string',
        ], [
            'referral_code.required' => 'Referral code is required when using a referral link.',
            'referral_code.exists' => 'The provided referral code is invalid or does not exist.',
        ]);

            if ($validator->fails()) {
                \Log::info('Registration validation failed', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Custom validation for referral code
            if ($isReferralLink && !$referrer) {
                return redirect()->back()
                    ->withErrors(['referral_code' => 'The provided referral code is invalid or does not exist.'])
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
                'referrer_id' => $referrer ? $referrer->id : null,
                'role' => 'user',
                'status' => 'active',
            ]);

            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Send welcome email
            try {
                $emailTriggerService = app(\App\Services\EmailTriggerService::class);
                $emailTriggerService->sendWelcomeEmail($user);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Build referral upline if referrer exists
            if ($referrer) {
                $referralService = app(ReferralService::class);
                $referralService->buildReferralUpline($user, $referrer);
            }

            \Log::info('=== REGISTRATION EMAIL VERIFICATION START ===', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()
            ]);

            // Check email configuration before sending
            $mailConfig = config('mail');
            \Log::info('Email configuration during registration', [
                'driver' => $mailConfig['default'],
                'host' => $mailConfig['mailers']['smtp']['host'],
                'port' => $mailConfig['mailers']['smtp']['port'],
                'username' => $mailConfig['mailers']['smtp']['username'],
                'from_address' => $mailConfig['from']['address'],
                'from_name' => $mailConfig['from']['name']
            ]);

            try {
                event(new Registered($user));
                \Log::info('Registered event fired successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Registered event failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            Auth::login($user);

            \Log::info('User logged in successfully', ['user_id' => $user->id]);

            \Log::info('=== REGISTRATION EMAIL VERIFICATION COMPLETE ===', [
                'user_id' => $user->id,
                'email' => $user->email,
                'redirect_to' => 'verification.notice'
            ]);

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
        try {
            \Log::info("Password reset request", [
                "email" => $request->email,
                "ip" => $request->ip(),
                "user_agent" => $request->userAgent()
            ]);

            $request->validate(["email" => "required|email"]);

            // Check if user exists
            $user = User::where("email", $request->email)->first();
            if (!$user) {
                \Log::warning("Password reset requested for non-existent email", [
                    "email" => $request->email,
                    "ip" => $request->ip()
                ]);
                return back()->withErrors(["email" => "We can't find a user with that email address."]);
            }

            \Log::info("Sending password reset link", [
                "user_id" => $user->id,
                "email" => $user->email
            ]);

            // Try Laravel's built-in password reset first
            $status = Password::sendResetLink($request->only("email"));

            if ($status === Password::RESET_LINK_SENT) {
                \Log::info("Password reset link sent successfully", [
                    "status" => $status,
                    "email" => $request->email
                ]);
                return back()->with(["status" => __($status)]);
            }

            // If Laravel's method fails, try fallback
            \Log::warning("Laravel password reset failed, trying fallback", [
                "status" => $status,
                "email" => $request->email
            ]);

            $fallbackService = app(FallbackEmailService::class);
            $fallbackResult = $fallbackService->sendPasswordResetEmail($user, "fallback-token-" . time());

            if ($fallbackResult) {
                \Log::info("Fallback email sent successfully", [
                    "email" => $request->email
                ]);
                return back()->with(["status" => "Password reset link sent!"]);
            }

            \Log::error("All email methods failed", [
                "email" => $request->email,
                "laravel_status" => $status
            ]);

            return back()->withErrors(["email" => "Unable to send password reset link. Please try again."]);

        } catch (\Exception $e) {
            \Log::error("Password reset link failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "email" => $request->email,
                "ip" => $request->ip()
            ]);

            return back()->withErrors(["email" => "Unable to send password reset link. Please try again."]);
        }
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
        try {
            \Log::info('Password reset attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    \Log::info('Resetting password for user', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();

                    \Log::info('Password reset successful', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                }
            );

            \Log::info('Password reset status', [
                'status' => $status,
                'email' => $request->email
            ]);

            return $status === Password::PASSWORD_RESET
                        ? redirect()->route('login')->with('status', __($status))
                        : back()->withErrors(['email' => [__($status)]]);
        } catch (\Exception $e) {
            \Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email' => 'Unable to reset password. Please try again.']);
        }
    }

    /**
     * Show email verification notice
     */
    public function showEmailVerificationNotice()
    {
        \Log::info('Email verification notice shown', [
            'user_id' => Auth::id(),
            'ip' => request()->ip()
        ]);

        return view('auth.verify-email');
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(\Illuminate\Foundation\Auth\EmailVerificationRequest $request)
    {
        try {
            \Log::info('Email verification attempt', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip' => $request->ip()
            ]);

            $request->fulfill();

            \Log::info('Email verification successful', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email
            ]);

            return redirect()->route('dashboard')->with('status', 'Email verified successfully!');
        } catch (\Exception $e) {
            \Log::error('Email verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email' => 'Unable to verify email. Please try again.']);
        }
    }

    /**
     * Resend email verification
     */
    public function resendEmailVerification(Request $request)
    {
        try {
            \Log::info('=== RESEND EMAIL VERIFICATION START ===', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            if ($request->user()->hasVerifiedEmail()) {
                \Log::info('User already verified - redirecting to dashboard', [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email
                ]);
                return redirect()->route('dashboard');
            }

            \Log::info('Attempting to send email verification notification', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'user_verified' => $request->user()->hasVerifiedEmail()
            ]);

            // Check email configuration before sending
            $mailConfig = config('mail');
            \Log::info('Email configuration check', [
                'driver' => $mailConfig['default'],
                'host' => $mailConfig['mailers']['smtp']['host'],
                'port' => $mailConfig['mailers']['smtp']['port'],
                'username' => $mailConfig['mailers']['smtp']['username'],
                'from_address' => $mailConfig['from']['address'],
                'from_name' => $mailConfig['from']['name']
            ]);

            $request->user()->sendEmailVerificationNotification();

            \Log::info('Email verification notification sent successfully', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'timestamp' => now()
            ]);

            return back()->with('status', 'Verification link sent!');
        } catch (\Exception $e) {
            \Log::error('=== RESEND EMAIL VERIFICATION FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            return back()->withErrors(['email' => 'Unable to resend verification email. Please try again.']);
        }
    }
}