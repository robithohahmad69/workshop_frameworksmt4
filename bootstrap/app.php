<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
       $middleware->validateCsrfTokens(except: [
        'midtrans/webhook',
    ]);

        // FIX SSE: Register WithoutSession middleware
        $middleware->alias(['without-session' => \App\Http\Middleware\WithoutSession::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
