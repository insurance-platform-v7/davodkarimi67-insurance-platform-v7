<?php

namespace App\Services\Issuance;

use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Issuance\Providers\AsiaIssuanceProvider;
use App\Services\Issuance\Providers\DanaIssuanceProvider;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use App\Services\Issuance\Providers\MellatIssuanceProvider;
use InvalidArgumentException;

class ProviderResolver
{
    public function resolve(): IssuanceProviderInterface
    {
        $provider = config(
            'issuance.default_provider',
            'internal'
        );

        return match ($provider) {

            'internal' => app(
                InternalIssuanceProvider::class
            ),

            'asia' => app(
                AsiaIssuanceProvider::class
            ),

            'dana' => app(
                DanaIssuanceProvider::class
            ),

            'mellat' => app(
                MellatIssuanceProvider::class
            ),

            default => throw new InvalidArgumentException(
                "Unsupported issuance provider [{$provider}]"
            ),
        };
    }
}
