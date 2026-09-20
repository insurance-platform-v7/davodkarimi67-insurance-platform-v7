<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $tenantId = $request->header('X-Tenant-ID');

        if ($tenantId === null || $tenantId === '') {
            return response()->json([
                'message' => 'X-Tenant-ID header is required.',
            ], 400);
        }

        if (
            ! ctype_digit((string) $tenantId)
            || (int) $tenantId <= 0
        ) {
            return response()->json([
                'message' => 'Invalid X-Tenant-ID header.',
            ], 400);
        }

        $tenant = Tenant::query()
            ->whereKey((int) $tenantId)
            ->where('is_active', true)
            ->first();

        if ($tenant === null) {
            return response()->json([
                'message' => 'Tenant not found.',
            ], 404);
        }

        $user = $request->user();

        if ($user !== null) {
            if ((int) $user->tenant_id !== (int) $tenant->id) {
                return response()->json([
                    'message' => 'Tenant mismatch.',
                ], 403);
            }
        }

        app()->instance('tenant', $tenant);

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
