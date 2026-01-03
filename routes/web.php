<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/order', [HomeController::class, 'order'])->name('order');
Route::post('/order', [HomeController::class, 'placeOrder'])->name('order.place');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// Public documentation route (accessible without login)
Route::get('/docs/admin-guide', function () {
    return view('documentation.admin-guide');
})->name('docs.admin-guide');

Route::get('/dashboard', function () {
    // Redirect users based on their role
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    
    if (auth()->user()->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    }
    
    // Customer/Staff dashboard
    return view('dashboard');
})->middleware(['auth', 'phone.verified', 'approved'])->name('dashboard');

Route::middleware(['auth', 'phone.verified', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
