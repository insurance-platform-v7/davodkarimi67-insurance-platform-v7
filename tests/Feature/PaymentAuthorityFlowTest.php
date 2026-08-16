<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuthorityFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_creation_stores_gateway_authority(): void
    {
        $tenant = Tenant::factory()->create();

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 1000,
            'status' => PolicyStatus::QUOTE_CREATED,
        ]);

        $paymentService = app(PaymentService::class);

        $payment = $paymentService->createPayment(
            $policy->id
        );

        $this->assertNotNull(
            $payment->authority
        );

        $this->assertStringStartsWith(
            'ZP_',
            $payment->authority
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'policy_id' => $policy->id,
            'authority' => $payment->authority,
            'status' => 'pending',
        ]);
    }
}
