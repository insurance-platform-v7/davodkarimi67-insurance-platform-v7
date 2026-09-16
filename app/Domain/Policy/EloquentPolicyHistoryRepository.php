<?php

namespace App\Domain\Policy;

use App\Models\PolicyAuditLog;
use Illuminate\Database\Eloquent\Collection;

class EloquentPolicyHistoryRepository implements PolicyHistoryRepository
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(
        int $policyId,
        string $action,
        array $payload = [],
        ?string $correlationId = null,
        ?string $traceId = null,
        ?string $source = null
    ): PolicyAuditLog {
        return PolicyAuditLog::create([
            'entity_type' => 'policy',
            'entity_id' => $policyId,
            'action' => $action,
            'payload' => $payload,
            'correlation_id' => $correlationId,
            'trace_id' => $traceId,
            'source' => $source ?? 'system',
        ]);
    }

    /**
     * @return Collection<int, PolicyAuditLog>
     */
    public function getHistory(int $policyId): Collection
    {
        return PolicyAuditLog::query()
            ->where('entity_type', 'policy')
            ->where('entity_id', $policyId)
            ->orderBy('created_at')
            ->get();
    }
}
