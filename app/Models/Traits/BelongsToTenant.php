<?php

namespace App\Models\Traits;

use App\Exceptions\CrossTenantAccessException;
use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (! app()->bound('tenant')) {
                return;
            }

            $tenantId = app('tenant')->id;

            if (
                ! empty($model->tenant_id) &&
                $model->tenant_id != $tenantId
            ) {
                throw new CrossTenantAccessException();
            }

            $model->tenant_id = $tenantId;
        });

        static::saving(function ($model) {
            if (! app()->bound('tenant')) {
                return;
            }

            // فقط برای مدل‌های موجود بررسی کن
            if (! $model->exists) {
                return;
            }

            if ($model->tenant_id != app('tenant')->id) {
                throw new CrossTenantAccessException();
            }
        });

        static::addGlobalScope(new TenantScope());
    }
}
