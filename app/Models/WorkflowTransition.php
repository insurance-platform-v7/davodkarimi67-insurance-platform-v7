<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    protected $fillable = [
        'tenant_id',
        'entity_type',
        'from_state_id',
        'to_state_id',
        'action',
        'conditions',
        'side_effects',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'side_effects' => 'array',
        'is_active' => 'boolean',
    ];
}
