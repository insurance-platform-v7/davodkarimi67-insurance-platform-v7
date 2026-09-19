<?php

namespace App\Domain\Policy;

use App\Models\PolicyAuditLog;
use Illuminate\Database\Eloquent\Collection;

interface PolicyHistoryRepository
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(
        int $policyId,
        string $action,
        array $payload = [],
        ?string $correlationId = null,
        ?string $traceId = null,
        ?string $source = null
    ): PolicyAuditLog;

    /**
     * @return Collection<int, PolicyAuditLog>
     */
    public function getHistory(int $policyId): Collection;
}
