<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class TenantCache
{
    public static function remember(
        string $key,
        int $seconds,
        Closure $callback
    ) {
        $tenantId = app()->bound('tenant')
            ? app('tenant')->id
            : 'global';

        return Cache::remember(
            "tenant:{$tenantId}:{$key}",
            $seconds,
            $callback
        );
    }

    public static function forget(string $key): void
    {
        $tenantId = app()->bound('tenant')
            ? app('tenant')->id
            : 'global';

        Cache::forget(
            "tenant:{$tenantId}:{$key}"
        );
    }
}
