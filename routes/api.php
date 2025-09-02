<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// Test route
Route::get('test', fn() => response()->json(['message' => 'REST API working']));

// Central routes (no tenant context)
Route::prefix('central')->group(function () {
    Route::get('health', fn() => response()->json(['status' => 'central ok']));
    // Future: tenant management, system health, etc.
});

// Tenant routes (require tenant context)
Route::middleware(['tenant'])->group(function () {
    // Guest auth endpoints
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Protected tenant routes
    Route::middleware(['auth:api', 'tenant_token'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Product endpoints
        Route::apiResource('products', ProductController::class)->only([
            'index',
            'store',
            'show',
            'update',
            'destroy'
        ]);
    });
});
