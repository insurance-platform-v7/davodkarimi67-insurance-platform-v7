<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\ClaimPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ClaimSettlementService
{
    public function settle(
        Claim $claim,
        float $amount
    ): ClaimPayment {
        if ($amount <= 0) {
            throw new InvalidArgumentException(
                'Settlement amount must be greater than zero.'
            );
        }

        if (
            $claim->approved_amount !== null &&
            $amount > (float) $claim->approved_amount
        ) {
            throw new InvalidArgumentException(
                'Settlement amount cannot exceed the approved amount.'
            );
        }

        if ($claim->status === ClaimStatus::PAID) {
            throw new InvalidArgumentException(
                'Claim has already been settled.'
            );
        }

        if ($claim->status !== ClaimStatus::APPROVED) {
            throw new InvalidArgumentException(
                'Only approved claims can be settled.'
            );
        }

        return DB::transaction(function () use ($claim, $amount) {
            $payment = ClaimPayment::create([
                'tenant_id' => $claim->tenant_id,
                'claim_id' => $claim->id,
                'amount' => $amount,
                'reference_number' => 'CLP-'.strtoupper(
                    Str::random(12)
                ),
                'paid_at' => now(),
                'meta' => [],
            ]);

            $this->markClaimAsPaid(
                $claim,
                $amount
            );

            return $payment;
        });
    }

    protected function markClaimAsPaid(
        Claim $claim,
        float $amount
    ): void {
        $claim->update([
            'status' => ClaimStatus::PAID,
            'approved_amount' => $amount,
        ]);
    }
}
