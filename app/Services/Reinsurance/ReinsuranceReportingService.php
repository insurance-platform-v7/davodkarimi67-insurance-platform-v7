<?php

namespace App\Services\Reinsurance;

use App\Models\ReinsuranceAllocation;

class ReinsuranceReportingService
{
    public function summary(): array
    {
        $allocations = ReinsuranceAllocation::query();

        return [
            'total_premium' =>
                (float) $allocations->sum('premium'),

            'total_retention' =>
                (float) ReinsuranceAllocation::sum('retention'),

            'total_ceded' =>
                (float) ReinsuranceAllocation::sum('ceded_amount'),

            'total_reinsurer_share' =>
                (float) ReinsuranceAllocation::sum(
                    'reinsurer_share'
                ),
        ];
    }
}
