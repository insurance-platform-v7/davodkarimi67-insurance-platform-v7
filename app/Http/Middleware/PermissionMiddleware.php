<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $user->hasPermission($permission)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
