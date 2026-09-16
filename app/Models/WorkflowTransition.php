<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransition extends BaseTenantModel
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
