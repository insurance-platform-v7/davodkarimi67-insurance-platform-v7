<?php

namespace App\Domain\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;

interface PaymentRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Payment;

    public function findByTransactionIdForUpdate(
        string $transactionId
    ): Payment;

    /**
     * @param  array<string, mixed>  $callbackPayload
     */
    public function updateStatus(
        Payment $payment,
        PaymentStatus $status,
        array $callbackPayload = []
    ): Payment;
}
