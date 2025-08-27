<?php
// app/Http/Middleware/TenantContextMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get tenant from X-Tenant-Id header
        $tenantId = $request->header('X-Tenant-Id');
        
        if (!$tenantId) {
            return response()->json([
                'error' => 'Tenant identifier required',
                'message' => 'Please provide X-Tenant-Id header'
            ], 400);
        }
        
        // Find and initialize tenant
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'Invalid tenant identifier'
            ], 404);
        }
        
        // Initialize tenancy
        tenancy()->initialize($tenant);
        
        return $next($request);
    }
}