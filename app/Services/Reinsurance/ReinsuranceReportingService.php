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
        $count = ReinsuranceAllocation::query()->count();
        $totalPremium = (float) ReinsuranceAllocation::query()->sum('premium');
        $totalRetention = (float) ReinsuranceAllocation::query()->sum('retention');
        $totalCeded = (float) ReinsuranceAllocation::query()->sum('ceded_amount');
        $totalReinsurerShare = (float) ReinsuranceAllocation::query()->sum('reinsurer_share');

        return [
            'count' => $count,
            'total_premium' => $totalPremium,
            'total_retention' => $totalRetention,
            'total_ceded' => $totalCeded,
            'total_reinsurer_share' => $totalReinsurerShare,
        ];
    }
}
