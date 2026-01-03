<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProcessingRequestController;
use App\Http\Controllers\Admin\FreezerInventoryController;
use App\Http\Controllers\Admin\StoreItemController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'phone.verified', 'approved', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/pending', [UserController::class, 'pending'])->name('pending');
        Route::post('/{user}/approve', [UserController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [UserController::class, 'reject'])->name('reject');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Roles & Permissions Management
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);

    // Categories Management
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Animals Management
    Route::resource('animals', AnimalController::class);

    // Orders Management
    Route::resource('orders', OrderController::class);

    // Customer Management
    Route::resource('customers', CustomerController::class);

    // Processing Requests Management
    Route::resource('processing', ProcessingRequestController::class);
    Route::post('/processing/{processing}/update-status', [ProcessingRequestController::class, 'updateStatus'])->name('processing.update-status');

    // Freezer Inventory Management
    Route::resource('freezer', FreezerInventoryController::class);
    Route::patch('/freezer/{freezer}/update-status', [FreezerInventoryController::class, 'updateStatus'])->name('freezer.update-status');

    // Store Items Management
    Route::resource('store-items', StoreItemController::class);
    Route::patch('/store-items/{storeItem}/toggle-status', [StoreItemController::class, 'toggleStatus'])->name('store-items.toggle-status');
    Route::post('/store-items/{storeItem}/adjust-stock', [StoreItemController::class, 'adjustStock'])->name('store-items.adjust-stock');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/processing', [ReportController::class, 'processing'])->name('processing');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/export-sales', [ReportController::class, 'exportSales'])->name('export-sales');
        Route::get('/export-inventory', [ReportController::class, 'exportInventory'])->name('export-inventory');
        Route::get('/export-customers', [ReportController::class, 'exportCustomers'])->name('export-customers');
        Route::get('/export-processing', [ReportController::class, 'exportProcessing'])->name('export-processing');
        Route::get('/export-financial', [ReportController::class, 'exportFinancial'])->name('export-financial');
        Route::get('/print-sales', [ReportController::class, 'printSales'])->name('print-sales');
        Route::get('/print-financial', [ReportController::class, 'printFinancial'])->name('print-financial');
    });

    // System Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/download/{filename}', [LogController::class, 'download'])->name('download');
        Route::delete('/delete/{filename}', [LogController::class, 'delete'])->name('delete');
        Route::post('/clear', [LogController::class, 'clear'])->name('clear');
    });

    // Contact Messages
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('index');
        Route::get('/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('show');
        Route::put('/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'update'])->name('update');
        Route::delete('/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('destroy');
    });
});
