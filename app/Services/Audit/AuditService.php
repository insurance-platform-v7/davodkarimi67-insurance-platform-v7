<?php

namespace App\Services\Audit;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function log(
        string $entityType,
        int $entityId,
        string $action,
        array $payload = [],
        ?string $correlationId = null,
        ?string $traceId = null,
        ?string $source = null
    ): void {
        /** @var Tenant $tenant */
        $tenant = app('tenant');

        DB::table('policy_audit_logs')->insert([
            'tenant_id' => $tenant->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'payload' => json_encode($payload),

            'correlation_id' => $correlationId
                ?: (string) Str::uuid(),

            'trace_id' => $traceId
                ?: (string) Str::uuid(),

            'source' => $source
                ?: 'system',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
