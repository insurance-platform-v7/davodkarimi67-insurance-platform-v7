<?php

namespace App\Services\Issuance\Providers;

use App\Models\Policy;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use Illuminate\Support\Str;

class DanaIssuanceProvider implements IssuanceProviderInterface
{
    public function issue(Policy $policy): array
    {
        return [
            'policy_number' => $policy->policy_number
                ?: 'DANA-' . strtoupper(Str::random(10)),
            'issued_at' => now(),
            'provider' => 'dana',
        ];
    }
}
