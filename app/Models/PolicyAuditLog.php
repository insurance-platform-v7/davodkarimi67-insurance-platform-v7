<?php

namespace App\Models;


class PolicyAuditLog extends BaseTenantModel
{

    protected $table = 'policy_audit_logs';

    protected $fillable = [
        'tenant_id',
        'entity_type',
        'entity_id',
        'action',
        'payload',
        'correlation_id',
        'trace_id',
        'source',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'payload' => 'array',
    ];
}
