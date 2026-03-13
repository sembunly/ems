<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/sales-data', [DashboardController::class, 'salesData']);
