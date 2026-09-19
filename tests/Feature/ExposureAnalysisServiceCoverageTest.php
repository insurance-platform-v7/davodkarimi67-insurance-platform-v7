<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\ReinsuranceAllocation;
use App\Models\ReinsuranceContract;
use App\Models\Tenant;
use App\Services\Reinsurance\ExposureAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExposureAnalysisServiceCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyze_returns_premium_retention_and_exposure(): void
    {
        $tenant = Tenant::factory()->create();
        app()->instance('tenant', $tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $contract = ReinsuranceContract::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Exposure Test Contract',
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
            'retention' => 60000,
            'ceded_amount' => 40000,
            'reinsurer_share' => 20000,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 50000,
            'retention' => 30000,
            'ceded_amount' => 20000,
            'reinsurer_share' => 10000,
        ]);

        $result = (new ExposureAnalysisService)->analyze();

        $this->assertSame(150000.0, $result['total_premium']);
        $this->assertSame(90000.0, $result['total_retention']);
        $this->assertSame(60000.0, $result['exposure']);
    }

    public function test_analyze_never_returns_negative_exposure(): void
    {
        $tenant = Tenant::factory()->create();
        app()->instance('tenant', $tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $contract = ReinsuranceContract::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Retention Test Contract',
            'type' => 'quota_share',
            'retention_limit' => 500000,
            'cession_rate' => 20,
            'active' => true,
        ]);

        ReinsuranceAllocation::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => 50000,
            'retention' => 70000,
            'ceded_amount' => 0,
            'reinsurer_share' => 0,
        ]);

        $result = (new ExposureAnalysisService)->analyze();

        $this->assertSame(50000.0, $result['total_premium']);
        $this->assertSame(70000.0, $result['total_retention']);
        $this->assertSame(0, $result['exposure']);
    }
}
