<?php

namespace App\Services\Reinsurance;

use App\Models\ReinsuranceAllocation;

class ReinsuranceReportingService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $totalPremium = (float) ReinsuranceAllocation::sum('premium');

        $totalRetention = (float) ReinsuranceAllocation::sum(
            'retention'
        );

        $totalCeded = (float) ReinsuranceAllocation::sum(
            'ceded_amount'
        );

        $totalReinsurerShare = (float) ReinsuranceAllocation::sum(
            'reinsurer_share'
        );

        $count = ReinsuranceAllocation::query()->count();

        return [
            'count' => $count,
            'total_premium' => $totalPremium,
            'total_retention' => $totalRetention,
            'total_ceded' => $totalCeded,
            'total_reinsurer_share' => $totalReinsurerShare,
        ];
    }
}
