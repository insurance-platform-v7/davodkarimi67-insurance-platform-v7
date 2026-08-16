<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Events\ClaimApproved;
use App\Events\ClaimPaid;
use App\Events\ClaimRejected;
use App\Models\Claim;
use RuntimeException;

class ClaimWorkflowService
{
    public function submit(Claim $claim): Claim
    {
        return $this->transition(
            $claim,
            ClaimStatus::SUBMITTED
        );
    }

    public function review(Claim $claim): Claim
    {
        return $this->transition(
            $claim,
            ClaimStatus::UNDER_REVIEW
        );
    }

    public function approve(
        Claim $claim,
        float $approvedAmount
    ): Claim {
        if ($approvedAmount < 0) {
            throw new RuntimeException(
                'Approved amount cannot be negative.'
            );
        }

        $claim = $this->transition(
            $claim,
            ClaimStatus::APPROVED,
            [
                'approved_amount' => $approvedAmount,
            ]
        );

        ClaimApproved::dispatch($claim);

        return $claim;
    }

    public function reject(
        Claim $claim,
        ?string $reason = null
    ): Claim {
        $meta = $claim->meta ?? [];

        $meta['rejection_reason'] = $reason;

        $claim = $this->transition(
            $claim,
            ClaimStatus::REJECTED,
            [
                'meta' => $meta,
            ]
        );

        ClaimRejected::dispatch($claim);

        return $claim;
    }

    public function pay(Claim $claim): Claim
    {
        $claim = $this->transition(
            $claim,
            ClaimStatus::PAID
        );

        ClaimPaid::dispatch($claim);

        return $claim;
    }

    protected function transition(
        Claim $claim,
        ClaimStatus $status,
        array $attributes = []
    ): Claim {
        $fromStatus = $claim->status;

        if ($fromStatus === $status) {
            return $claim;
        }

        if (! $this->isValidTransition(
            $fromStatus,
            $status
        )) {
            throw new RuntimeException(
                sprintf(
                    'Invalid claim transition from [%s] to [%s].',
                    $fromStatus->value,
                    $status->value
                )
            );
        }

        $claim->update([
            ...$attributes,
            'status' => $status,
        ]);

        return $claim->refresh();
    }

    protected function isValidTransition(
        ClaimStatus $from,
        ClaimStatus $to
    ): bool {
        return match ($from) {
            ClaimStatus::SUBMITTED => $to === ClaimStatus::UNDER_REVIEW,

            ClaimStatus::UNDER_REVIEW => in_array(
                $to,
                [
                    ClaimStatus::APPROVED,
                    ClaimStatus::REJECTED,
                ],
                true
            ),

            ClaimStatus::APPROVED => $to === ClaimStatus::PAID,

            ClaimStatus::REJECTED,
            ClaimStatus::PAID => false,
        };
    }
}
