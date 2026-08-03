<?php

namespace App\Policies;

use App\Models\Policy;
use App\Models\User;

class PolicyPolicy
{
    public function view(User $user, Policy $policy): bool
    {
        return $user->tenant_id === $policy->tenant_id;
    }
}
