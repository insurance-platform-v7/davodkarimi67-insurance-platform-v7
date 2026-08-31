<?php

namespace App\Services\Broker;

use App\Models\Broker;
use App\Models\BrokerCommission;
use App\Models\Policy;
use App\Models\Tenant;
use RuntimeException;

class CommissionService
{
    public function calculate(
        Broker $broker,
        Policy $policy
    ): BrokerCommission {
        $tenant = app()->bound('tenant')
            ? app('tenant')
            : null;

        if (!$tenant instanceof Tenant) {
            throw new RuntimeException('Tenant context is required.');
        }

        $premium = (float) $policy->premium;

        $rate = (float) $broker->commission_rate;

        $commission = round(
            $premium * ($rate / 100),
            2
        );

        return BrokerCommission::create([
            'tenant_id' => $tenant->id,
            'broker_id' => $broker->id,
            'policy_id' => $policy->id,
            'premium' => $premium,
            'rate' => $rate,
            'commission_amount' => $commission,
        ]);
    }
}
