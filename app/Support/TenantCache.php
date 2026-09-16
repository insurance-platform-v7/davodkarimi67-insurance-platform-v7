<?php

namespace App\Support;

use App\Models\Tenant;
use Closure;
use Illuminate\Support\Facades\Cache;

class TenantCache
{
    /**
     * @template TCacheValue
     *
     * @param Closure(): TCacheValue $callback
     * @return TCacheValue
     */
    public static function remember(
        string $key,
        int $seconds,
        Closure $callback
    ) {
        /** @var Tenant|null $tenant */
        $tenant = app()->bound('tenant')
            ? app('tenant')
            : null;

        /** @var int|string $tenantId */
        $tenantId = $tenant instanceof Tenant
            ? $tenant->id
            : 'global';

        return Cache::remember(
            "tenant:{$tenantId}:{$key}",
            $seconds,
            $callback
        );
    }

    public static function forget(string $key): void
    {
        /** @var Tenant|null $tenant */
        $tenant = app()->bound('tenant')
            ? app('tenant')
            : null;

        /** @var int|string $tenantId */
        $tenantId = $tenant instanceof Tenant
            ? $tenant->id
            : 'global';

        Cache::forget(
            "tenant:{$tenantId}:{$key}"
        );
    }
}
