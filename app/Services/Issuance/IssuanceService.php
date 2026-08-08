<?php

namespace App\Services\Issuance;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Audit\AuditService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IssuanceService
{
    public function __construct(
        private IssuanceProviderInterface $provider,
        private PolicyWorkflowService $workflow,
        private AuditService $audit,
    ) {}

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
