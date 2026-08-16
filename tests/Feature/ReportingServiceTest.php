<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Reporting\ReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporting_summary_exists(): void
    {
        $service = app(
            ReportingService::class
        );

        $summary = $service->summary();

        $this->assertArrayHasKey(
            'premium_total',
            $summary
        );

        $this->assertArrayHasKey(
            'policy_count',
            $summary
        );

        $this->assertArrayHasKey(
            'payment_success_rate',
            $summary
        );
    }

    public function test_payment_success_rate_counts_paid_payments(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        Payment::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'gateway' => 'fake',
            'transaction_id' => 'tx-paid-1',
            'authority' => 'fake_authority_1',
            'amount' => 1000,
            'status' => PaymentStatus::PAID,
        ]);

        Payment::query()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'gateway' => 'fake',
            'transaction_id' => 'tx-pending-1',
            'authority' => 'fake_authority_2',
            'amount' => 1000,
            'status' => PaymentStatus::PENDING,
        ]);

        $service = app(
            ReportingService::class
        );

        $this->assertSame(
            50.0,
            $service->paymentSuccessRate()
        );
    }

    public function test_reporting_is_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
            'premium' => 100000,
        ]);

        Payment::query()->create([
            'tenant_id' => $tenantA->id,
            'policy_id' => $policyA->id,
            'gateway' => 'fake',
            'transaction_id' => 'tenant-a-paid',
            'authority' => 'fake_tenant_a',
            'amount' => 100000,
            'status' => PaymentStatus::PAID,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
            'premium' => 300000,
        ]);

        Payment::query()->create([
            'tenant_id' => $tenantB->id,
            'policy_id' => $policyB->id,
            'gateway' => 'fake',
            'transaction_id' => 'tenant-b-paid',
            'authority' => 'fake_tenant_b',
            'amount' => 300000,
            'status' => PaymentStatus::PAID,
        ]);

        Payment::query()->create([
            'tenant_id' => $tenantB->id,
            'policy_id' => $policyB->id,
            'gateway' => 'fake',
            'transaction_id' => 'tenant-b-pending',
            'authority' => 'fake_tenant_b_pending',
            'amount' => 300000,
            'status' => PaymentStatus::PENDING,
        ]);

        $service = app(
            ReportingService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Tenant B sees only B
        |--------------------------------------------------------------------------
        */

        $summary = $service->summary();

        $this->assertSame(
            300000.0,
            $summary['premium_total']
        );

        $this->assertSame(
            1,
            $summary['policy_count']
        );

        $this->assertSame(
            50.0,
            $summary['payment_success_rate']
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
            $summary['premium_total']
        );

        $this->assertSame(
            1,
            $summary['policy_count']
        );

        $this->assertSame(
            100.0,
            $summary['payment_success_rate']
        );
    }
}