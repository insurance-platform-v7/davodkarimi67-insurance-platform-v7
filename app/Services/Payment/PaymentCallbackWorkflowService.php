<?php
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PaymentCallbackWorkflowService
{
    public function __construct(
        private PaymentGatewayInterface $gateway
    ) {}

    public function handle(array $payload): bool
    {
        $authority = $payload['authority'] ?? null;

        if (!$authority) {
            return false;
        }

        if (!$this->gateway->verify($authority)) {
            return false;
        }

        // ادامه workflow پرداخت موفق
        return true;
    }
}
