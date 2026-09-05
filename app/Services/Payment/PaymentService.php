<?php

namespace App\Services\Payment;

use App\Domain\Payment\PaymentRepository;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Events\PaymentSucceeded;
use App\Models\Payment;
use App\Models\Policy;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private PaymentRepository $payments,
        private PolicyWorkflowService $workflow,
        private PaymentGatewayInterface $gateway,
    ) {}

    public function createPayment(int $policyId): Payment
    {
        return DB::transaction(function () use ($policyId): Payment {
            $policy = Policy::query()
                ->findOrFail($policyId);

            $gatewayResponse = $this->gateway->request(
                (int) $policy->premium,
                [
                    'policy_id' => $policy->id,
                    'tenant_id' => $policy->tenant_id,
                ]
            );

            if (($gatewayResponse['status'] ?? null) !== 'success') {
                throw new RuntimeException(
                    'Payment gateway request failed.'
                );
            }

            $authority = $gatewayResponse['authority'] ?? null;

            if (! $authority) {
                throw new RuntimeException(
                    'Payment gateway did not return an authority.'
                );
            }

            $gatewayName = strtoupper(
                (string) config(
                    'services.payment_gateway',
                    'fake'
                )
            );

            $gatewayEnum = PaymentGateway::tryFrom(
                $gatewayName
            );

            if ($gatewayEnum === null) {
                throw new RuntimeException(
                    'Unsupported payment gateway: ' . $gatewayName
                );
            }

            $payment = $this->payments->create([
                'tenant_id' => $policy->tenant_id,
                'policy_id' => $policy->id,
                'amount' => $policy->premium,
                'transaction_id' => (string) Str::uuid(),
                'authority' => $authority,
                'gateway' => $gatewayEnum->value,
                'status' => PaymentStatus::PENDING,
            ]);

            $this->workflow->markPaymentPending(
                $policy->id
            );

            return $payment;
        });
    }

    public function markPaid(
        string $transactionId,
        array $callback = []
    ): Payment {
        return DB::transaction(function () use (
            $transactionId,
            $callback
        ): Payment {
            $payment = $this->payments
                ->findByTransactionIdForUpdate(
                    $transactionId
                );

            /*
             * Idempotency:
             * A repeated callback for an already-paid payment
             * must not execute the payment workflow or event again.
             */
            if ($payment->status === PaymentStatus::PAID) {
                return $payment;
            }

            $authority = $callback['authority'] ?? null;

            if (! $authority) {
                throw new RuntimeException(
                    'Payment callback authority is required.'
                );
            }

            if (! hash_equals(
                (string) $payment->authority,
                (string) $authority
            )) {
                throw new RuntimeException(
                    'Payment callback authority does not match payment.'
                );
            }

            $callbackAmount = $callback['amount'] ?? null;

            if ($callbackAmount === null) {
                throw new RuntimeException(
                    'Payment callback amount is required.'
                );
            }

            if (
                (int) $callbackAmount
                !== (int) $payment->amount
            ) {
                throw new RuntimeException(
                    'Payment callback amount does not match payment amount.'
                );
            }

            $payment = $this->payments->updateStatus(
                $payment,
                PaymentStatus::PAID,
                $callback
            );

            $policy = $this->workflow->markPaid(
                $payment->policy_id
            );

            PaymentSucceeded::dispatch($policy);

            return $payment;
        });
    }
}
