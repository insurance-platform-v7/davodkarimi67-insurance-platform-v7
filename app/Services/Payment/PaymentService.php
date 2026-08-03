<?php

namespace App\Services\Payment;

use App\Domain\Payment\PaymentRepository;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Policy;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PaymentRepository $payments,
        private PolicyWorkflowService $workflow,
    ) {}

    public function createPayment(int $policyId): Payment
    {
        return DB::transaction(function () use ($policyId): Payment {

            $policy = Policy::query()->findOrFail($policyId);

            $payment = $this->payments->create([
                'tenant_id'      => $policy->tenant_id,
                'policy_id'      => $policy->id,
                'amount'         => $policy->premium,
                'transaction_id' => (string) Str::uuid(),
                'gateway'        => 'zarinpal',
                'status'         => PaymentStatus::PENDING,
            ]);

            $this->workflow->markPaymentPending($policyId);

            return $payment;
        });
    }

    public function markPaid(
        string $transactionId,
        array $callback = []
    ): Payment {
        return DB::transaction(function () use (
            $transactionId,
            $callback
        ): Payment {

            $payment = $this->payments
                ->findByTransactionIdForUpdate($transactionId);

            if ($payment->status === PaymentStatus::PAID) {
                return $payment;
            }

            $payment = $this->payments->updateStatus(
                $payment,
                PaymentStatus::PAID,
                $callback
            );

            $this->workflow->markPaid($payment->policy_id);

            return $payment;
        });
    }
}
