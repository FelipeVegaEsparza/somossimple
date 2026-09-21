<?php

use App\Http\Middleware\EnsurePlatformAdmin;
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
        // Detrás de un proxy (Dokploy/Traefik) con HTTPS: confiar en las
        // cabeceras X-Forwarded-* para generar URLs https y cookies seguras.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => EnsurePlatformAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
