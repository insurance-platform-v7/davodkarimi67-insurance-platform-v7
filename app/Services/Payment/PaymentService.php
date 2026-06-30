<?php

// app/Services/Payment/PaymentService.php

namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PolicyStatus;
use App\Models\Payment;
use App\Models\Policy;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PolicyWorkflowService $workflow
    ) {}

    public function createPayment(int $policyId): Payment
    {
        return DB::transaction(function () use ($policyId) {

            $policy = Policy::query()->findOrFail($policyId);

            $payment = Payment::query()->create([
                'policy_id' => $policy->id,
                'amount' => $policy->premium ?? 0,
                'transaction_id' => (string) Str::uuid(),
                'gateway' => 'zarinpal',   // ✅ اضافه شد
                'status' => PaymentStatus::PENDING,
            ]);

            $this->workflow->markPaymentPending($policy->id);

            return $payment->refresh();
        });
    }


    public function markPaid(string $transactionId, array $callback = []): Payment
    {
        return DB::transaction(function () use ($transactionId, $callback) {
            $payment = Payment::query()
                ->where('transaction_id', $transactionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status === PaymentStatus::PAID) {
                return $payment;
            }

            $payment->update([
                'status' => PaymentStatus::PAID,
                'callback_payload' => $callback,
                'paid_at' => now(),
            ]);

            $policy = $payment->policy()->firstOrFail();

            if ($policy->status !== PolicyStatus::ISSUED) {
                $policy->update([
                    'status' => PolicyStatus::PAID,
                ]);
            }

            return $payment->refresh();
        });
    }
}
