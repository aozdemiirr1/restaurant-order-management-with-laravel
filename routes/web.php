<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderArchiveController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ExpenseController;

// Ana sayfa route'u login sayfasına yönlendirir
Route::redirect('/', '/admin/login');

// Admin Auth Routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Menu routes
    Route::resource('menus', MenuController::class);

    // Customer routes
    Route::resource('customers', CustomerController::class);

    // Order routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/archive', [OrderArchiveController::class, 'index'])->name('archive');
        Route::delete('/archive/bulk-delete', [OrderArchiveController::class, 'bulkDelete'])->name('archive.bulk-delete');
        Route::delete('/bulk-delete', [OrderController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/archive/{id}', [OrderArchiveController::class, 'show'])->name('archive.show');
        Route::delete('/archive/{id}', [OrderArchiveController::class, 'destroy'])->name('archive.destroy');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    });
    Route::resource('orders', OrderController::class);

    // Revenue routes
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');

    // Expense routes
    Route::resource('expenses', ExpenseController::class);

    // Settings routes
    Route::get('/settings', function() {
        return view('admin.settings.index');
    })->name('settings.index');
});
