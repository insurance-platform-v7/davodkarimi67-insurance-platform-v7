<?php

// app/Services/Policy/PolicyWorkflowService.php

namespace App\Services\Policy;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

class PolicyWorkflowService
{
    public function issue(int $policyId): Policy
    {
        return DB::transaction(function () use ($policyId) {
            $policy = Policy::query()->findOrFail($policyId);

            if ($policy->status !== PolicyStatus::PAID) {
                throw new \RuntimeException('Policy is not ready for issuance.');
            }

            $policy->update([
                'status' => PolicyStatus::ISSUED,
            ]);

            return $policy->refresh();
        });
    }

    public function markPaymentPending(int $policyId): Policy
    {
        $policy = Policy::query()->findOrFail($policyId);

        $policy->update([
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        return $policy->refresh();
    }

    public function cancel(): void
    {
        //
    }

    public function expire(): void
    {
        //
    }



}
