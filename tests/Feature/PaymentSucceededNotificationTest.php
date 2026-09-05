<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\PaymentCallbackWorkflowService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaymentSucceededNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_sends_payment_sms(): void
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

        Log::spy();

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

        Log::shouldHaveReceived('info')
            ->with(
                'sms.notification',
                [
                    'mobile' => '09120000000',
                    'message' => 'Payment completed successfully.',
                ]
            )
            ->once();
    }
}
