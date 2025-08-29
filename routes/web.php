<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MagazineController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\TranslationController as AdminTranslationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController as RegularUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Language switching (public route)
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/current', [LanguageController::class, 'current'])->name('language.current');

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
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Protected User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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


// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
    });

    // Protected Admin Routes
    Route::middleware(['auth:admin', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
        
        // Analytics & Reports
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
            Route::post('/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('export');
            Route::get('/kpis', [App\Http\Controllers\Admin\AnalyticsController::class, 'getKPIsApi'])->name('kpis');
            Route::get('/chart-data', [App\Http\Controllers\Admin\AnalyticsController::class, 'getChartData'])->name('chart-data');
        });
        
        // User Management
        Route::resource('users', UserController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
        ]);
        Route::post('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggle-block');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        
        // Magazine Management
        Route::resource('magazines', MagazineController::class)->names([
            'index' => 'magazines.index',
            'create' => 'magazines.create',
            'store' => 'magazines.store',
            'show' => 'magazines.show',
            'edit' => 'magazines.edit',
            'update' => 'magazines.update',
            'destroy' => 'magazines.destroy',
        ]);
        Route::post('/magazines/{magazine}/toggle-status', [MagazineController::class, 'toggleStatus'])->name('magazines.toggle-status');
        Route::post('/magazines/bulk-update', [MagazineController::class, 'bulkUpdate'])->name('magazines.bulk-update');
        Route::get('/magazines/{magazine}/download', [MagazineController::class, 'download'])->name('magazines.download');
        Route::post('/magazines/{magazine}/upload-version', [MagazineController::class, 'uploadVersion'])->name('magazines.upload-version');
        
        // Transaction Management
        Route::resource('transactions', TransactionController::class)->names([
            'index' => 'transactions.index',
            'show' => 'transactions.show',
        ]);
        Route::put('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::post('/transactions/{transaction}/mark-reviewed', [TransactionController::class, 'markReviewed'])->name('transactions.mark-reviewed');
        Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
        Route::get('/transactions/analytics', [TransactionController::class, 'analytics'])->name('transactions.analytics');
        
        // Subscription Management
        Route::resource('subscriptions', SubscriptionController::class)->names([
            'index' => 'subscriptions.index',
            'create' => 'subscriptions.create',
            'store' => 'subscriptions.store',
            'show' => 'subscriptions.show',
            'edit' => 'subscriptions.edit',
            'update' => 'subscriptions.update',
        ]);
        Route::post('/subscriptions/{subscription}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');
        Route::post('/subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/bulk-update', [SubscriptionController::class, 'bulkUpdate'])->name('subscriptions.bulk-update');
        Route::get('/subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');
        Route::get('/subscriptions/analytics', [SubscriptionController::class, 'analytics'])->name('subscriptions.analytics');
        
        // Referral Management
        Route::resource('referrals', ReferralController::class)->names([
            'index' => 'referrals.index',
            'show' => 'referrals.show',
        ]);
        Route::get('/referrals/export', [ReferralController::class, 'export'])->name('referrals.export');
        
        // Commission Management
        Route::resource('commissions', CommissionController::class)->names([
            'index' => 'commissions.index',
            'show' => 'commissions.show',
        ]);
        Route::post('/commissions/{commission}/adjust', [CommissionController::class, 'adjust'])->name('commissions.adjust');
        Route::post('/commissions/{commission}/void', [CommissionController::class, 'void'])->name('commissions.void');
        Route::post('/commissions/{commission}/restore', [CommissionController::class, 'restore'])->name('commissions.restore');
        Route::post('/commissions/create-payout-batch', [CommissionController::class, 'createPayoutBatch'])->name('commissions.create-payout-batch');
        Route::get('/commissions/export', [CommissionController::class, 'export'])->name('commissions.export');
        
        // Payout Management
        Route::resource('payouts', PayoutController::class)->names([
            'index' => 'payouts.index',
            'show' => 'payouts.show',
        ]);
        Route::post('/payouts/{payoutBatch}/start-processing', [PayoutController::class, 'startProcessing'])->name('payouts.start-processing');
        Route::post('/payouts/items/{payoutBatchItem}/mark-sent', [PayoutController::class, 'markAsSent'])->name('payouts.mark-sent');
        Route::post('/payouts/items/{payoutBatchItem}/mark-paid', [PayoutController::class, 'markAsPaid'])->name('payouts.mark-paid');
        Route::post('/payouts/items/{payoutBatchItem}/mark-failed', [PayoutController::class, 'markAsFailed'])->name('payouts.mark-failed');
        Route::post('/payouts/{payoutBatch}/close', [PayoutController::class, 'close'])->name('payouts.close');
        Route::get('/payouts/{payoutBatch}/export', [PayoutController::class, 'export'])->name('payouts.export');
        
        // Email & Notification Management
        Route::resource('email-templates', EmailTemplateController::class)->names([
            'index' => 'email-templates.index',
            'create' => 'email-templates.create',
            'store' => 'email-templates.store',
            'show' => 'email-templates.show',
            'edit' => 'email-templates.edit',
            'update' => 'email-templates.update',
            'destroy' => 'email-templates.destroy',
        ]);
        Route::post('/email-templates/{emailTemplate}/send-test', [EmailTemplateController::class, 'sendTest'])->name('email-templates.send-test');
        Route::post('/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
        
        Route::resource('email-logs', EmailLogController::class)->names([
            'index' => 'email-logs.index',
            'show' => 'email-logs.show',
        ]);
        Route::post('/email-logs/{emailLog}/retry', [EmailLogController::class, 'retry'])->name('email-logs.retry');
        Route::get('/email-logs/export', [EmailLogController::class, 'export'])->name('email-logs.export');
        Route::post('/email-logs/clear-old', [EmailLogController::class, 'clearOldLogs'])->name('email-logs.clear-old');
        
        Route::resource('campaigns', CampaignController::class)->names([
            'index' => 'campaigns.index',
            'create' => 'campaigns.create',
            'store' => 'campaigns.store',
        ]);
        Route::post('/campaigns/preview', [CampaignController::class, 'preview'])->name('campaigns.preview');
        Route::post('/campaigns/recipient-count', [CampaignController::class, 'getRecipientCount'])->name('campaigns.recipient-count');
        Route::get('/campaigns/user-stats', [CampaignController::class, 'getUserStats'])->name('campaigns.user-stats');
        
        // Language Management
        Route::resource('languages', AdminLanguageController::class)->names([
            'index' => 'languages.index',
            'create' => 'languages.create',
            'store' => 'languages.store',
            'edit' => 'languages.edit',
            'update' => 'languages.update',
            'destroy' => 'languages.destroy',
        ]);
        Route::post('/languages/{language}/set-default', [AdminLanguageController::class, 'setDefault'])->name('languages.set-default');
        Route::post('/languages/{language}/toggle-status', [AdminLanguageController::class, 'toggleStatus'])->name('languages.toggle-status');
        
        // Translation Management
        Route::resource('translations', AdminTranslationController::class)->names([
            'index' => 'translations.index',
            'create' => 'translations.create',
            'store' => 'translations.store',
            'edit' => 'translations.edit',
            'update' => 'translations.update',
            'destroy' => 'translations.destroy',
        ]);
        Route::post('/translations/bulk-import', [AdminTranslationController::class, 'bulkImport'])->name('translations.bulk-import');
        Route::get('/translations/export', [AdminTranslationController::class, 'export'])->name('translations.export');
        
        // Settings Management
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::put('/{key}', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
            Route::post('/update-multiple', [App\Http\Controllers\Admin\SettingsController::class, 'updateMultiple'])->name('update-multiple');
            Route::get('/{key}/value', [App\Http\Controllers\Admin\SettingsController::class, 'getValue'])->name('get-value');
            Route::post('/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
        });
        
        // Security Management
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SecurityController::class, 'index'])->name('index');
            Route::put('/{policyName}', [App\Http\Controllers\Admin\SecurityController::class, 'update'])->name('update');
            Route::post('/update-multiple', [App\Http\Controllers\Admin\SecurityController::class, 'updateMultiple'])->name('update-multiple');
            Route::get('/{policyName}/value', [App\Http\Controllers\Admin\SecurityController::class, 'getPolicyValue'])->name('get-policy-value');
        });
        
        // Role Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'show'])->name('show');
            Route::put('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
            Route::get('/permissions', [App\Http\Controllers\Admin\RoleController::class, 'getPermissions'])->name('permissions');
        });
        
        // API Key Management
        Route::prefix('api-keys')->name('api-keys.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->name('store');
            Route::put('/{apiKey}', [App\Http\Controllers\Admin\ApiKeyController::class, 'update'])->name('update');
            Route::delete('/{apiKey}', [App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->name('destroy');
            Route::post('/{apiKey}/toggle-status', [App\Http\Controllers\Admin\ApiKeyController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{apiKey}/regenerate', [App\Http\Controllers\Admin\ApiKeyController::class, 'regenerate'])->name('regenerate');
        });
        
        // Session Management
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SessionController::class, 'index'])->name('index');
            Route::get('/admin/{adminId}', [App\Http\Controllers\Admin\SessionController::class, 'getAdminSessions'])->name('admin-sessions');
            Route::post('/{session}/revoke', [App\Http\Controllers\Admin\SessionController::class, 'revokeSession'])->name('revoke');
            Route::post('/admin/{adminId}/revoke-all', [App\Http\Controllers\Admin\SessionController::class, 'revokeAllSessions'])->name('revoke-all-admin');
            Route::post('/revoke-all-maintenance', [App\Http\Controllers\Admin\SessionController::class, 'revokeAllSessionsMaintenance'])->name('revoke-all-maintenance');
            Route::post('/cleanup-expired', [App\Http\Controllers\Admin\SessionController::class, 'cleanupExpired'])->name('cleanup-expired');
            Route::get('/stats', [App\Http\Controllers\Admin\SessionController::class, 'getStats'])->name('stats');
        });
    });
});
