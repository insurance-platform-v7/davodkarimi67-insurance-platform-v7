<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

class OfferCacheService
{
    public function remember(string $key, callable $callback, int $ttl = 300)
    {
        $tenantId = app()->bound('tenant')
            ? app('tenant')->id
            : 'global';

        return Cache::remember(
            "tenant:{$tenantId}:offer:{$key}",
            $ttl,
            $callback
        );
    }

    public function forget(string $key): void
    {
        $tenantId = app()->bound('tenant')
            ? app('tenant')->id
            : 'global';

        Cache::forget(
            "tenant:{$tenantId}:offer:{$key}"
        );
    }
}
