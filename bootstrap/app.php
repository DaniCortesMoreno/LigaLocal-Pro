<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole; // Asegúrate de que este middleware existe

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aquí registramos el middleware personalizado 'role'
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Captura de errores en rutas API y retorno como JSON
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*')) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $statusCode);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
    })
    ->create();
