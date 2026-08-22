<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000001',
            'email' => 'test@example.com',
            'national_code' => '0012345678',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/v1/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'user',
                'token',
            ]);

        $this->assertNotEmpty(
            $response->json('token')
        );

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_invalid_password_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000002',
            'email' => 'wrong-password@example.com',
            'national_code' => '0012345679',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/v1/login', [
                'email' => 'wrong-password@example.com',
                'password' => 'wrong-password',
            ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $tenant = Tenant::factory()->create();

        User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Inactive',
            'last_name' => 'User',
            'mobile' => '09120000003',
            'email' => 'inactive@example.com',
            'national_code' => '0012345680',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/v1/login', [
                'email' => 'inactive@example.com',
                'password' => 'password123',
            ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'User account is inactive.',
            ]);
    }

    public function test_user_can_logout_and_token_is_revoked(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Logout',
            'last_name' => 'User',
            'mobile' => '09120000004',
            'email' => 'logout@example.com',
            'national_code' => '0012345681',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->postJson('/api/v1/logout');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_logged_out_token_cannot_access_protected_endpoint(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Revoked',
            'last_name' => 'User',
            'mobile' => '09120000005',
            'email' => 'revoked@example.com',
            'national_code' => '0012345682',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);

        auth()->forgetGuards();

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard');

        $response->assertUnauthorized();
    }

    public function test_sanctum_rejects_deleted_token_directly(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Sanctum',
            'last_name' => 'Test',
            'mobile' => '09120000006',
            'email' => 'sanctum-test@example.com',
            'national_code' => '0012345683',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $accessToken = $user->tokens()->latest()->first();

        $this->assertNotNull($accessToken);

        $accessToken->delete();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->id,
        ]);

        $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }

    public function test_user_from_another_tenant_cannot_login(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        User::create([
            'tenant_id' => $tenantA->id,
            'first_name' => 'Tenant',
            'last_name' => 'A',
            'mobile' => '09120000007',
            'email' => 'cross-tenant@example.com',
            'national_code' => '0012345684',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this
            ->withHeader('X-Tenant-ID', $tenantB->id)
            ->postJson('/api/v1/login', [
                'email' => 'cross-tenant@example.com',
                'password' => 'password123',
            ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
