<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Models\Claim;

class ClaimWorkflowService
{
    public function submit(Claim $claim): Claim
    {
        $claim->update([
            'status' => ClaimStatus::SUBMITTED,
        ]);

        return $claim->refresh();
    }

    public function review(Claim $claim): Claim
    {
        $claim->update([
            'status' => ClaimStatus::UNDER_REVIEW,
        ]);

        return $claim->refresh();
    }

    public function approve(
        Claim $claim,
        float $approvedAmount
    ): Claim {

        $claim->update([
            'status' => ClaimStatus::APPROVED,
            'approved_amount' => $approvedAmount,
        ]);

        return $claim->refresh();
    }

    public function reject(
        Claim $claim,
        string $reason = null
    ): Claim {

        $meta = $claim->meta ?? [];

        $meta['rejection_reason'] = $reason;

        $claim->update([
            'status' => ClaimStatus::REJECTED,
            'meta' => $meta,
        ]);

        return $claim->refresh();
    }

    public function pay(Claim $claim): Claim
    {
        $claim->update([
            'status' => ClaimStatus::PAID,
        ]);

        return $claim->refresh();
    }
}
