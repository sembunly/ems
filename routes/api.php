<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductApiController;
use App\Http\Controllers\Api\v1\CategoryApiController;
use App\Http\Controllers\Api\v1\OrderItemApiController;
use App\Http\Controllers\Api\v1\OrderApiController;
use App\Http\Controllers\Api\v1\UserApiController;

Route::get('/sales-data', [DashboardController::class, 'salesData']);

// API routes for products
Route::apiResource('api-products', ProductApiController::class);

// API routes for categories
Route::apiResource('api-categories', CategoryApiController::class);

// API routes for order items
Route::apiResource('api-orderitems', OrderItemApiController::class);

// API routes for orders
Route::apiResource('api-orders', OrderApiController::class);

// API routes for users
Route::apiResource('api-users', UserApiController::class);
