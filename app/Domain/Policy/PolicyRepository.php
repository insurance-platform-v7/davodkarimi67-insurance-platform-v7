<?php

namespace App\Domain\Policy;

use App\Enums\PolicyStatus;
use App\Models\Policy;

interface PolicyRepository
{
    public function findOrFail(int $id): Policy;

    public function updateStatus(
        Policy $policy,
        PolicyStatus $status
    ): Policy;
}
