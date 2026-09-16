<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @return BelongsTo<WorkflowState, $this>
     */
    public function fromState(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowState::class,
            'from_state_id'
        );
    }

    /**
     * @return BelongsTo<WorkflowState, $this>
     */
    public function toState(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowState::class,
            'to_state_id'
        );
    }
}
