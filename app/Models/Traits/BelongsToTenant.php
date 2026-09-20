<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            if (
                app()->bound('tenant')
                && empty($model->getAttribute('tenant_id'))
            ) {
                $tenant = app('tenant');

                if ($tenant instanceof Tenant) {
                    $model->setAttribute(
                        'tenant_id',
                        $tenant->id
                    );
                }
            }
        });
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForTenant(
        Builder $query,
        int $tenantId
    ): Builder {
        return $query->where(
            $query->getModel()->getTable().'.tenant_id',
            $tenantId
        );
    }
}
