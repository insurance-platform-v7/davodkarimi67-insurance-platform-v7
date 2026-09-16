<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TenantPolicy
{
    public function access(User $user, Model $model): bool
    {
        $tenantId = $model->getAttribute('tenant_id');

        if ($tenantId === null) {
            return true;
        }

        return $user->tenant_id === $tenantId;
    }
}
