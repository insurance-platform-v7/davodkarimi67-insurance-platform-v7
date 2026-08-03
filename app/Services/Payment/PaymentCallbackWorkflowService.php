<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;
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

        if (! $authority) {
            return false;
        }

        if (! $this->gateway->verify($authority)) {
            return false;
        }

        $transactionId = $payload['transaction_id'] ?? null;

        if (! $transactionId) {
            return false;
        }

        try {
            $this->paymentService->markPaid(
                $transactionId,
                $payload
            );
        } catch (
            ModelNotFoundException|
            RuntimeException|
            Throwable
        ) {
            return false;
        }

        return true;
    }
}
