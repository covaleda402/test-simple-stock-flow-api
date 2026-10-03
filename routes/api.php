<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controller\ProductController;
use App\Presentation\Http\Controller\OrderController;

Route::prefix('v1')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
});