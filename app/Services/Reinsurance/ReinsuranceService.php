<?php

namespace App\Services\Reinsurance;

use App\Models\Policy;
use App\Models\ReinsuranceContract;

class ReinsuranceService
{
    public function calculate(
        Policy $policy,
        ReinsuranceContract $contract
    ): array {
        $premium = (float) $policy->premium;

        $retention = min(
            $premium,
            (float) $contract->retention_limit
        );

        $ceded = max(0, $premium - $retention);

        $reinsurerShare = round(
            $ceded * ((float) $contract->cession_rate / 100),
            2
        );

        return [
            'premium' => $premium,
            'retention' => $retention,
            'ceded_amount' => $ceded,
            'reinsurer_share' => $reinsurerShare,
        ];
    }
}
