<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderItemApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\UserApiController;

Route::get('/sales-data', [DashboardController::class, 'salesData']);

// API routes for products
Route::apiResource('products', ProductApiController::class);

// API routes for categories
Route::apiResource('categories', CategoryApiController::class);

// API routes for order items
Route::apiResource('order-items', OrderItemApiController::class);

// API routes for orders
Route::apiResource('orders', OrderApiController::class);

// API routes for users
Route::apiResource('users', UserApiController::class);


