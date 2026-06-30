<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            return response()->json([
                'message' => 'X-Tenant-ID header is required.'
            ], 400);
        }

        $request->attributes->set('tenant_id', (int) $tenantId);

        return $next($request);
    }
}
