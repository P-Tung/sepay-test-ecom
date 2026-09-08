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
        // Cloudflare Tunnel / ngrok terminate TLS o dau proxy. Tin proxy de Laravel
        // doc X-Forwarded-Proto va sinh URL https thay vi http.
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: ['sepay/webhook', 'sepay/ipn']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
