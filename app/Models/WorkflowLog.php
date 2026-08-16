<?php

namespace App\Models;

class WorkflowLog extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'entity_type',
        'entity_id',
        'from_state_id',
        'to_state_id',
        'action',
        'user_id',
        'payload',
        'note',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function fromState()
    {
        return $this->belongsTo(
            WorkflowState::class,
            'from_state_id'
        );
    }

    public function toState()
    {
        return $this->belongsTo(
            WorkflowState::class,
            'to_state_id'
        );
    }
}
