<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\ReinsuranceAllocation;
use App\Models\ReinsuranceContract;
use App\Models\Tenant;
use App\Services\Reinsurance\ReinsuranceReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReinsuranceReportingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_exists(): void
    {
        $service = app(
            ReinsuranceReportingService::class
        );

        $this->assertInstanceOf(
            ReinsuranceReportingService::class,
            $service
        );
    }

    public function test_summary_calculates_reinsurance_totals(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $contract = ReinsuranceContract::query()->create([
            'name' => 'Test Contract',
            'type' => 'quota_share',
            'retention_limit' => 500000,
            'cession_rate' => 20,
            'active' => true,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 100000,
            'retention' => 80000,
            'ceded_amount' => 20000,
            'reinsurer_share' => 20000,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 200000,
            'retention' => 160000,
            'ceded_amount' => 40000,
            'reinsurer_share' => 40000,
        ]);

        $service = app(
            ReinsuranceReportingService::class
        );

        $summary = $service->summary();

        $this->assertSame(
            300000.0,
            $summary['total_premium']
        );

        $this->assertSame(
            240000.0,
            $summary['total_retention']
        );

        $this->assertSame(
            60000.0,
            $summary['total_ceded']
        );

        $this->assertSame(
            60000.0,
            $summary['total_reinsurer_share']
        );
    }

    public function test_summary_is_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $contract = ReinsuranceContract::query()->create([
            'name' => 'Shared Test Contract',
            'type' => 'quota_share',
            'retention_limit' => 500000,
            'cession_rate' => 20,
            'active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenantA->id,
            'policy_id' => $policyA->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 100000,
            'retention' => 80000,
            'ceded_amount' => 20000,
            'reinsurer_share' => 20000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenantB->id,
            'policy_id' => $policyB->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 300000,
            'retention' => 240000,
            'ceded_amount' => 60000,
            'reinsurer_share' => 60000,
        ]);

        $service = app(
            ReinsuranceReportingService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Tenant B sees only B
        |--------------------------------------------------------------------------
        */

        $summary = $service->summary();

        $this->assertSame(
            300000.0,
            $summary['total_premium']
        );

        $this->assertSame(
            240000.0,
            $summary['total_retention']
        );

        $this->assertSame(
            60000.0,
            $summary['total_ceded']
        );

        $this->assertSame(
            60000.0,
            $summary['total_reinsurer_share']
        );

        /*
        |--------------------------------------------------------------------------
        | Switch to Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $summary = $service->summary();

        $this->assertSame(
            100000.0,
            $summary['total_premium']
        );

        $this->assertSame(
            80000.0,
            $summary['total_retention']
        );

        $this->assertSame(
            20000.0,
            $summary['total_ceded']
        );

        $this->assertSame(
            20000.0,
            $summary['total_reinsurer_share']
        );
    }
}
