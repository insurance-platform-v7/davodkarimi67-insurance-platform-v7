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

        $retention = $this->calculateRetention(
            $premium,
            (float) $contract->retention_limit
        );

        $ceded = $this->calculateCededAmount(
            $premium,
            $retention
        );

        $reinsurerShare = $this->calculateReinsurerShare(
            $ceded,
            (float) $contract->cession_rate
        );

        return [
            'premium' => $premium,
            'retention' => $retention,
            'ceded_amount' => $ceded,
            'reinsurer_share' => $reinsurerShare,
        ];
    }

    protected function calculateRetention(
        float $premium,
        float $retentionLimit
    ): float {
        return min($premium, $retentionLimit);
    }

    protected function calculateCededAmount(
        float $premium,
        float $retention
    ): float {
        return max(0, $premium - $retention);
    }

    protected function calculateReinsurerShare(
        float $ceded,
        float $cessionRate
    ): float {
        return round(
            $ceded * ($cessionRate / 100),
            2
        );
    }
}
