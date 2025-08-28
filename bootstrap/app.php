<?php

use App\Http\Middleware\TenantContextMiddleware;
use App\Http\Middleware\ValidateTenantToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    apiPrefix: '',
    commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'tenant' => TenantContextMiddleware::class,
        'tenant_token' => ValidateTenantToken::class,
    ]);

    // Or if you want it as a middleware group:
    // $middleware->group('tenant', [
    //     TenantContextMiddleware::class,
    // ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
