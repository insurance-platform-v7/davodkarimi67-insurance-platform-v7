<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class PaymentAuthorityMismatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_cannot_be_marked_paid_with_another_payments_authority(): void
    {
        $tenant = Tenant::factory()->create();

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 1000,
            'status' => 'payment_pending',
        ]);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 2000,
            'status' => 'payment_pending',
        ]);

        $paymentA = Payment::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policyA->id,
            'gateway' => 'ZARINPAL',
            'transaction_id' => (string) Str::uuid(),
            'authority' => 'ZP_PAYMENT_A',
            'amount' => 1000,
            'status' => PaymentStatus::PENDING,
        ]);

        $paymentB = Payment::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policyB->id,
            'gateway' => 'ZARINPAL',
            'transaction_id' => (string) Str::uuid(),
            'authority' => 'ZP_PAYMENT_B',
            'amount' => 2000,
            'status' => PaymentStatus::PENDING,
        ]);

        $paymentService = app(PaymentService::class);

        $this->expectException(RuntimeException::class);

        $paymentService->markPaid(
            $paymentA->transaction_id,
            [
                'authority' => $paymentB->authority,
            ]
        );

        $this->assertDatabaseHas('payments', [
            'id' => $paymentA->id,
            'status' => PaymentStatus::PENDING->value,
        ]);
    }
}
