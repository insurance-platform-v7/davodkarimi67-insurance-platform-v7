<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\ClaimPayment;
use App\Models\Tenant;
use App\Services\Claim\ClaimSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimSettlementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_exists(): void
    {
        $service = app(ClaimSettlementService::class);

        $this->assertInstanceOf(
            ClaimSettlementService::class,
            $service
        );
    }

    public function test_claim_can_be_settled_and_payment_is_created(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => null,
        ]);

        $service = app(ClaimSettlementService::class);

        $payment = $service->settle($claim, 250000);

        $this->assertInstanceOf(
            ClaimPayment::class,
            $payment
        );

        $this->assertDatabaseHas('claim_payments', [
            'id' => $payment->id,
            'claim_id' => $claim->id,
            'tenant_id' => $tenant->id,
            'amount' => 250000,
        ]);
    }

    public function test_settlement_marks_claim_as_paid(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => null,
        ]);

        $service = app(ClaimSettlementService::class);

        $service->settle($claim, 300000);

        $claim->refresh();

        $this->assertSame(
            ClaimStatus::PAID,
            $claim->status
        );

        $this->assertSame(
            300000.0,
            (float) $claim->approved_amount
        );
    }

    public function test_settlement_creates_reference_and_paid_at(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => null,
        ]);

        $service = app(ClaimSettlementService::class);

        $payment = $service->settle($claim, 200000);

        $this->assertNotEmpty(
            $payment->reference_number
        );

        $this->assertStringStartsWith(
            'CLP-',
            $payment->reference_number
        );

        $this->assertNotNull(
            $payment->paid_at
        );
    }

    public function test_settlement_is_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $this->app->instance('tenant', $tenantA);

        $claimA = Claim::factory()->create([
            'tenant_id' => $tenantA->id,
            'status' => ClaimStatus::APPROVED,
        ]);

        $service = app(ClaimSettlementService::class);

        $payment = $service->settle(
            $claimA,
            150000
        );

        $this->assertSame(
            $tenantA->id,
            $payment->tenant_id
        );

        $this->app->instance('tenant', $tenantB);

        $this->assertCount(
            0,
            ClaimPayment::query()->get()
        );

        $this->app->instance('tenant', $tenantA);

        $this->assertCount(
            1,
            ClaimPayment::query()->get()
        );
    }

    public function test_settlement_rejects_zero_amount(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => 300000,
        ]);

        $service = app(ClaimSettlementService::class);

        $this->expectException(
            \InvalidArgumentException::class
        );

        $service->settle($claim, 0);
    }

    public function test_settlement_rejects_negative_amount(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => 300000,
        ]);

        $service = app(ClaimSettlementService::class);

        $this->expectException(
            \InvalidArgumentException::class
        );

        $service->settle($claim, -1000);
    }

    public function test_settlement_rejects_amount_greater_than_approved_amount(): void
    {
        $tenant = Tenant::factory()->create();

        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => 300000,
        ]);

        $service = app(ClaimSettlementService::class);

        $this->expectException(
            \InvalidArgumentException::class
        );

        $service->settle($claim, 350000);
    }
}