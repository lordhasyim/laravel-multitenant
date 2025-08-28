<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// Test route
Route::get('test', function () {
    return response()->json(['message' => 'REST API working']);
});

// Central routes (no tenant context)
Route::prefix('central')->group(function () {
    Route::get('health', function () {
        return response()->json(['status' => 'central ok']);
    });
    // Future: tenant management, system health, etc.
});

// Tenant routes (require X-Tenant-Id header)
Route::middleware(['tenant'])->group(function () {
    // Auth endpoints
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected endpoints
    Route::middleware(['auth:api', 'tenant_token'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('user', [AuthController::class, 'user']);

        // Future REST endpoints:
        // Route::apiResource('products', ProductController::class);
        // Route::apiResource('orders', OrderController::class);
        // Route::apiResource('customers', CustomerController::class);

        // Product endpoints
        Route::get('products', [ProductController::class, 'index']);
        Route::post('products', [ProductController::class, 'store']);
        Route::get('products/{id}', [ProductController::class, 'show']);
    });
});