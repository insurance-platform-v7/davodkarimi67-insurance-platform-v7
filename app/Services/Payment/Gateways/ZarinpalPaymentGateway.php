<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

class ZarinpalPaymentGateway implements PaymentGatewayInterface
{
    public function request(
        int $amount,
        array $meta = []
    ): array {
        // Mock implementation for V1.
        return [
            'status' => 'success',
            'authority' => 'ZP_' . uniqid(),
            'amount' => $amount,
        ];
    }

    public function verify(
        string $authority
    ): bool {
        return str_starts_with($authority, 'ZP_');
    }
}
