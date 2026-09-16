<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    public function request(
        int $amount,
        array $meta = []
    ): array;

    public function verify(
        string $authority
    ): bool;
}
