<?php

namespace App\Services\Reinsurance;

use App\Models\Policy;
use App\Models\ReinsuranceAllocation;
use App\Models\ReinsuranceContract;

class ReinsuranceAllocationService
{
    public function __construct(
        protected ReinsuranceService $service
    ) {
    }

    public function allocate(
        Policy $policy,
        ReinsuranceContract $contract
    ): ReinsuranceAllocation {

        $result = $this->service->calculate(
            $policy,
            $contract
        );

        return ReinsuranceAllocation::create([
            'policy_id' => $policy->id,
            'reinsurance_contract_id' => $contract->id,
            'premium' => $result['premium'],
            'retention' => $result['retention'],
            'ceded_amount' => $result['ceded_amount'],
            'reinsurer_share' => $result['reinsurer_share'],
        ]);
    }
}
