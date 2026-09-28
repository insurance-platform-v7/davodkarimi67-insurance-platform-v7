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

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $tenant = Tenant::create([
            'name' => 'Unauthenticated Tenant',
            'code' => 'unauthenticated-tenant',
            'is_active' => true,
        ]);

        $this->withHeader('X-Tenant-ID', $tenant->id)
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }

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

    public function test_user_without_role_has_no_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'No Role Tenant',
            'code' => 'no-role-tenant',
            'is_active' => true,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => null,
            'first_name' => 'No',
            'last_name' => 'Role',
            'mobile' => '09120000003',
            'email' => 'norole@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertFalse(
            $user->hasPermission('admin.dashboard')
        );
    }

    public function test_user_without_tenant_has_no_permission(): void
    {
        $user = User::create([
            'tenant_id' => null,
            'role_id' => null,
            'first_name' => 'No',
            'last_name' => 'Tenant',
            'mobile' => '09120000004',
            'email' => 'notenant@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertFalse(
            $user->hasPermission('admin.dashboard')
        );
    }

    public function test_user_with_role_from_another_tenant_has_no_permission(): void
    {
        $tenantA = Tenant::create([
            'name' => 'Tenant A',
            'code' => 'tenant-a',
            'is_active' => true,
        ]);

        $tenantB = Tenant::create([
            'name' => 'Tenant B',
            'code' => 'tenant-b',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role = Role::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Admin B',
            'code' => 'admin',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenantA->id,
            'role_id' => $role->id,
            'first_name' => 'Cross',
            'last_name' => 'Tenant',
            'mobile' => '09120000005',
            'email' => 'cross@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertFalse(
            $user->hasPermission('admin.dashboard')
        );
    }

    public function test_user_without_requested_permission_has_no_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'Permission Tenant',
            'code' => 'permission-tenant',
            'is_active' => true,
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        $permission = Permission::create([
            'tenant_id' => $tenant->id,
            'name' => 'View Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Test',
            'last_name' => 'Permission',
            'mobile' => '09120000006',
            'email' => 'permission@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertFalse(
            $user->hasPermission('admin.users')
        );
    }

    public function test_user_can_have_global_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'Global Permission Tenant',
            'code' => 'global-permission',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => null,
            'name' => 'Global Dashboard',
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
            'first_name' => 'Global',
            'last_name' => 'Permission',
            'mobile' => '09120000007',
            'email' => 'global@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $user->hasPermission('admin.dashboard')
        );
    }

    public function test_super_admin_can_use_tenant_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'Super Admin Tenant',
            'code' => 'super-admin-tenant',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => $tenant->id,
            'name' => 'Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role = Role::create([
            'tenant_id' => null,
            'name' => 'Super Admin',
            'code' => 'SUPER_ADMIN',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'mobile' => '09120000008',
            'email' => 'superadmin@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $user->hasPermission('admin.dashboard')
        );
    }

    public function test_role_relationships_work(): void
    {
        $tenant = Tenant::create([
            'name' => 'Role Tenant',
            'code' => 'role-tenant',
            'is_active' => true,
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Role',
            'code' => 'test-role',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Role',
            'last_name' => 'User',
            'mobile' => '09120000009',
            'email' => 'role-user@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $role->tenant->is($tenant)
        );

        $this->assertTrue(
            $role->users->contains($user)
        );
    }

    public function test_user_relationships_work(): void
    {
        $tenant = Tenant::create([
            'name' => 'User Relationship Tenant',
            'code' => 'user-rel-tenant',
            'is_active' => true,
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Relationship Role',
            'code' => 'relationship-role',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Relationship',
            'last_name' => 'User',
            'mobile' => '09120000010',
            'email' => 'relationship@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $user->tenant->is($tenant)
        );

        $this->assertTrue(
            $user->role->is($role)
        );
    }

    public function test_user_with_same_tenant_role_and_permission_has_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'Same Tenant',
            'code' => 'same-tenant',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => $tenant->id,
            'name' => 'View Dashboard',
            'code' => 'dashboard.view',
        ]);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'code' => 'tenant_admin',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Same',
            'last_name' => 'Tenant',
            'mobile' => '09120000011',
            'email' => 'same-tenant@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $user->hasPermission('dashboard.view')
        );
    }

    public function test_super_admin_can_use_global_permission(): void
    {
        $tenant = Tenant::create([
            'name' => 'Super Admin Global Tenant',
            'code' => 'super-admin-global',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'tenant_id' => null,
            'name' => 'Global Dashboard',
            'code' => 'admin.dashboard',
        ]);

        $role = Role::create([
            'tenant_id' => null,
            'name' => 'Super Admin',
            'code' => 'SUPER_ADMIN',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'first_name' => 'Super',
            'last_name' => 'Global',
            'mobile' => '09120000012',
            'email' => 'superadmin-global@test.com',
            'password' => 'password123',
            'status' => 'active',
        ]);

        $this->assertTrue(
            $user->hasPermission('admin.dashboard')
        );
    }
}
