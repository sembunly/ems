<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Auth\SocialAuthController;

// Test Email Route (for Postman testing)
Route::get('/api/csrf-token', [TestEmailController::class, 'getCsrfToken'])->name('test.csrf');
Route::post('/api/test-email', [TestEmailController::class, 'sendTestEmail'])->name('test.email');
Route::post('/api/test-invoice-email', [TestEmailController::class, 'testInvoiceEmail'])->name('test.invoice.email')->middleware('auth'); 

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [FrontendProductController::class, 'show'])->name('products.show');

Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    Route::resource('orders', OrderController::class);
});

require __DIR__.'/frontend/web.php';
require __DIR__.'/auth.php';