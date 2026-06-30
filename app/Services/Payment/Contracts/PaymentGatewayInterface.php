<?php

namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function request(int $amount, array $meta = []): array;

    public function verify(string $authority): bool;
}
