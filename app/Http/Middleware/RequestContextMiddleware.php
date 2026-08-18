<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        $correlationId = $request->header(
            'X-Correlation-ID',
            (string) Str::uuid()
        );

        $tenantId = $request->header('X-Tenant-ID');
        $requestId = (string) Str::uuid();

        if ($tenantId && ctype_digit((string) $tenantId)) {
            $tenant = Tenant::find((int) $tenantId);

            if ($tenant) {
                app()->instance('tenant', $tenant);
            }
        }

        Log::withContext([
            'correlation_id' => $correlationId,
            'request_id' => $requestId,
            'tenant_id' => $tenantId,
            'method' => $request->method(),
            'path' => $request->path(),
        ]);

        /** @var Response $response */
        $response = $next($request);

        $duration = round(
            (microtime(true) - $startedAt) * 1000,
            2
        );

        Log::info('api_request_metric', [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'tenant_id' => $tenantId,
        ]);

        $response->headers->set('X-Correlation-ID', $correlationId);
        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-Response-Time', "{$duration}ms");

        return $response;
    }
}
