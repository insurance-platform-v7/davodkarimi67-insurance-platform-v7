<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Policy\RenewalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenewalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_exists(): void
    {
        $service = app(
            RenewalService::class
        );

        $this->assertInstanceOf(
            RenewalService::class,
            $service
        );
    }

    public function test_policy_is_eligible_when_it_expires_within_thirty_days(): void
    {
        Carbon::setTestNow(
            Carbon::parse('2026-08-16 12:00:00')
        );

        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 100000,
            'ends_at' => Carbon::parse('2026-08-30'),
        ]);

        $service = app(
            RenewalService::class
        );

        $this->assertTrue(
            $service->isEligible($policy)
        );

        Carbon::setTestNow();
    }

    public function test_policy_is_not_eligible_when_no_end_date_exists(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'ends_at' => null,
        ]);

        $service = app(
            RenewalService::class
        );

        $this->assertFalse(
            $service->isEligible($policy)
        );
    }

    public function test_policy_is_not_eligible_when_expiry_is_more_than_thirty_days_away(): void
    {
        Carbon::setTestNow(
            Carbon::parse('2026-08-16 12:00:00')
        );

        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'ends_at' => Carbon::parse('2026-10-01'),
        ]);

        $service = app(
            RenewalService::class
        );

        $this->assertFalse(
            $service->isEligible($policy)
        );

        Carbon::setTestNow();
    }

    public function test_create_renewal_quote_returns_expected_data(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance(
            'tenant',
            $tenant
        );

        $endsAt = Carbon::parse('2026-08-30');

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 250000,
            'ends_at' => $endsAt,
        ]);

        $service = app(
            RenewalService::class
        );

        $quote = $service->createRenewalQuote(
            $policy
        );

        $this->assertSame(
            250000.0,
            (float) $quote['premium']
        );

        $this->assertTrue(
            $quote['renewal']
        );

        $this->assertEquals(
            $endsAt,
            $quote['starts_at']
        );
    }
}
