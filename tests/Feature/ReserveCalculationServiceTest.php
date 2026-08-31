<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\Reserve;
use App\Models\Tenant;
use App\Services\Actuarial\ReserveCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReserveCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_exists(): void
    {
        $service = app(
            ReserveCalculationService::class
        );

        $this->assertInstanceOf(
            ReserveCalculationService::class,
            $service
        );
    }

    public function test_reserve_is_calculated_and_stored_correctly(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 100000,
        ]);

        $service = app(
            ReserveCalculationService::class
        );

        $reserve = $service->calculate($policy);

        $this->assertDatabaseHas(
            'reserves',
            [
                'id' => $reserve->id,
                'tenant_id' => $tenant->id,
                'policy_id' => $policy->id,
                'reserve_amount' => 15000,
                'reserve_type' => 'best_estimate',
            ]
        );

        $this->assertSame(
            15000.0,
            (float) $reserve->reserve_amount
        );

        $this->assertEquals(
            now()->toDateString(),
            $reserve->valuation_date->toDateString()
        );
    }

    public function test_reserve_calculation_uses_fifteen_percent_of_premium(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 250000,
        ]);

        $service = app(
            ReserveCalculationService::class
        );

        $reserve = $service->calculate($policy);

        $this->assertSame(
            37500.0,
            (float) $reserve->reserve_amount
        );
    }

    public function test_reserve_is_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        /*
        |----------------------------------------------------------------------
        | Tenant A
        |----------------------------------------------------------------------
        */

        $this->app->instance(
            'tenant',
            $tenantA
        );

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
            'premium' => 100000,
        ]);

        $service = app(
            ReserveCalculationService::class
        );

        $reserveA = $service->calculate(
            $policyA
        );

        /*
        |----------------------------------------------------------------------
        | Tenant B
        |----------------------------------------------------------------------
        */

        $this->app->instance(
            'tenant',
            $tenantB
        );

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
            'premium' => 200000,
        ]);

        $reserveB = $service->calculate(
            $policyB
        );

        /*
        |----------------------------------------------------------------------
        | Tenant B sees only B
        |----------------------------------------------------------------------
        */

        $reserves = Reserve::query()->get();

        $this->assertCount(
            1,
            $reserves
        );

        $this->assertTrue(
            $reserves->first()->is($reserveB)
        );

        /*
        |----------------------------------------------------------------------
        | Switch to Tenant A
        |----------------------------------------------------------------------
        */

        $this->app->instance(
            'tenant',
            $tenantA
        );

        $reserves = Reserve::query()->get();

        $this->assertCount(
            1,
            $reserves
        );

        $this->assertTrue(
            $reserves->first()->is($reserveA)
        );
    }
}
