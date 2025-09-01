<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
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
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\TranslationController as AdminTranslationController;
use App\Http\Controllers\Admin\CommandSchedulerController;
use App\Http\Controllers\Admin\CronJobController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are completely separate from user routes.
|
*/

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
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
        Route::post('/users/{user}/reset-balance', [UserController::class, 'resetBalance'])->name('users.reset-balance');
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
        
        // Payment Tracking & Notifications
        Route::prefix('payment-tracking')->name('payment-tracking.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PaymentTrackingController::class, 'index'])->name('index');
            Route::get('/{transaction}', [App\Http\Controllers\Admin\PaymentTrackingController::class, 'show'])->name('show');
            Route::post('/{transaction}/mark-reviewed', [App\Http\Controllers\Admin\PaymentTrackingController::class, 'markAsReviewed'])->name('mark-reviewed');
            Route::post('/{transaction}/send-confirmation', [App\Http\Controllers\Admin\PaymentTrackingController::class, 'sendPaymentConfirmation'])->name('send-confirmation');
            Route::post('/{transaction}/send-referrer-notification', [App\Http\Controllers\Admin\PaymentTrackingController::class, 'sendReferrerNotification'])->name('send-referrer-notification');
        });
        
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
        
        // Advanced Subscription Management
        Route::prefix('subscription-management')->name('subscription-management.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'index'])->name('index');
            Route::get('/create-admin-user', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'createAdminUser'])->name('create-admin-user');
            Route::post('/store-admin-user', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'storeAdminUser'])->name('store-admin-user');
            Route::get('/expiration-alerts', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'expirationAlerts'])->name('expiration-alerts');
            Route::post('/users/{user}/send-expiration-notification', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'sendExpirationNotification'])->name('send-expiration-notification');
            Route::post('/users/{user}/extend-subscription', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'extendSubscription'])->name('extend-subscription');
            Route::post('/users/{user}/send-payment-confirmation', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'sendPaymentConfirmation'])->name('send-payment-confirmation');
            Route::post('/users/{user}/send-referrer-notification', [App\Http\Controllers\Admin\SubscriptionManagementController::class, 'sendReferrerNotification'])->name('send-referrer-notification');
        });
        
        // Referral Management
        Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
        Route::get('/referrals/user/{user}', [ReferralController::class, 'show'])->name('referrals.show');
        Route::get('/referrals/export', [ReferralController::class, 'export'])->name('referrals.export');
        
        // Referral System Validation
        Route::prefix('referral-validation')->name('referral-validation.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReferralValidationController::class, 'index'])->name('index');
            Route::get('/user/{user}/referral-tree', [App\Http\Controllers\Admin\ReferralValidationController::class, 'showReferralTree'])->name('referral-tree');
            Route::post('/validate-system', [App\Http\Controllers\Admin\ReferralValidationController::class, 'validateReferralSystem'])->name('validate-system');
            Route::post('/test-system', [App\Http\Controllers\Admin\ReferralValidationController::class, 'testReferralSystem'])->name('test-system');
        });
        
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
        
        // Advanced Commission Management
        Route::prefix('commission-management')->name('commission-management.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CommissionManagementController::class, 'index'])->name('index');
            Route::get('/monthly-breakdown', [App\Http\Controllers\Admin\CommissionManagementController::class, 'monthlyBreakdown'])->name('monthly-breakdown');
            Route::post('/process-eligibility', [App\Http\Controllers\Admin\CommissionManagementController::class, 'processMonthlyEligibility'])->name('process-eligibility');
            Route::post('/create-payout-batch', [App\Http\Controllers\Admin\CommissionManagementController::class, 'createPayoutBatch'])->name('create-payout-batch');
            Route::post('/payout-items/{payoutItem}/mark-sent', [App\Http\Controllers\Admin\CommissionManagementController::class, 'markPayoutSent'])->name('mark-payout-sent');
            Route::post('/payout-items/{payoutItem}/mark-paid', [App\Http\Controllers\Admin\CommissionManagementController::class, 'markPayoutPaid'])->name('mark-payout-paid');
        });
        
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
        Route::resource('languages', LanguageController::class)->names([
            'index' => 'languages.index',
            'create' => 'languages.create',
            'store' => 'languages.store',
            'edit' => 'languages.edit',
            'update' => 'languages.update',
            'destroy' => 'languages.destroy',
        ]);
        Route::post('/languages/{language}/set-default', [LanguageController::class, 'setDefault'])->name('languages.set-default');
        Route::post('/languages/{language}/toggle-status', [LanguageController::class, 'toggleStatus'])->name('languages.toggle-status');
        
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
        
        // Contract Management
        Route::resource('contracts', App\Http\Controllers\Admin\ContractController::class)->names([
            'index' => 'contracts.index',
            'create' => 'contracts.create',
            'store' => 'contracts.store',
            'show' => 'contracts.show',
            'edit' => 'contracts.edit',
            'update' => 'contracts.update',
            'destroy' => 'contracts.destroy',
        ]);
        Route::post('/contracts/{contract}/toggle-status', [App\Http\Controllers\Admin\ContractController::class, 'toggleStatus'])->name('contracts.toggle-status');
        Route::post('/contracts/import', [App\Http\Controllers\Admin\ContractController::class, 'import'])->name('contracts.import');
        Route::get('/contracts/{contract}/export', [App\Http\Controllers\Admin\ContractController::class, 'export'])->name('contracts.export');
        
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
        
        // Command Scheduler Management
        Route::prefix('command-scheduler')->name('command-scheduler.')->group(function () {
            Route::get('/', [CommandSchedulerController::class, 'index'])->name('index');
            Route::get('/scheduled-commands', [CommandSchedulerController::class, 'getScheduledCommands'])->name('scheduled-commands');
            Route::post('/run-command', [CommandSchedulerController::class, 'runCommand'])->name('run-command');
            Route::post('/run-multiple-commands', [CommandSchedulerController::class, 'runMultipleCommands'])->name('run-multiple-commands');
            Route::get('/command-groups', [CommandSchedulerController::class, 'getCommandGroups'])->name('command-groups');
            Route::post('/schedule-command', [CommandSchedulerController::class, 'scheduleCommand'])->name('schedule-command');
            Route::get('/logs', [CommandSchedulerController::class, 'getLogs'])->name('logs');
            Route::post('/clear-logs', [CommandSchedulerController::class, 'clearLogs'])->name('clear-logs');
            Route::get('/stats', [CommandSchedulerController::class, 'getStats'])->name('stats');
            Route::get('/export-logs', [CommandSchedulerController::class, 'exportLogs'])->name('export-logs');
            Route::post('/commands/{scheduledCommand}/toggle-status', [CommandSchedulerController::class, 'toggleCommandStatus'])->name('toggle-command-status');
            Route::delete('/commands/{scheduledCommand}', [CommandSchedulerController::class, 'deleteCommand'])->name('delete-command');
        });

        // Cron Job Management Routes
        Route::prefix('cron-jobs')->name('cron-jobs.')->group(function () {
            Route::get('/', [CronJobController::class, 'index'])->name('index');
            Route::get('/setup-guide', [CronJobController::class, 'getSetupGuide'])->name('setup-guide');
            Route::post('/run-business-command', [CronJobController::class, 'runBusinessCommand'])->name('run-business-command');
            Route::get('/command-history', [CronJobController::class, 'getCommandHistory'])->name('command-history');
            Route::get('/status', [CronJobController::class, 'getStatus'])->name('status');
        });
    });
});

