<?php

namespace Tests\Feature;

use App\Services\Issuance\ProviderResolver;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use Tests\TestCase;

class ProviderResolverTest extends TestCase
{
    public function test_provider_resolver()
    {
        config([
            'issuance.default_provider' => 'internal',
        ]);

        $provider = app(
            ProviderResolver::class
        )->resolve();

        $this->assertInstanceOf(
            InternalIssuanceProvider::class,
            $provider
        );
    }
}
