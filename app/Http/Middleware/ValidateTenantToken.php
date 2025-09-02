<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ValidateTenantToken
{
    public function handle($request, Closure $next)
    {
        try {
            // Parse JWT token from Authorization header
            $user = JWTAuth::parseToken()->authenticate();
            $claims = JWTAuth::getPayload();

            $tokenTenantId = $claims->get('tenant_id');   // UUID from token
            $currentTenantId = tenant('id');              // UUID from X-Tenant-Id

            if ($tokenTenantId !== $currentTenantId) {
                return response()->json(['error' => 'Invalid tenant token'], 403);
            }

            // Attach user to request if needed
            $request->merge(['auth_user' => $user]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token validation failed'], 401);
        }

        return $next($request);
    }
}
