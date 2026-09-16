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

        $payment = app(ClaimSettlementService::class)
            ->settle($claim, 250000);

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

        app(ClaimSettlementService::class)
            ->settle($claim, 300000);

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

        $payment = app(ClaimSettlementService::class)
            ->settle($claim, 200000);

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

        $payment = app(ClaimSettlementService::class)
            ->settle($claimA, 150000);

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

        $this->expectException(
            \InvalidArgumentException::class
        );

        app(ClaimSettlementService::class)
            ->settle($claim, 0);
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

        $this->expectException(
            \InvalidArgumentException::class
        );

        app(ClaimSettlementService::class)
            ->settle($claim, -1000);
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

        $this->expectException(
            \InvalidArgumentException::class
        );

        app(ClaimSettlementService::class)
            ->settle($claim, 350000);
    }

    public function test_settlement_rejects_already_paid_claim(): void
    {
        $tenant = Tenant::factory()->create();
        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::PAID,
            'approved_amount' => 300000,
        ]);

        $this->expectException(
            \InvalidArgumentException::class
        );

        app(ClaimSettlementService::class)
            ->settle($claim, 300000);
    }

    public function test_settlement_rejects_non_approved_claim(): void
    {
        $tenant = Tenant::factory()->create();
        $this->app->instance('tenant', $tenant);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => ClaimStatus::SUBMITTED,
            'approved_amount' => null,
        ]);

        $this->expectException(
            \InvalidArgumentException::class
        );

        app(ClaimSettlementService::class)
            ->settle($claim, 300000);
    }
}