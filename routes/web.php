<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controller\HealthController;
use App\Presentation\Http\Controller\MediaController;

// E-14: GET /health (anónimo, prueba de vida del servicio)
Route::get('/health', [HealthController::class, 'check']);

// E-15: GET /media/{key} (anónimo, entrega de binarios)
Route::get('/media/{key}', [MediaController::class, 'show']);
