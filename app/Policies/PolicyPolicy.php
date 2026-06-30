<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Policy;

class PolicyPolicy
{
    public function view(User $user, Policy $policy): bool
    {
        return $user->tenant_id === $policy->tenant_id;
    }
}
