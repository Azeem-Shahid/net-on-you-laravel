<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Test routes that bypass email verification
Route::middleware(['auth'])->group(function () {
    Route::get('/test-dashboard', [DashboardController::class, 'index'])->name('test.dashboard');
    Route::get('/test-profile/edit', [DashboardController::class, 'editProfile'])->name('test.profile.edit');
    Route::put('/test-profile/update', [DashboardController::class, 'updateProfile'])->name('test.profile.update');
    Route::get('/test-profile/change-password', [DashboardController::class, 'showChangePassword'])->name('test.profile.change-password');
    Route::put('/test-profile/change-password', [DashboardController::class, 'changePassword'])->name('test.profile.change-password.update');
});

