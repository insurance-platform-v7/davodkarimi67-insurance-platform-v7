<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\PaymentCallbackWorkflowService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCallbackIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_successful_callback_is_idempotent(): void
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

        $callbackService = app(
            PaymentCallbackWorkflowService::class
        );

        $payload = [
            'authority' => $payment->authority,
            'transaction_id' => $payment->transaction_id,
            'amount' => 1000,
        ];

        $firstResult = $callbackService->handle($payload);

        $payment->refresh();
        $policy->refresh();

        $firstPaidAt = $payment->paid_at;
        $firstCallbackData = $payment->callback_data;

        $secondResult = $callbackService->handle($payload);

        $payment->refresh();
        $policy->refresh();

        $this->assertTrue($firstResult);
        $this->assertTrue($secondResult);

        $this->assertSame(
            PaymentStatus::PAID,
            $payment->status
        );

        $this->assertSame(
            PolicyStatus::PAID,
            $policy->status
        );

        $this->assertEquals(
            $firstPaidAt,
            $payment->paid_at
        );

        $this->assertEquals(
            $firstCallbackData,
            $payment->callback_data
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::PAID->value,
        ]);

        $this->assertDatabaseHas('policies', [
            'id' => $policy->id,
            'status' => PolicyStatus::PAID->value,
        ]);
    }
}
