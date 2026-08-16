<?php

namespace App\Domain\Policy;

use App\Models\PolicyAuditLog;
use Illuminate\Database\Eloquent\Collection;

interface PolicyHistoryRepository
{
    public function create(
        int $policyId,
        string $action,
        array $payload = [],
        ?string $correlationId = null,
        ?string $traceId = null,
        ?string $source = null
    ): PolicyAuditLog;

    public function getHistory(int $policyId): Collection;
}
