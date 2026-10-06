<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Domain\Exception\BusinessRuleValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Reglas de Negocio del Dominio -> 422 con RFC 7807
        $exceptions->render(function (BusinessRuleValidationException $e, Request $request) {
            return response()->json([
                'title' => 'Regla de negocio violada',
                'status' => 422,
                'detail' => $e->getMessage(),
            ], 422, ['Content-Type' => 'application/problem+json; charset=utf-8']);
        });

        // 2. Errores de Validación de Entrada -> 400 con RFC 7807 (api-contract.md §2.2)
        $exceptions->render(function (ValidationException $e, Request $request) {
            $errorKeys = array_keys($e->errors());
            $detail = 'Datos de entrada no válidos: ' . implode(', ', $errorKeys) . '.';

            return response()->json([
                'title' => 'Datos de entrada no válidos',
                'status' => 400,
                'detail' => $detail,
                'errors' => $e->errors(),
            ], 400, ['Content-Type' => 'application/problem+json; charset=utf-8']);
        });

        // 3. 404 Not Found -> Cuerpo Vacío (api-contract.md §2.3)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response('', 404);
        });

        // 4. 403 Forbidden -> Cuerpo Vacío (api-contract.md §2.3)
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response('', 403);
        });

        // 5. 405 Method Not Allowed -> Cuerpo Vacío (api-contract.md §2.3)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response('', 405);
        });
    })->create();
