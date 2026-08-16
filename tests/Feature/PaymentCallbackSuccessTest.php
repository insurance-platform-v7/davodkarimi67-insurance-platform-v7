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

class PaymentCallbackSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_callback_marks_payment_paid_and_policy_paid(): void
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

        $this->assertSame(
            PaymentStatus::PENDING,
            $payment->status
        );

        $callbackService = app(
            PaymentCallbackWorkflowService::class
        );

        $result = $callbackService->handle([
            'authority' => $payment->authority,
            'transaction_id' => $payment->transaction_id,
            'amount' => 1000,
        ]);

        $this->assertTrue($result);

        $payment->refresh();
        $policy->refresh();

        $this->assertSame(
            PaymentStatus::PAID,
            $payment->status
        );

        $this->assertSame(
            PolicyStatus::PAID,
            $policy->status
        );

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'policy_id' => $policy->id,
            'authority' => $payment->authority,
            'status' => PaymentStatus::PAID->value,
        ]);

        $this->assertDatabaseHas('policies', [
            'id' => $policy->id,
            'status' => PolicyStatus::PAID->value,
        ]);
    }
}
