<?php

namespace Tests\Feature;

use App\Domain\Payment\PaymentRepository;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\PaymentService;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class PaymentServiceCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_payment_rejects_failed_gateway_request(): void
    {
        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldReceive('request')->once()->andReturn([
            'status' => 'failed',
        ]);

        $workflow = Mockery::mock(PolicyWorkflowService::class);

        $service = new PaymentService(
            app(PaymentRepository::class),
            $workflow,
            $gateway
        );

        $policy = Policy::factory()->create([
            'premium' => 1000,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment gateway request failed.');

        $service->createPayment($policy->id);
    }

    public function test_create_payment_rejects_missing_authority(): void
    {
        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldReceive('request')->once()->andReturn([
            'status' => 'success',
        ]);

        $workflow = Mockery::mock(PolicyWorkflowService::class);

        $service = new PaymentService(
            app(PaymentRepository::class),
            $workflow,
            $gateway
        );

        $policy = Policy::factory()->create([
            'premium' => 1000,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment gateway did not return an authority.');

        $service->createPayment($policy->id);
    }

    public function test_create_payment_rejects_unsupported_gateway(): void
    {
        config(['services.payment_gateway' => 'unsupported']);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $gateway->shouldReceive('request')->once()->andReturn([
            'status' => 'success',
            'authority' => 'AUTH-1',
        ]);

        $workflow = Mockery::mock(PolicyWorkflowService::class);

        $service = new PaymentService(
            app(PaymentRepository::class),
            $workflow,
            $gateway
        );

        $policy = Policy::factory()->create([
            'premium' => 1000,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported payment gateway: UNSUPPORTED');

        $service->createPayment($policy->id);
    }

    public function test_mark_paid_rejects_missing_callback_authority(): void
    {
        $payment = Payment::query()->create([
            'tenant_id' => Tenant::factory()->create()->id,
            'policy_id' => Policy::factory()->create()->id,
            'transaction_id' => 'TXN-'.uniqid(),
            'gateway' => 'FAKE',
            'status' => PaymentStatus::PENDING,
            'authority' => 'AUTH-1',
            'amount' => 1000,
        ]);

        $service = app(PaymentService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment callback authority is required.');

        $service->markPaid($payment->transaction_id, [
            'amount' => 1000,
        ]);
    }

    public function test_mark_paid_rejects_mismatched_callback_authority(): void
    {
        $payment = Payment::query()->create([
            'tenant_id' => Tenant::factory()->create()->id,
            'policy_id' => Policy::factory()->create()->id,
            'transaction_id' => 'TXN-'.uniqid(),
            'gateway' => 'FAKE',
            'status' => PaymentStatus::PENDING,
            'authority' => 'AUTH-1',
            'amount' => 1000,
        ]);

        $service = app(PaymentService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment callback authority does not match payment.');

        $service->markPaid($payment->transaction_id, [
            'authority' => 'AUTH-2',
            'amount' => 1000,
        ]);
    }

    public function test_mark_paid_rejects_missing_callback_amount(): void
    {
        $payment = Payment::query()->create([
            'tenant_id' => Tenant::factory()->create()->id,
            'policy_id' => Policy::factory()->create()->id,
            'transaction_id' => 'TXN-'.uniqid(),
            'gateway' => 'FAKE',
            'status' => PaymentStatus::PENDING,
            'authority' => 'AUTH-1',
            'amount' => 1000,
        ]);

        $service = app(PaymentService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment callback amount is required.');

        $service->markPaid($payment->transaction_id, [
            'authority' => 'AUTH-1',
        ]);
    }

    public function test_mark_paid_rejects_mismatched_callback_amount(): void
    {
        $payment = Payment::query()->create([
            'tenant_id' => Tenant::factory()->create()->id,
            'policy_id' => Policy::factory()->create()->id,
            'transaction_id' => 'TXN-'.uniqid(),
            'gateway' => 'FAKE',
            'status' => PaymentStatus::PENDING,
            'authority' => 'AUTH-1',
            'amount' => 1000,
        ]);

        $service = app(PaymentService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment callback amount does not match payment amount.');

        $service->markPaid($payment->transaction_id, [
            'authority' => 'AUTH-1',
            'amount' => 999,
        ]);
    }
}
