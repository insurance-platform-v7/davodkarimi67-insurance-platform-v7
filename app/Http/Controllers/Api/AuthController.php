<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $tenant = app('tenant');

        $user = User::query()
            ->where('tenant_id', $tenant->id)
            ->where('email', $request->email)
            ->first();

        if (
            ! $user ||
            ! Hash::check($request->password, $user->password)
        ) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'User account is inactive.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $user->update([
            'last_login_at' => now(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();
            }
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
