<?php

namespace App\Services\Actuarial;

use App\Models\Policy;
use App\Models\Reserve;

class ReserveCalculationService
{
    public function calculate(
        Policy $policy
    ): Reserve {
        $reserveAmount = $this->calculateReserveAmount($policy);

        return Reserve::create([
            'tenant_id' => $policy->tenant_id,
            'policy_id' => $policy->id,
            'reserve_amount' => $reserveAmount,
            'reserve_type' => 'best_estimate',
            'valuation_date' => now()->toDateString(),
            'meta' => [],
        ]);
    }

    protected function calculateReserveAmount(
        Policy $policy
    ): float {
        return round(
            (float) $policy->premium * 0.15,
            2
        );
    }
}
