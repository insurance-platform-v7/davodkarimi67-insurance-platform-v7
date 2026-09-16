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
    /**
     * @var array<string, class-string<IssuanceProviderInterface>>
     */
    private const PROVIDERS = [
        'internal' => InternalIssuanceProvider::class,
        'asia' => AsiaIssuanceProvider::class,
        'dana' => DanaIssuanceProvider::class,
        'mellat' => MellatIssuanceProvider::class,
    ];

    public function resolve(): IssuanceProviderInterface
    {
        $configuredProvider = config(
            'issuance.default_provider',
            'internal'
        );

        $provider = is_string($configuredProvider)
            ? strtolower($configuredProvider)
            : 'internal';

        $providerClass = self::PROVIDERS[$provider] ?? null;

        if ($providerClass === null) {
            throw new InvalidArgumentException(
                "Unsupported issuance provider [{$provider}]"
            );
        }

        return app($providerClass);
    }
}
