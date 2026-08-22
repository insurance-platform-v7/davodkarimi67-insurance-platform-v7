<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_access_admin_dashboard(): void
    {
        $tenant = Tenant::factory()->create();

        $permission = Permission::create([
            'name' => 'Admin Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role = Role::create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'mobile' => '09120000010',
            'email' => 'permission@example.com',
            'national_code' => '0012345690',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();
    }

    public function test_user_without_permission_cannot_access_admin_dashboard(): void
    {
        $tenant = Tenant::factory()->create();

        $role = Role::create([
            'name' => 'User',
            'code' => 'user',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Normal',
            'last_name' => 'User',
            'mobile' => '09120000011',
            'email' => 'no-permission@example.com',
            'national_code' => '0012345691',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }
}