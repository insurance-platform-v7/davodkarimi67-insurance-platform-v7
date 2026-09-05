<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_access_admin_dashboard(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test-tenant',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => $tenant->id,
            'name' => 'View Admin Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000001',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();
    }

    public function test_user_without_permission_cannot_access_admin_dashboard(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test-tenant-2',
            'is_active' => true,
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'User',
            'code' => 'user',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000002',
            'email' => 'user@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }
}
