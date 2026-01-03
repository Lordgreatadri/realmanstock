<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
|
| Routes for manager role with operational management access
|
*/

Route::prefix('manager')->name('manager.')->middleware(['auth', 'phone.verified', 'approved', 'role:manager'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Animals Management
    Route::resource('animals', \App\Http\Controllers\Manager\AnimalController::class);
    
    // Orders Management
    Route::resource('orders', \App\Http\Controllers\Manager\OrderController::class);
    
    // Customers Management
    Route::resource('customers', \App\Http\Controllers\Manager\CustomerController::class);
    
    // Processing Management
    Route::resource('processing', \App\Http\Controllers\Manager\ProcessingController::class);
    Route::post('/processing/{processing}/update-status', [\App\Http\Controllers\Manager\ProcessingController::class, 'updateStatus'])->name('processing.update-status');
    
    // Freezer Inventory
    Route::resource('freezer', \App\Http\Controllers\Manager\FreezerController::class);
        Route::patch('/freezer/{freezer}/update-status', [\App\Http\Controllers\Manager\FreezerController::class, 'updateStatus'])->name('freezer.update-status');
    
    // Store Items
    Route::resource('store-items', \App\Http\Controllers\Manager\StoreItemController::class);
    
    // Reports (View & Export only, no delete)
    Route::get('/reports', [\App\Http\Controllers\Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [\App\Http\Controllers\Manager\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [\App\Http\Controllers\Manager\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/customers', [\App\Http\Controllers\Manager\ReportController::class, 'customers'])->name('reports.customers');
    Route::get('/reports/processing', [\App\Http\Controllers\Manager\ReportController::class, 'processing'])->name('reports.processing');
    Route::get('/reports/financial', [\App\Http\Controllers\Manager\ReportController::class, 'financial'])->name('reports.financial');
    
    // Export Reports
    Route::get('/reports/export/sales', [\App\Http\Controllers\Manager\ReportController::class, 'exportSales'])->name('reports.export.sales');
    Route::get('/reports/export/inventory', [\App\Http\Controllers\Manager\ReportController::class, 'exportInventory'])->name('reports.export.inventory');
    Route::get('/reports/export/customers', [\App\Http\Controllers\Manager\ReportController::class, 'exportCustomers'])->name('reports.export.customers');
    Route::get('/reports/export/processing', [\App\Http\Controllers\Manager\ReportController::class, 'exportProcessing'])->name('reports.export.processing');
    Route::get('/reports/export/financial', [\App\Http\Controllers\Manager\ReportController::class, 'exportFinancial'])->name('reports.export.financial');
    
    // Print Reports
    Route::get('/reports/print/sales', [\App\Http\Controllers\Manager\ReportController::class, 'printSales'])->name('reports.print.sales');
    Route::get('/reports/print/financial', [\App\Http\Controllers\Manager\ReportController::class, 'printFinancial'])->name('reports.print.financial');
    
    // Categories
    Route::resource('categories', \App\Http\Controllers\Manager\CategoryController::class);
    Route::post('/categories/{category}/toggle-status', [\App\Http\Controllers\Manager\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // System Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Manager\LogController::class, 'index'])->name('index');
        Route::get('/download/{filename}', [\App\Http\Controllers\Manager\LogController::class, 'download'])->name('download');
    });
});
