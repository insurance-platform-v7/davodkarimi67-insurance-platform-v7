<?php

namespace App\Services\Workflow;

use App\Models\WorkflowLog;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Exception;
use Illuminate\Database\Eloquent\Model;

class WorkflowEngine
{
    public function transition(Model $model, string $toStateCode): void
    {
        $currentState = $model->status;

        $fromState = WorkflowState::where('code', $currentState)->first();
        $toState = WorkflowState::where('code', $toStateCode)->first();

        if (! $fromState || ! $toState) {
            throw new Exception('Workflow state not found');
        }

        $transition = WorkflowTransition::where('from_state_id', $fromState->id)
            ->where('to_state_id', $toState->id)
            ->first();

        if (! $transition) {
            throw new Exception('Invalid workflow transition');
        }

        $model->update([
            'status' => $toState->code,
        ]);

        WorkflowLog::create([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'from_state_id' => $fromState->id,
            'to_state_id' => $toState->id,
        ]);
    }
}
