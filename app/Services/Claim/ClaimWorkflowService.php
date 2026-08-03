<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Models\Claim;

class ClaimWorkflowService
{
    public function submit(Claim $claim): Claim
    {
        return $this->transition($claim, ClaimStatus::SUBMITTED);
    }

    public function review(Claim $claim): Claim
    {
        return $this->transition($claim, ClaimStatus::UNDER_REVIEW);
    }

    public function approve(
        Claim $claim,
        float $approvedAmount
    ): Claim {
        return $this->transition($claim, ClaimStatus::APPROVED, [
            'approved_amount' => $approvedAmount,
        ]);
    }

    public function reject(
        Claim $claim,
        ?string $reason = null
    ): Claim {
        $meta = $claim->meta ?? [];
        $meta['rejection_reason'] = $reason;

        return $this->transition($claim, ClaimStatus::REJECTED, [
            'meta' => $meta,
        ]);
    }

    public function pay(Claim $claim): Claim
    {
        return $this->transition($claim, ClaimStatus::PAID);
    }

    protected function transition(
        Claim $claim,
        ClaimStatus $status,
        array $attributes = []
    ): Claim {
        $claim->update([
            ...$attributes,
            'status' => $status,
        ]);

        return $claim->refresh();
    }
}
