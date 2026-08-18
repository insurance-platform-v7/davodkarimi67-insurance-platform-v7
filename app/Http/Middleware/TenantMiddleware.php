<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID');

        if ($tenantId === null || $tenantId === '') {
            return response()->json([
                'message' => 'X-Tenant-ID header is required.',
            ], 400);
        }

        if (! ctype_digit((string) $tenantId) || (int) $tenantId <= 0) {
            return response()->json([
                'message' => 'Invalid X-Tenant-ID header.',
            ], 400);
        }

        $tenant = Tenant::find((int) $tenantId);

        if (! $tenant) {
            return response()->json([
                'message' => 'Tenant not found.',
            ], 404);
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
