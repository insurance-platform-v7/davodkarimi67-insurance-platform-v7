<?php

namespace App\Services\Policy;

use App\Models\Policy;
use Carbon\Carbon;

class RenewalService
{
    public function isEligible(
        Policy $policy
    ): bool {
        if (! $policy->ends_at) {
            return false;
        }

        return Carbon::now()
            ->diffInDays(
                $policy->ends_at,
                false
            ) <= 30;
    }

    /**
     * @return array<string, mixed>
     */
    public function createRenewalQuote(
        Policy $policy
    ): array {
        return [
            'policy_id' => $policy->id,
            'premium' => $policy->premium,
            'starts_at' => $policy->ends_at,
            'renewal' => true,
        ];
    }
}
