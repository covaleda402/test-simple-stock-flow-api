<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controller\AuthController;
use App\Presentation\Http\Controller\CategoryController;
use App\Presentation\Http\Controller\ProductController;
use App\Presentation\Http\Controller\SaleController;
use App\Presentation\Http\Controller\ReportController;
use App\Presentation\Http\Controller\HealthController;
use App\Presentation\Http\Controller\MediaController;
use App\Presentation\Http\Middleware\JwtAuthMiddleware;
use App\Presentation\Http\Middleware\RequireAdminMiddleware;

// Rutas Públicas de la API
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas Protegidas por Autenticación JWT (Cualquier rol: admin o seller)
Route::middleware([JwtAuthMiddleware::class])->group(function () {
    // Categorías
    Route::get('/categories', [CategoryController::class, 'index']);

    // Productos (Lectura)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Ventas
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/sales/{id}', [SaleController::class, 'show']);

    // Reportes
    Route::get('/reports/sales', [ReportController::class, 'sales']);

    // Rutas Exclusivas para Administrador
    Route::middleware([RequireAdminMiddleware::class])->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::post('/products/{id}/image', [ProductController::class, 'uploadImage']);
    });
});