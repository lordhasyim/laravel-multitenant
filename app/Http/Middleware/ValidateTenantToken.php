<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateTenantToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Get the token payload
            $payload = JWTAuth::parseToken()->getPayload();
            $tokenTenantId = $payload->get('tenant_id');
            
            // Get tenant ID from header
            $headerTenantId = (int) $request->header('X-Tenant-Id');
            
            // Validate they match
            if ($tokenTenantId !== $headerTenantId) {
                return response()->json([
                    'error' => 'Token does not belong to the specified tenant',
                    'message' => 'Access denied'
                ], 403);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid token',
                'message' => 'Token validation failed'
            ], 401);
        }
        
        return $next($request);
    }
}