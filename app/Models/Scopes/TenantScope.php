<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        if (app()->bound('tenant')) {
            $builder->where(
                $table . '.tenant_id',
                app('tenant')->id
            );

            return;
        }

        if (auth()->check()) {
            $builder->where(
                $table . '.tenant_id',
                auth()->user()->tenant_id
            );
        }
    }
}
