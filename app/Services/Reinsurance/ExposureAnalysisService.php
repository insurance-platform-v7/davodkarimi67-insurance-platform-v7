<?php

namespace App\Services\Reinsurance;

use App\Models\ReinsuranceAllocation;

class ExposureAnalysisService
{
    /**
     * @return array<string, float>
     */
    public function analyze(): array
    {
        $totalPremium =
            (float) ReinsuranceAllocation::sum(
                'premium'
            );

        $totalRetention =
            (float) ReinsuranceAllocation::sum(
                'retention'
            );

        $exposure =
            max(
                0,
                $totalPremium - $totalRetention
            );

        return [
            'total_premium' => $totalPremium,
            'total_retention' => $totalRetention,
            'exposure' => $exposure,
        ];
    }
}
