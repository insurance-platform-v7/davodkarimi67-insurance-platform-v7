<?php

namespace App\Services\Broker;

use App\Models\Broker;
use App\Models\BrokerCommission;
use App\Models\Policy;

class CommissionService
{
    public function calculate(
        Broker $broker,
        Policy $policy
    ): BrokerCommission {

        $premium = (float) $policy->premium;

        $rate = (float) $broker->commission_rate;

        $commission = round(
            $premium * ($rate / 100),
            2
        );

        return BrokerCommission::create([
            'broker_id' => $broker->id,
            'policy_id' => $policy->id,
            'premium' => $premium,
            'rate' => $rate,
            'commission_amount' => $commission,
        ]);
    }
}
