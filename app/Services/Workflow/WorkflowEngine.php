<?php

namespace App\Services\Workflow;

use App\Models\WorkflowLog;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkflowEngine
{
    public function transition(Model $model, string $toStateCode): void
    {
        DB::transaction(function () use ($model, $toStateCode): void {
            $tenantId = $model->tenant_id;
            $entityType = $this->entityType($model);

            $currentState = $model->getRawOriginal('status');

            if (
                is_object($model->status)
                && property_exists($model->status, 'value')
            ) {
                $currentState = $model->status->value;
            }

            $fromState = WorkflowState::query()
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $entityType)
                ->where('code', $currentState)
                ->where('is_active', true)
                ->first();

            $toState = WorkflowState::query()
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $entityType)
                ->where('code', $toStateCode)
                ->where('is_active', true)
                ->first();

            if (! $fromState || ! $toState) {
                throw new Exception('Workflow state not found');
            }

            $transition = WorkflowTransition::query()
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $entityType)
                ->where('from_state_id', $fromState->id)
                ->where('to_state_id', $toState->id)
                ->where('is_active', true)
                ->first();

            if (! $transition) {
                throw new Exception('Invalid workflow transition');
            }

            // Conditions MUST pass before state changes.
            $this->validateConditions(
                $model,
                $transition->conditions ?? []
            );

            // Change workflow state.
            $model->update([
                'status' => $toState->code,
            ]);

            // Apply side effects inside the same transaction.
            $this->applySideEffects(
                $model,
                $transition->side_effects ?? []
            );

            // Write workflow history.
            WorkflowLog::create([
                'tenant_id' => $tenantId,
                'entity_type' => $entityType,
                'entity_id' => $model->id,
                'from_state_id' => $fromState->id,
                'to_state_id' => $toState->id,
                'action' => $transition->action,
                'user_id' => auth()->id(),
                'payload' => [
                    'transition_id' => $transition->id,
                    'from' => $fromState->code,
                    'to' => $toState->code,
                ],
            ]);
        });
    }

    /**
     * Conditions format:
     *
     * [
     *     'premium' => [
     *         'min' => 1000,
     *     ],
     * ]
     */
    private function validateConditions(
        Model $model,
        array $conditions
    ): void {
        foreach ($conditions as $field => $rules) {
            if (! is_array($rules)) {
                continue;
            }

            $actual = data_get($model, $field);

            foreach ($rules as $operator => $expected) {
                $passed = match ($operator) {
                    'min' => $actual >= $expected,
                    'max' => $actual <= $expected,

                    'eq', '=' => $actual == $expected,
                    'neq', '!=' => $actual != $expected,

                    'gt', '>' => $actual > $expected,
                    'gte', '>=' => $actual >= $expected,

                    'lt', '<' => $actual < $expected,
                    'lte', '<=' => $actual <= $expected,

                    'in' => in_array(
                        $actual,
                        (array) $expected,
                        true
                    ),

                    'not_in' => ! in_array(
                        $actual,
                        (array) $expected,
                        true
                    ),

                    'exists' => $expected
                        ? ! is_null($actual)
                        : is_null($actual),

                    default => throw new Exception(
                        "Unsupported workflow condition operator: {$operator}"
                    ),
                };

                if (! $passed) {
                    throw new Exception(
                        "Workflow condition failed for field: {$field}"
                    );
                }
            }
        }
    }

    /**
     * Side effects format:
     *
     * [
     *     [
     *         'type' => 'set',
     *         'field' => 'policy_number',
     *         'value' => 'POL-TEST-001',
     *     ],
     *
     *     [
     *         'type' => 'merge_meta',
     *         'key' => 'workflow_test',
     *         'value' => true,
     *     ],
     * ]
     */
    private function applySideEffects(
        Model $model,
        array $sideEffects
    ): void {
        foreach ($sideEffects as $effect) {
            $type = $effect['type'] ?? null;

            if (! $type) {
                continue;
            }

            match ($type) {
                'set' => $this->applySetEffect(
                    $model,
                    $effect
                ),

                'merge_meta' => $this->applyMergeMetaEffect(
                    $model,
                    $effect
                ),

                default => throw new Exception(
                    "Unsupported workflow side effect: {$type}"
                ),
            };
        }
    }

    private function applySetEffect(
        Model $model,
        array $effect
    ): void {
        $field = $effect['field'] ?? null;

        if (! $field) {
            throw new Exception(
                'Workflow set side effect requires a field.'
            );
        }

        $model->update([
            $field => $effect['value'] ?? null,
        ]);
    }

    private function applyMergeMetaEffect(
        Model $model,
        array $effect
    ): void {
        $key = $effect['key'] ?? null;

        if (! $key) {
            throw new Exception(
                'Workflow merge_meta side effect requires a key.'
            );
        }

        $currentMeta = $model->meta ?? [];

        if (! is_array($currentMeta)) {
            $currentMeta = [];
        }

        $currentMeta[$key] = $effect['value'] ?? null;

        $model->update([
            'meta' => $currentMeta,
        ]);
    }

    private function entityType(Model $model): string
    {
        return match (class_basename($model)) {
            'Policy' => 'policy',
            'Quote' => 'quote',
            'Claim' => 'claim',
            default => strtolower(class_basename($model)),
        };
    }
}
