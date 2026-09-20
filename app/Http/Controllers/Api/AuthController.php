<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $email = $validated['email'] ?? null;
        $password = $validated['password'] ?? null;

        if (! is_string($email) || ! is_string($password)) {
            return response()->json([
                'message' => 'Invalid login payload.',
            ], 422);
        }

        $tenant = app('tenant');

        if (! is_object($tenant) || ! isset($tenant->id)) {
            return response()->json([
                'message' => 'Tenant context is not available.',
            ], 500);
        }

        $tenantId = $tenant->id;

        if (! is_int($tenantId) && ! is_numeric($tenantId)) {
            return response()->json([
                'message' => 'Invalid tenant ID.',
            ], 500);
        }

        try {
            $result = $this->authService->login(
                (int) $tenantId,
                $email,
                $password,
            );
        } catch (RuntimeException $exception) {
            $status = $exception->getMessage() === 'Invalid credentials'
                ? 401
                : 403;

            return response()->json([
                'message' => $exception->getMessage(),
            ], $status);
        }

        return response()
            ->json($result)
            ->header('X-API-Version', 'v1');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return response()
            ->json([
                'message' => 'Logged out successfully',
            ])
            ->header('X-API-Version', 'v1');
    }

    public function me(Request $request): JsonResponse
    {
        return response()
            ->json([
                'user' => $request->user(),
            ])
            ->header('X-API-Version', 'v1');
    }
}
