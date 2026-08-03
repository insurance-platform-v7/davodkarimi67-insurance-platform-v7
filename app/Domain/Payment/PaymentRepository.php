<?php

namespace App\Domain\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;

interface PaymentRepository
{
    public function create(array $data): Payment;

    public function findByTransactionIdForUpdate(string $transactionId): Payment;

    public function updateStatus(
        Payment $payment,
        PaymentStatus $status,
        array $callbackPayload = []
    ): Payment;
}
