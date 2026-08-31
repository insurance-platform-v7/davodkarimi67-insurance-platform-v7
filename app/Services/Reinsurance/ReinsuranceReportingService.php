<?php

namespace App\Services\Reinsurance;

use App\Models\ReinsuranceAllocation;

class ReinsuranceReportingService
{
    public function summary(): array
    {
        return [
            'total_premium' => $this->totalPremium(),
            'total_retention' => $this->totalRetention(),
            'total_ceded' => $this->totalCeded(),
            'total_reinsurer_share' => $this->totalReinsurerShare(),
        ];
    }

    protected function totalPremium(): float
    {
        return (float) ReinsuranceAllocation::query()
            ->sum('premium');
    }

    protected function totalRetention(): float
    {
        return (float) ReinsuranceAllocation::query()
            ->sum('retention');
    }

    protected function totalCeded(): float
    {
        return (float) ReinsuranceAllocation::query()
            ->sum('ceded_amount');
    }

    protected function totalReinsurerShare(): float
    {
        return (float) ReinsuranceAllocation::query()
            ->sum('reinsurer_share');
    }
}
