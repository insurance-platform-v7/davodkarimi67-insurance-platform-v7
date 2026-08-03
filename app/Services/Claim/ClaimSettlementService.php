<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\ClaimPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClaimSettlementService
{
    public function settle(
        Claim $claim,
        float $amount
    ): ClaimPayment {

        return DB::transaction(function () use ($claim, $amount) {

            $payment = ClaimPayment::create([
                'claim_id' => $claim->id,
                'amount' => $amount,
                'reference_number' => 'CLP-'.strtoupper(Str::random(12)),
                'paid_at' => now(),
                'meta' => [],
            ]);

            $this->markClaimAsPaid($claim, $amount);

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
