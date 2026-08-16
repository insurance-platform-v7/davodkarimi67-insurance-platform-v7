<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Throwable;

class PaymentCallbackWorkflowService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentService $paymentService,
    ) {}

    public function handle(array $payload): bool
    {
        $authority = $payload['authority'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $amount = $payload['amount'] ?? null;

        if (! $authority || ! $transactionId || $amount === null) {
            return false;
        }

        if (! $this->gateway->verify($authority)) {
            return false;
        }

        try {
            $this->paymentService->markPaid(
                $transactionId,
                $payload
            );
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
