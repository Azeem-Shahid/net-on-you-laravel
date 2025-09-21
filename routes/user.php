<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController as RegularUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user routes for your application.
| These routes are completely separate from admin routes.
|
*/

// Public routes - Redirect to login for unauthenticated users
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/home', function () {
    return redirect()->route('login');
})->name('homepage');

// Language switching (public route)
Route::post('/language/switch', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/current', [App\Http\Controllers\LanguageController::class, 'current'])->name('language.current');

// User Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
    
    // Short referral link route
    Route::get('/ref/{referralCode}', [AuthController::class, 'showRegistrationForm'])->name('register.ref');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showEmailVerificationNotice'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resendEmailVerification'])->middleware(['throttle:6,1'])->name('verification.send');
    
    // Logout route - accessible to all authenticated users (verified or not)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/change-password', [DashboardController::class, 'showChangePassword'])->name('profile.change-password');
    Route::put('/profile/change-password', [DashboardController::class, 'changePassword'])->name('profile.change-password.update');
    
    // User Language Routes
    Route::post('/user/language', [RegularUserController::class, 'updateLanguage'])->name('user.language.update');
    Route::get('/user/language', [RegularUserController::class, 'getLanguage'])->name('user.language.get');
    
    // Magazine Routes (for subscribers)
    Route::get('/magazines', [App\Http\Controllers\MagazineController::class, 'index'])->name('magazines.index');
    Route::get('/magazines/{magazine}', [App\Http\Controllers\MagazineController::class, 'show'])->name('magazines.show');
    Route::get('/magazines/{magazine}/download', [App\Http\Controllers\MagazineController::class, 'download'])->name('magazines.download');
    Route::get('/magazines/access/status', [App\Http\Controllers\MagazineController::class, 'accessStatus'])->name('magazines.access-status');
    
    // Payment Routes
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/status/{transaction}', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/payment/manual/{transaction}', [PaymentController::class, 'manual'])->name('payment.manual');
    Route::post('/payment/upload-proof/{transaction}', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
    Route::get('/payment/history', [PaymentController::class, 'history'])->name('payment.history');
    
    // User Transaction Routes
    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
    
    // Referral Routes
    Route::get('/referrals', [App\Http\Controllers\ReferralController::class, 'index'])->name('referrals.index');
    Route::get('/referrals/details', [App\Http\Controllers\ReferralController::class, 'details'])->name('referrals.details');
    
    // CoinPayments Routes
    Route::post('/payments/coinpayments/create', [PaymentController::class, 'createCoinPayments'])->name('coinpayments.create');
});

// Public Magazine API Routes
Route::get('/api/magazines/categories', [App\Http\Controllers\MagazineController::class, 'categories'])->name('api.magazines.categories');
Route::get('/api/magazines/languages', [App\Http\Controllers\MagazineController::class, 'languages'])->name('api.magazines.languages');

// Contract Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/contract', [App\Http\Controllers\ContractController::class, 'show'])->name('contract.show');
    Route::post('/contract/accept', [App\Http\Controllers\ContractController::class, 'accept'])->name('contract.accept');
    Route::get('/contract/status', [App\Http\Controllers\ContractController::class, 'status'])->name('contract.status');
});

// Payment Webhook (public route)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// CoinPayments Routes
Route::post('/payments/coinpayments/ipn', [PaymentController::class, 'coinPaymentsIPN'])->name('coinpayments.ipn');

// Additional Public Routes for Footer
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/help', function () {
    return view('pages.help');
})->name('help');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/support', function () {
    return view('pages.support');
})->name('support');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/cookies', function () {
    return view('pages.cookies');
})->name('cookies');
