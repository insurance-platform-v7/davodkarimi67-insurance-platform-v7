<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
class WorkflowLog extends BaseTenantModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'entity_type',
        'entity_id',
        'from_state_id',
        'to_state_id',
        'user_id',
        'action',
        'payload',
        'note',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
