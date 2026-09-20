<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;

class FakePaymentGateway implements PaymentGatewayInterface
{
    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function request(
        int $amount,
        array $meta = []
    ): array {
        return [
            'status' => 'success',
            'authority' => uniqid('fake_'),
            'amount' => $amount,
        ];
    }

    public function verify(
        string $authority
    ): bool {
        return str_starts_with($authority, 'fake_');
    }
}
