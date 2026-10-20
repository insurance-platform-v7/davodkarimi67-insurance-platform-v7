<?php

namespace Tests\Feature;

use App\Services\Issuance\ProviderResolver;
use App\Services\Issuance\Providers\AsiaIssuanceProvider;
use App\Services\Issuance\Providers\DanaIssuanceProvider;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use App\Services\Issuance\Providers\MellatIssuanceProvider;
use InvalidArgumentException;
use Tests\TestCase;

class ProviderResolverTest extends TestCase
{
    public function test_resolves_internal_provider(): void
    {
        config(['issuance.default_provider' => 'internal']);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(InternalIssuanceProvider::class, $provider);
    }

    public function test_resolves_asia_provider(): void
    {
        config(['issuance.default_provider' => 'asia']);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(AsiaIssuanceProvider::class, $provider);
    }

    public function test_resolves_dana_provider(): void
    {
        config(['issuance.default_provider' => 'dana']);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(DanaIssuanceProvider::class, $provider);
    }

    public function test_resolves_mellat_provider(): void
    {
        config(['issuance.default_provider' => 'mellat']);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(MellatIssuanceProvider::class, $provider);
    }

    public function test_provider_name_is_case_insensitive(): void
    {
        config(['issuance.default_provider' => 'INTERNAL']);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(InternalIssuanceProvider::class, $provider);
    }

    public function test_unsupported_provider_throws_exception(): void
    {
        config(['issuance.default_provider' => 'unsupported']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unsupported issuance provider [unsupported]'
        );

        app(ProviderResolver::class)->resolve();
    }

    public function test_non_string_provider_defaults_to_internal(): void
    {
        config(['issuance.default_provider' => null]);

        $provider = app(ProviderResolver::class)->resolve();

        $this->assertInstanceOf(InternalIssuanceProvider::class, $provider);
    }
}
