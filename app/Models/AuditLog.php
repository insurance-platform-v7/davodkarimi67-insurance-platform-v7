<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;

class AuditLog extends BaseTenantModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
