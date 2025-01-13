<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Menu Routes
    Route::get('/admin/menus', [MenuController::class, 'index'])->name('admin.menus.index');
    Route::get('/admin/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
    Route::post('/admin/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::get('/admin/menus/{menu}/edit', [MenuController::class, 'edit'])->name('admin.menus.edit');
    Route::put('/admin/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/admin/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');

    // Customer Routes
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/admin/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // Order Routes
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/admin/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::delete('/admin/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
});
