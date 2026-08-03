<?php

namespace App\Policies;

use App\Models\User;

class TenantPolicy
{
    public function access(User $user, $model): bool
    {
        if (! isset($model->tenant_id)) {
            return true;
        }

        return $user->tenant_id === $model->tenant_id;
    }
}
