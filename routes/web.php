<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderArchiveController;

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
    Route::prefix('orders')->group(function () {
        // Archive routes - must be defined BEFORE the resource routes
        Route::prefix('archive')->name('orders.archive.')->group(function () {
            Route::get('/', [OrderArchiveController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderArchiveController::class, 'show'])->name('show');
            Route::delete('/{id}', [OrderArchiveController::class, 'destroy'])->name('destroy');
            Route::delete('/', [OrderArchiveController::class, 'bulkDelete'])->name('bulk-delete');
        });

        // Regular order routes
        Route::delete('/bulk-delete', [OrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    Route::resource('orders', OrderController::class);
});
