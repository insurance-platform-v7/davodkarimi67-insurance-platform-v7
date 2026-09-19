<?php

namespace App\Services\Cache;

use App\Models\Tenant;
use Closure;
use Illuminate\Support\Facades\Cache;

class FormulaCacheService
{
    /**
     * @param  Closure(): mixed  $callback
     */
    public function remember(
        string $key,
        Closure $callback,
        int $ttl = 300
    ): mixed {
        $tenantId = 'global';

        if (app()->bound('tenant')) {
            /** @var Tenant $tenant */
            $tenant = app('tenant');
            $tenantId = (string) $tenant->id;
        }

        return Cache::remember(
            "tenant:{$tenantId}:formula:{$key}",
            $ttl,
            $callback
        );
    }

    public function forget(string $key): void
    {
        $tenantId = 'global';

        if (app()->bound('tenant')) {
            /** @var Tenant $tenant */
            $tenant = app('tenant');
            $tenantId = (string) $tenant->id;
        }

        Cache::forget(
            "tenant:{$tenantId}:formula:{$key}"
        );
    }
}
