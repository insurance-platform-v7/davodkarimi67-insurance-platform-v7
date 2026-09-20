<?php

namespace App\Services\Issuance\Providers;

use App\Models\Policy;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use Illuminate\Support\Str;

class InternalIssuanceProvider implements IssuanceProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function issue(Policy $policy): array
    {
        $policyNumber = $policy->policy_number;

        if (blank($policyNumber)) {
            $policyNumber = 'P-'.strtoupper(Str::random(12));
        }

        return [
            'policy_number' => $policyNumber,
            'issued_at' => now(),
            'provider' => 'internal',
        ];
    }
}
