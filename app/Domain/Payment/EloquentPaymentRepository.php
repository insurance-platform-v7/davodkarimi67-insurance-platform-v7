<?php

namespace App\Domain\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;

class EloquentPaymentRepository implements PaymentRepository
{
    public function create(array $data): Payment
    {
        return Payment::query()
            ->create($data)
            ->refresh();
    }

    public function findByTransactionIdForUpdate(
        string $transactionId
    ): Payment {
        return Payment::query()
            ->where('transaction_id', $transactionId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function updateStatus(
        Payment $payment,
        PaymentStatus $status,
        array $callbackPayload = []
    ): Payment {
        $payment->update([
            'status' => $status,
            'callback_data' => $callbackPayload,
            'paid_at' => $status === PaymentStatus::PAID
                ? now()
                : $payment->paid_at,
        ]);

        return $payment->refresh();
    }
}
