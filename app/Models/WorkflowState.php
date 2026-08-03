<?php

namespace App\Models;

class WorkflowState extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'entity_type',
        'name',
        'code',
        'is_initial',
        'is_final',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_initial' => 'boolean',
        'is_final' => 'boolean',
        'is_active' => 'boolean',
    ];
}
