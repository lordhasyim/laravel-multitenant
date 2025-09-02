<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class TenantContextMiddleware
{
    protected Tenancy $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    public function handle($request, Closure $next)
    {
        // Require X-Tenant-Id header
        $tenantId = $request->header('X-Tenant-Id');

        if (! $tenantId) {
            return response()->json(['error' => 'Missing X-Tenant-Id header'], 400);
        }

        try {
            // Force lookup by UUID only
            $tenant = $this->tenancy->find($tenantId);

            if (! $tenant) {
                throw new TenantCouldNotBeIdentifiedById($tenantId);
            }

            // Initialize tenant context
            $this->tenancy->initialize($tenant);
        } catch (TenantCouldNotBeIdentifiedById $e) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        return $next($request);
    }
}
