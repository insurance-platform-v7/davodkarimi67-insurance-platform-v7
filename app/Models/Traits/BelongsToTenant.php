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
                (int) $model->tenant_id !== (int) $tenantId
            ) {
                throw new CrossTenantAccessException();
            }

            $model->tenant_id = $tenantId;
        });

        static::saving(function ($model) {
            if (! app()->bound('tenant')) {
                return;
            }

            $tenantId = app('tenant')->id;

            if (
                ! empty($model->tenant_id) &&
                (int) $model->tenant_id !== (int) $tenantId
            ) {
                throw new CrossTenantAccessException();
            }

            if (! $model->exists || empty($model->tenant_id)) {
                $model->tenant_id = $tenantId;
            }
        });

        static::addGlobalScope(new TenantScope());
    }
}