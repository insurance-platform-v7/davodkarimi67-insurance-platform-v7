<?php

namespace App\Domain\Policy;

use App\Enums\PolicyStatus;
use App\Models\Policy;

class EloquentPolicyRepository implements PolicyRepository
{
    public function findOrFail(int $id): Policy
    {
        return Policy::query()
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function updateStatus(
        Policy $policy,
        PolicyStatus $status
    ): Policy {
        if ($policy->status === $status) {
            return $policy;
        }

        $policy->update([
            'status' => $status,
        ]);

        return $policy->refresh();
    }
}
