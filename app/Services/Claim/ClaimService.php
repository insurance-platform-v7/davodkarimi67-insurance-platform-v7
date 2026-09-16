<?php

namespace App\Services\Claim;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Policy;
use Illuminate\Support\Str;

class ClaimService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(
        Policy $policy,
        array $data
    ): Claim {
        return Claim::create([
            'tenant_id' => $policy->tenant_id,
            'policy_id' => $policy->id,

            'claim_number' => 'CLM-'.strtoupper(
                    Str::random(10)
                ),

            'status' => ClaimStatus::SUBMITTED,

            'requested_amount' => $data['requested_amount'] ?? null,

            'description' => $data['description'] ?? null,

            'meta' => $data['meta'] ?? [],
        ]);
    }
}
