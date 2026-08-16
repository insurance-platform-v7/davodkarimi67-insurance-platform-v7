<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Claim\ClaimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_claim_service_exists(): void
    {
        $service = app(
            ClaimService::class
        );

        $this->assertInstanceOf(
            ClaimService::class,
            $service
        );
    }

    public function test_claim_can_be_created(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $service = app(
            ClaimService::class
        );

        $claim = $service->create(
            $policy,
            [
                'requested_amount' => 1000000,
                'description' => 'Vehicle accident claim',
            ]
        );

        $this->assertInstanceOf(
            Claim::class,
            $claim
        );

        $this->assertSame(
            $policy->id,
            $claim->policy_id
        );

        $this->assertSame(
            ClaimStatus::SUBMITTED,
            $claim->status
        );

        $this->assertSame(
            1000000.0,
            (float) $claim->requested_amount
        );

        $this->assertSame(
            'Vehicle accident claim',
            $claim->description
        );

        $this->assertNotEmpty(
            $claim->claim_number
        );

        $this->assertDatabaseHas(
            'claims',
            [
                'id' => $claim->id,
                'tenant_id' => $tenant->id,
                'policy_id' => $policy->id,
                'status' => ClaimStatus::SUBMITTED->value,
            ]
        );
    }

    public function test_claim_number_starts_with_clm(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $service = app(
            ClaimService::class
        );

        $claim = $service->create(
            $policy,
            [
                'requested_amount' => 500000,
                'description' => 'Test claim',
            ]
        );

        $this->assertStringStartsWith(
            'CLM-',
            $claim->claim_number
        );
    }

    public function test_claim_belongs_to_policy(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $service = app(
            ClaimService::class
        );

        $claim = $service->create(
            $policy,
            [
                'requested_amount' => 750000,
                'description' => 'Policy claim',
            ]
        );

        $this->assertTrue(
            $claim->policy->is($policy)
        );
    }

    public function test_claim_is_tenant_isolated(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $service = app(
            ClaimService::class
        );

        $claim = $service->create(
            $policy,
            [
                'requested_amount' => 250000,
                'description' => 'Tenant claim',
            ]
        );

        $this->assertSame(
            $tenant->id,
            $claim->tenant_id
        );
    }
}
