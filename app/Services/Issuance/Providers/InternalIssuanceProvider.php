<?php
// File: app/Services/Issuance/Providers/InternalIssuanceProvider.php

namespace App\Services\Issuance\Providers;

use App\Models\Policy;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use Illuminate\Support\Str;

class InternalIssuanceProvider implements IssuanceProviderInterface
{
    public function issue(Policy $policy): array
    {
        return [
            'policy_number' => $policy->policy_number ?: 'P-' . strtoupper(Str::random(12)),
            'issued_at' => now(),
            'provider' => 'internal',
        ];
    }
}
