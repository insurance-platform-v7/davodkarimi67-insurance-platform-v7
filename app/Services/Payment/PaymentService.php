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

            $gatewayResponse = $this->requestGateway($policy);
            $gatewayEnum = $this->resolveGateway();

            $payment = $this->payments->create([
                'tenant_id' => $policy->tenant_id,
                'policy_id' => $policy->id,
                'amount' => $policy->premium,
                'transaction_id' => (string) Str::uuid(),
                'authority' => $gatewayResponse['authority'],
                'gateway' => $gatewayEnum->value,
                'status' => PaymentStatus::PENDING,
            ]);

            $this->workflow->markPaymentPending(
                $policy->id
            );

            return $payment;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function requestGateway(Policy $policy): array
    {
        $response = $this->gateway->request(
            (int) $policy->premium,
            [
                'policy_id' => $policy->id,
                'tenant_id' => $policy->tenant_id,
            ]
        );

        if (($response['status'] ?? null) !== 'success') {
            throw new RuntimeException(
                'Payment gateway request failed.'
            );
        }

        if (!($response['authority'] ?? null)) {
            throw new RuntimeException(
                'Payment gateway did not return an authority.'
            );
        }

        return $response;
    }

    private function resolveGateway(): PaymentGateway
    {
        $configuredGateway = config(
            'services.payment_gateway',
            'fake'
        );

        $gatewayName = strtoupper(
            is_string($configuredGateway)
                ? $configuredGateway
                : 'fake'
        );

        $gateway = PaymentGateway::tryFrom($gatewayName);

        if ($gateway === null) {
            throw new RuntimeException(
                'Unsupported payment gateway: ' . $gatewayName
            );
        }

        return $gateway;
    }

    /**
     * @param array<string, mixed> $callback
     */
    public function markPaid(
        string $transactionId,
        array $callback = []
    ): Payment {
        /**
         * @var array{
         *     payment: Payment,
         *     policy: Policy|null,
         *     dispatch: bool
         * } $result
         */
        $result = DB::transaction(
            function () use (
                $transactionId,
                $callback
            ): array {
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
                    return [
                        'payment' => $payment,
                        'policy' => null,
                        'dispatch' => false,
                    ];
                }

                $this->validateCallback(
                    $payment,
                    $callback
                );

                $payment = $this->payments->updateStatus(
                    $payment,
                    PaymentStatus::PAID,
                    $callback
                );

                $policy = $this->workflow->markPaid(
                    $payment->policy_id
                );

                return [
                    'payment' => $payment,
                    'policy' => $policy,
                    'dispatch' => true,
                ];
            }
        );

        if ($result['dispatch']) {
            /** @var Policy $policy */
            $policy = $result['policy'];

            PaymentSucceeded::dispatch($policy);
        }

        return $result['payment'];
    }

    /**
     * @param array<string, mixed> $callback
     */
    private function validateCallback(
        Payment $payment,
        array $callback
    ): void {
        $authority = $callback['authority'] ?? null;

        if (
            !is_string($authority)
            || $authority === ''
        ) {
            throw new RuntimeException(
                'Payment callback authority is required.'
            );
        }

        if (
            !hash_equals(
                (string) $payment->authority,
                $authority
            )
        ) {
            throw new RuntimeException(
                'Payment callback authority does not match payment.'
            );
        }

        $callbackAmount = $callback['amount'] ?? null;

        if (
            !is_int($callbackAmount)
            && !is_float($callbackAmount)
            && !is_string($callbackAmount)
        ) {
            throw new RuntimeException(
                'Payment callback amount is required.'
            );
        }

        if (!is_numeric($callbackAmount)) {
            throw new RuntimeException(
                'Payment callback amount must be numeric.'
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
    }
}
