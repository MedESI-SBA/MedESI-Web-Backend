<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/patients')->middleware('auth:patient')->group(base_path('routes/patients/index.php'));
            Route::prefix('api/doctors')->middleware('auth:doctor')->group(base_path('routes/doctors/index.php'));
            Route::prefix('api/admins')->middleware('auth:admin')->group(base_path('routes/admins/index.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
