<?php

namespace Tests\Feature;

use App\Services\Cache\FormulaCacheService;
use App\Services\Cache\OfferCacheService;
use App\Services\Cache\QuoteCacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServicesTest extends TestCase
{
    public function test_formula_cache_remember_uses_global_namespace(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('tenant:global:formula:key', 120, \Mockery::type('callable'))
            ->andReturn('formula-result');

        $result = app(FormulaCacheService::class)->remember(
            'key',
            fn () => 'callback',
            120
        );

        $this->assertSame('formula-result', $result);
    }

    public function test_formula_cache_forget_uses_global_namespace(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tenant:global:formula:key')
            ->andReturn(true);

        app(FormulaCacheService::class)->forget('key');

        $this->assertTrue(true);
    }

    public function test_offer_cache_remember_and_forget_use_tenant_namespace(): void
    {
        app()->instance('tenant', (object) ['id' => 42]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('tenant:42:offer:key', 300, \Mockery::type('callable'))
            ->andReturn('offer-result');

        Cache::shouldReceive('forget')
            ->once()
            ->with('tenant:42:offer:key')
            ->andReturn(true);

        $service = app(OfferCacheService::class);

        $this->assertSame(
            'offer-result',
            $service->remember('key', fn () => 'callback')
        );

        $service->forget('key');

        $this->assertTrue(true);
    }

    public function test_quote_cache_remember_and_forget_use_tenant_namespace(): void
    {
        app()->instance('tenant', (object) ['id' => 77]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('tenant:77:quote:key', 600, \Mockery::type('callable'))
            ->andReturn('quote-result');

        Cache::shouldReceive('forget')
            ->once()
            ->with('tenant:77:quote:key')
            ->andReturn(true);

        $service = app(QuoteCacheService::class);

        $this->assertSame(
            'quote-result',
            $service->remember('key', fn () => 'callback', 600)
        );

        $service->forget('key');

        $this->assertTrue(true);
    }
}
