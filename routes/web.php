<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController as AdminSale;
use App\Http\Controllers\Admin\SupplierReturnController;
use App\Http\Controllers\Admin\SalesTrendController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboard;
use App\Http\Controllers\Cashier\SaleController as CashierSale;
use App\Http\Controllers\Cashier\ProfileController;
use App\Http\Controllers\Cashier\SalesTrendController as CashierSalesTrend;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Sales Trends
    Route::get('/sales-trends', [SalesTrendController::class, 'index'])->name('sales-trends.index');

    // Sales by product
    Route::get('/sales/by-product/{product}', [AdminSale::class, 'byProduct'])->name('sales.by-product');

    // Archived sales routes
    Route::get('/sales/trashed', [AdminSale::class, 'trashed'])->name('sales.trashed');
    Route::patch('/sales/{sale}/restore', [AdminSale::class, 'restore'])->name('sales.restore');
    Route::delete('/sales/{sale}/force-delete', [AdminSale::class, 'forceDelete'])->name('sales.force-delete');

    Route::get('/sales', [AdminSale::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [AdminSale::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/edit', [AdminSale::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [AdminSale::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [AdminSale::class, 'destroy'])->name('sales.destroy');

    // Products
    Route::get('/products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
    Route::patch('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{product}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::resource('products', ProductController::class);

    // Suppliers
    Route::get('/suppliers/trashed', [SupplierController::class, 'trashed'])->name('suppliers.trashed');
    Route::patch('/suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::delete('/suppliers/{supplier}/force-delete', [SupplierController::class, 'forceDelete'])->name('suppliers.force-delete');
    Route::resource('suppliers', SupplierController::class);

    // Cashiers
    Route::get('/cashiers', [CashierController::class, 'index'])->name('cashiers.index');
    Route::get('/cashiers/create', [CashierController::class, 'create'])->name('cashiers.create');
    Route::post('/cashiers', [CashierController::class, 'store'])->name('cashiers.store');
    Route::patch('/cashiers/{user}/toggle-status', [CashierController::class, 'toggleStatus'])->name('cashiers.toggle-status');
    Route::patch('/cashiers/{user}/reset-password', [CashierController::class, 'resetPassword'])->name('cashiers.reset-password');

    // FIX: Deliveries - Added archived routes before resource routes
    Route::get('/deliveries/trashed', [DeliveryController::class, 'trashed'])->name('deliveries.trashed');
    Route::patch('/deliveries/{delivery}/restore', [DeliveryController::class, 'restore'])->name('deliveries.restore');
    Route::delete('/deliveries/{delivery}/force-delete', [DeliveryController::class, 'forceDelete'])->name('deliveries.force-delete');
    
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
    Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::get('/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
    Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('deliveries.update');
    Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

    // Adjustments
    Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/adjustments/type/{type}', [StockAdjustmentController::class, 'byType'])->name('adjustments.type');
    Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');

    // Stock Movements
    Route::get('/movements', [StockMovementController::class, 'index'])->name('movements.index');

    /*
    |--------------------------------------------------------------------------
    | Supplier Returns
    |--------------------------------------------------------------------------
    */

    // Main Page
    Route::get('/supplier-returns', [SupplierReturnController::class, 'index'])
        ->name('supplier-returns.index');

    // Filter by Status (Completed / Rejected)
    Route::get('/supplier-returns/status/{status}', [SupplierReturnController::class, 'byStatus'])
        ->name('supplier-returns.status');

    // Filter by Supplier
    Route::get('/supplier-returns/supplier/{supplier}', [SupplierReturnController::class, 'bySupplier'])
        ->name('supplier-returns.supplier');

    // Create
    Route::get('/supplier-returns/create', [SupplierReturnController::class, 'create'])
        ->name('supplier-returns.create');

    Route::post('/supplier-returns', [SupplierReturnController::class, 'store'])
        ->name('supplier-returns.store');

    // View Single Record
    Route::get('/supplier-returns/{return}', [SupplierReturnController::class, 'show'])
        ->name('supplier-returns.show');

    // Update Status
    Route::patch('/supplier-returns/{return}/status', [SupplierReturnController::class, 'updateStatus'])
        ->name('supplier-returns.update-status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
});

// Cashier routes
Route::middleware(['auth', 'cashier'])->prefix('cashier')->name('cashier.')->group(function () {

    Route::get('/dashboard', [CashierDashboard::class, 'index'])->name('dashboard');

    // DAGDAG: Sales Trend Analytics
    Route::get('/sales-trend', [CashierSalesTrend::class, 'index'])
        ->name('sales-trend.index');

    // FIX: Added archived sales routes BEFORE regular sales routes
    Route::get('/sales/archived', [CashierSale::class, 'archived'])->name('sales.archived');
    Route::post('/sales/{sale}/restore', [CashierSale::class, 'restore'])->name('sales.restore');
    // FIX: Added force-delete route for permanent deletion
    Route::delete('/sales/{sale}/force-delete', [CashierSale::class, 'forceDelete'])->name('sales.force-delete');

    Route::get('/sales', [CashierSale::class, 'index'])->name('sales.index');
    Route::get('/sales/history', [CashierSale::class, 'index'])->name('sales.history');
    Route::get('/sales/create', [CashierSale::class, 'create'])->name('sales.create');
    Route::post('/sales', [CashierSale::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/edit', [CashierSale::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [CashierSale::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [CashierSale::class, 'destroy'])->name('sales.destroy');
    Route::get('/sales/{sale}', [CashierSale::class, 'show'])->name('sales.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});