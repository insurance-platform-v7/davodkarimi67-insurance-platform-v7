<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClaimApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_claim(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000101',
            'email' => 'claim-api@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'customer@example.com',
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->withToken($token)
            ->postJson('/api/v1/claims', [
                'policy_id' => $policy->id,
                'requested_amount' => 1500000,
                'description' => 'Vehicle accident claim',
            ]);

        $response
            ->assertCreated()
            ->assertHeader('X-API-Version', 'v1')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'claim_number',
                    'policy_id',
                    'status',
                    'requested_amount',
                    'description',
                ],
            ])
            ->assertJsonPath(
                'data.policy_id',
                $policy->id
            )
            ->assertJsonPath(
                'data.status',
                ClaimStatus::SUBMITTED->value
            )
            ->assertJsonPath(
                'data.description',
                'Vehicle accident claim'
            );

        $this->assertDatabaseHas('claims', [
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'requested_amount' => 1500000,
            'description' => 'Vehicle accident claim',
            'status' => ClaimStatus::SUBMITTED->value,
        ]);
    }

    public function test_claim_cannot_be_created_for_another_tenant_policy(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenantA->id,
            'first_name' => 'Tenant',
            'last_name' => 'A',
            'mobile' => '09120000102',
            'email' => 'tenant-a-claim@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $customerB = Customer::factory()->create([
            'tenant_id' => $tenantB->id,
            'email' => 'tenant-b-customer@example.com',
        ]);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
            'customer_id' => $customerB->id,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this
            ->withHeader('X-Tenant-ID', $tenantA->id)
            ->withToken($token)
            ->postJson('/api/v1/claims', [
                'policy_id' => $policyB->id,
                'requested_amount' => 1000000,
                'description' => 'Cross tenant claim attempt',
            ]);

        $response->assertNotFound();

        $this->assertDatabaseMissing('claims', [
            'policy_id' => $policyB->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_claim(): void
    {
        $tenant = Tenant::factory()->create();

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
        ]);

        $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/v1/claims', [
                'policy_id' => $policy->id,
                'requested_amount' => 1000000,
                'description' => 'Unauthenticated claim',
            ])
            ->assertUnauthorized();
    }
}
