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
use App\Http\Controllers\Admin\PermissionController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard,index')
        ->name('dashboard');
    Route::get('orders/export', [OrderController::class, 'export'])
        ->middleware('permission:reports,export')
        ->name('orders.export');

    Route::get('products', [AdminProductController::class, 'index'])
        ->middleware('permission:products,index')
        ->name('products.index');
    Route::get('products/create', [AdminProductController::class, 'create'])
        ->middleware('permission:products,create')
        ->name('products.create');
    Route::post('products', [AdminProductController::class, 'store'])
        ->middleware('permission:products,store')
        ->name('products.store');
    Route::get('products/{product}', [AdminProductController::class, 'show'])
        ->middleware('permission:products,show')
        ->name('products.show');
    Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])
        ->middleware('permission:products,edit')
        ->name('products.edit');
    Route::match(['put', 'patch'], 'products/{product}', [AdminProductController::class, 'update'])
        ->middleware('permission:products,update')
        ->name('products.update');
    Route::delete('products/{product}', [AdminProductController::class, 'destroy'])
        ->middleware('permission:products,destroy')
        ->name('products.destroy');

    Route::get('categories', [CategoryController::class, 'index'])
        ->middleware('permission:categories,index')
        ->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])
        ->middleware('permission:categories,create')
        ->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])
        ->middleware('permission:categories,store')
        ->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:categories,edit')
        ->name('categories.edit');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:categories,update')
        ->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories,destroy')
        ->name('categories.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions,index')
        ->name('permissions.index');
    Route::post('permissions/groups', [PermissionController::class, 'createGroup'])
        ->middleware('permission:permissions,store')
        ->name('permissions.groups.store');
    Route::post('permissions', [PermissionController::class, 'store'])
        ->middleware('permission:permissions,store')
        ->name('permissions.store');

    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission:users,index')
        ->name('users.index');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users,edit')
        ->name('users.edit');
    Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users,update')
        ->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users,destroy')
        ->name('users.destroy');

    Route::get('orders', [OrderController::class, 'index'])
        ->middleware('permission:reports,index')
        ->name('orders.index');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])
        ->middleware('permission:orders,edit')
        ->name('orders.edit');
    Route::match(['put', 'patch'], 'orders/{order}', [OrderController::class, 'update'])
        ->middleware('permission:orders,update')
        ->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])
        ->middleware('permission:orders,destroy')
        ->name('orders.destroy');
});

require __DIR__.'/frontend/web.php';
require __DIR__.'/auth.php';
