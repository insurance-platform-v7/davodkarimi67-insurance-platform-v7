<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @return array{user: User, token: string}
     */
    public function login(
        int $tenantId,
        string $email,
        string $password
    ): array {
        $user = User::query()
            ->where('tenant_id', $tenantId)
            ->where('email', $email)
            ->first();

        if (
            ! $user ||
            ! Hash::check($password, $user->password)
        ) {
            throw new \RuntimeException('Invalid credentials');
        }

        if ($user->status !== 'active') {
            throw new \RuntimeException('User account is inactive.');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $user->update([
            'last_login_at' => now(),
        ]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request): void
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }
    }
}
