<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        /** @var array{email: string, password: string} $validated */
        $validated = $request->validated();

        /** @var object{id: int} $tenant */
        $tenant = app('tenant');

        try {
            $result = $this->authService->login(
                $tenant->id,
                $validated['email'],
                $validated['password'],
            );

            return response()
                ->json($result, 200)
                ->header('X-API-Version', 'v1');
        } catch (\RuntimeException $e) {
            $status = $e->getMessage() === 'User account is inactive.'
                ? 403
                : 401;

            return response()
                ->json([
                    'message' => $e->getMessage(),
                ], $status)
                ->header('X-API-Version', 'v1');
        }
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
