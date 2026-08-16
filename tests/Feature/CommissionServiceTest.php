<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Broker\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_commission_is_calculated_and_stored_correctly(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $broker = Broker::query()->create([
            'name' => 'Test Broker',
            'code' => 'BR-001',
            'commission_rate' => 10,
            'is_active' => true,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 250000,
        ]);

        $service = app(CommissionService::class);

        $commission = $service->calculate(
            $broker,
            $policy
        );

        $this->assertDatabaseHas(
            'broker_commissions',
            [
                'id' => $commission->id,
                'tenant_id' => $tenant->id,
                'broker_id' => $broker->id,
                'policy_id' => $policy->id,
                'premium' => 250000,
                'rate' => 10,
                'commission_amount' => 25000,
            ]
        );

        $this->assertSame(
            25000.0,
            (float) $commission->commission_amount
        );
    }

    public function test_commission_uses_broker_rate(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $broker = Broker::query()->create([
            'name' => 'Broker Two',
            'code' => 'BR-002',
            'commission_rate' => 7.50,
            'is_active' => true,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 100000,
        ]);

        $service = app(CommissionService::class);

        $commission = $service->calculate(
            $broker,
            $policy
        );

        $this->assertSame(
            7500.0,
            (float) $commission->commission_amount
        );

        $this->assertSame(
            7.50,
            (float) $commission->rate
        );
    }

    public function test_commissions_are_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $broker = Broker::query()->create([
            'name' => 'Shared Broker',
            'code' => 'BR-SHARED',
            'commission_rate' => 10,
            'is_active' => true,
        ]);

        app()->instance('tenant', $tenantA);

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
            'premium' => 100000,
        ]);

        $service = app(CommissionService::class);

        $commissionA = $service->calculate(
            $broker,
            $policyA
        );

        app()->instance('tenant', $tenantB);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
            'premium' => 300000,
        ]);

        $commissionB = $service->calculate(
            $broker,
            $policyB
        );

        $visible = \App\Models\BrokerCommission::query()->get();

        $this->assertCount(1, $visible);

        $this->assertTrue(
            $visible->first()->is($commissionB)
        );

        app()->instance('tenant', $tenantA);

        $visible = \App\Models\BrokerCommission::query()->get();

        $this->assertCount(1, $visible);

        $this->assertTrue(
            $visible->first()->is($commissionA)
        );
    }
}
