<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FuelConsumptionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleUsageController;
use App\Http\Controllers\VehicleServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Support both "/" and "/login" for GET to avoid "405 Method Not Allowed"
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.page');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('bookings', BookingController::class);
        Route::post('/bookings/{booking}/submit', [BookingController::class, 'submit'])->name('bookings.submit');

        Route::resource('vehicles', VehicleController::class);
        Route::resource('vehicle-services', VehicleServiceController::class);
        Route::resource('drivers', DriverController::class);
        Route::resource('users', UserController::class);
        Route::resource('fuel-consumptions', FuelConsumptionController::class);
        Route::resource('vehicle-usages', VehicleUsageController::class);

        Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

        Route::get('/exports/bookings', [ExportController::class, 'bookings'])->name('exports.bookings');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::middleware('role:approver_l1,approver_l2')->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });
});
