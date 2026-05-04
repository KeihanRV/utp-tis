<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
        // Suppress swagger-php warnings
        if (php_sapi_name() === 'cli' && (strpos($_SERVER['argv'][1] ?? '', 'l5-swagger') !== false)) {
            set_error_handler(function ($severity, $message, $file, $line) {
                if (str_contains($message, 'Required @OA')) {
                    return true;
                }
                return false;
            });
        }
    })->create();
