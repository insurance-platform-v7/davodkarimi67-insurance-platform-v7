<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\PaymentCallbackWorkflowService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentCallbackFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_payment_callback_does_not_mark_payment_or_policy_paid(): void
    {
        $tenant = Tenant::factory()->create();

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'premium' => 1000,
            'status' => PolicyStatus::QUOTE_CREATED,
        ]);

        $paymentService = app(PaymentService::class);

        $payment = $paymentService->createPayment($policy->id);

        $this->assertSame(PaymentStatus::PENDING, $payment->status);

        $callbackService = app(PaymentCallbackWorkflowService::class);

        $result = $callbackService->handle([
            'authority' => 'invalid_authority',
            'transaction_id' => $payment->transaction_id,
            'amount' => 1000,
        ]);

        $this->assertFalse($result);

        $payment->refresh();
        $policy->refresh();

        $this->assertSame(PaymentStatus::PENDING, $payment->status);
        $this->assertSame(PolicyStatus::PAYMENT_PENDING, $policy->status);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'policy_id' => $policy->id,
            'status' => PaymentStatus::PENDING->value,
        ]);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::PAID->value,
        ]);

        $this->assertDatabaseMissing('policies', [
            'id' => $policy->id,
            'status' => PolicyStatus::PAID->value,
        ]);
    }

    public function test_callback_returns_false_when_payload_field_is_missing(): void
    {
        $service = app(PaymentCallbackWorkflowService::class);

        $this->assertFalse($service->handle([]));

        $this->assertFalse($service->handle([
            'authority' => 'authority',
            'transaction_id' => 'transaction',
        ]));

        $this->assertFalse($service->handle([
            'authority' => 'authority',
            'amount' => 1000,
        ]));
    }

    public function test_callback_returns_false_when_payment_mark_paid_throws(): void
    {
        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $paymentService = Mockery::mock(PaymentService::class);

        $gateway
            ->shouldReceive('verify')
            ->once()
            ->with('authority')
            ->andReturn(true);

        $paymentService
            ->shouldReceive('markPaid')
            ->once()
            ->with('transaction', [
                'authority' => 'authority',
                'transaction_id' => 'transaction',
                'amount' => 1000,
            ])
            ->andThrow(new \RuntimeException('payment failure'));

        $service = new PaymentCallbackWorkflowService(
            $gateway,
            $paymentService
        );

        $this->assertFalse($service->handle([
            'authority' => 'authority',
            'transaction_id' => 'transaction',
            'amount' => 1000,
        ]));
    }
}
