<?php

namespace App\Services\Actuarial;

use App\Models\Policy;
use App\Models\Reserve;

class ReserveCalculationService
{
    public function calculate(
        Policy $policy
    ): Reserve {

        $reserveAmount =
            round(
                ((float) $policy->premium) * 0.15,
                2
            );

        return Reserve::create([
            'policy_id' => $policy->id,
            'reserve_amount' => $reserveAmount,
            'reserve_type' => 'best_estimate',
            'valuation_date' => now()->toDateString(),
            'meta' => [],
        ]);
    }
}
