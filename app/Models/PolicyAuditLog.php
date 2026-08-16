<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyAuditLog extends BaseTenantModel
{
    use HasFactory;

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

    protected $casts = [
        'payload' => 'array',
    ];
}
