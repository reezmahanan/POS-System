<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Redirect welcome page to login or dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes (Require login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholders for other routes (to be completed in later steps)
    // POS Terminal
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{sale}', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');
    // Categories
    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
    Route::middleware('admin')->group(function () {
        Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    // Products
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::middleware('admin')->group(function () {
        Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    });
    // Sales History
    Route::get('/sales', [\App\Http\Controllers\SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [\App\Http\Controllers\SalesController::class, 'show'])->name('sales.show');
    Route::middleware('admin')->group(function () {
        Route::put('/sales/{sale}/refund', [\App\Http\Controllers\SalesController::class, 'refund'])->name('sales.refund');
    });

    // Admin-only Reports & Exports
    Route::middleware('admin')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/sales', [\App\Http\Controllers\ReportsController::class, 'exportSales'])->name('reports.export.sales');
        Route::get('/reports/export/products', [\App\Http\Controllers\ReportsController::class, 'exportProducts'])->name('reports.export.products');
        Route::get('/reports/export/stock', [\App\Http\Controllers\ReportsController::class, 'exportStock'])->name('reports.export.stock');
    });
});
