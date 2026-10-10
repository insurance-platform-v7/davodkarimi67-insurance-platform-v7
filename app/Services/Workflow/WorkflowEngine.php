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
            $this->performTransition($model, $toStateCode);
        });
    }

    private function performTransition(
        Model $model,
        string $toStateCode
    ): void {
        $tenantId = $model->getAttribute('tenant_id');
        $entityType = $this->entityType($model);
        $currentState = $this->resolveCurrentState($model);

        $fromState = $this->findState(
            $tenantId,
            $entityType,
            $currentState
        );

        $toState = $this->findState(
            $tenantId,
            $entityType,
            $toStateCode
        );

        $transition = $this->findTransition(
            $tenantId,
            $entityType,
            $fromState->id,
            $toState->id
        );

        $conditions = $this->normalizeConditions(
            $transition->conditions
        );

        $this->validateConditions($model, $conditions);

        $model->update([
            'status' => $toState->code,
        ]);

        $sideEffects = $this->normalizeSideEffects(
            $transition->side_effects
        );

        $this->applySideEffects($model, $sideEffects);

        $this->createWorkflowLog(
            $model,
            $tenantId,
            $entityType,
            $fromState,
            $toState,
            $transition
        );
    }

    private function resolveCurrentState(Model $model): string|int
    {
        $currentState = $model->getRawOriginal('status');
        $status = $model->getAttribute('status');

        if (is_object($status) && property_exists($status, 'value')) {
            $currentState = $status->value;
        }

        if (! is_string($currentState) && ! is_int($currentState)) {
            throw new Exception('Current workflow state is invalid.');
        }

        return $currentState;
    }

    private function findState(
        mixed $tenantId,
        string $entityType,
        string|int $stateCode
    ): WorkflowState {
        $state = WorkflowState::query()
            ->where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->where('code', $stateCode)
            ->where('is_active', true)
            ->first();

        if ($state === null) {
            throw new Exception('Workflow state not found');
        }

        return $state;
    }

    private function findTransition(
        mixed $tenantId,
        string $entityType,
        int $fromStateId,
        int $toStateId
    ): WorkflowTransition {
        $transition = WorkflowTransition::query()
            ->where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->where('from_state_id', $fromStateId)
            ->where('to_state_id', $toStateId)
            ->where('is_active', true)
            ->first();

        if ($transition === null) {
            throw new Exception('Invalid workflow transition');
        }

        return $transition;
    }

    private function createWorkflowLog(
        Model $model,
        mixed $tenantId,
        string $entityType,
        WorkflowState $fromState,
        WorkflowState $toState,
        WorkflowTransition $transition
    ): void {
        WorkflowLog::create([
            'tenant_id' => $tenantId,
            'entity_type' => $entityType,
            'entity_id' => $model->getKey(),
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
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function normalizeConditions(mixed $conditions): array
    {
        if (! is_array($conditions)) {
            return [];
        }

        $result = [];

        foreach ($conditions as $field => $rules) {
            if (! is_string($field) || ! is_array($rules)) {
                continue;
            }

            $result[$field] = $this->normalizeRules($rules);
        }

        return $result;
    }

    /**
     * @param  array<mixed, mixed>  $rules
     * @return array<string, mixed>
     */
    private function normalizeRules(array $rules): array
    {
        $normalizedRules = [];

        foreach ($rules as $operator => $expected) {
            if (! is_string($operator)) {
                continue;
            }

            $normalizedRules[$operator] = $expected;
        }

        return $normalizedRules;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeSideEffects(mixed $sideEffects): array
    {
        if (! is_array($sideEffects)) {
            return [];
        }

        $result = [];

        foreach ($sideEffects as $effect) {
            if (! is_array($effect)) {
                continue;
            }

            $result[] = $this->normalizeEffect($effect);
        }

        return $result;
    }

    /**
     * @param  array<mixed, mixed>  $effect
     * @return array<string, mixed>
     */
    private function normalizeEffect(array $effect): array
    {
        $normalizedEffect = [];

        foreach ($effect as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $normalizedEffect[$key] = $value;
        }

        return $normalizedEffect;
    }

    /**
     * @param  array<string, array<string, mixed>>  $conditions
     */
    private function validateConditions(
        Model $model,
        array $conditions
    ): void {
        foreach ($conditions as $field => $rules) {
            $actual = data_get($model, $field);

            foreach ($rules as $operator => $expected) {
                $this->validateCondition(
                    $actual,
                    $operator,
                    $expected,
                    $field
                );
            }
        }
    }

    private function validateCondition(
        mixed $actual,
        string $operator,
        mixed $expected,
        string $field
    ): void {
        if (! $this->evaluateCondition($actual, $operator, $expected)) {
            throw new Exception(
                "Workflow condition failed for field: {$field}"
            );
        }
    }

    private function evaluateCondition(
        mixed $actual,
        string $operator,
        mixed $expected
    ): bool {
        return $this->evaluateOperator(
            $actual,
            $operator,
            $expected
        );
    }

    private function evaluateOperator(
        mixed $actual,
        string $operator,
        mixed $expected
    ): bool {
        $operators = [
            'min' => fn (): bool => $actual >= $expected,
            'max' => fn (): bool => $actual <= $expected,
            'eq' => fn (): bool => $actual == $expected,
            '=' => fn (): bool => $actual == $expected,
            'neq' => fn (): bool => $actual != $expected,
            '!=' => fn (): bool => $actual != $expected,
            'gt' => fn (): bool => $actual > $expected,
            '>' => fn (): bool => $actual > $expected,
            'gte' => fn (): bool => $actual >= $expected,
            '>=' => fn (): bool => $actual >= $expected,
            'lt' => fn (): bool => $actual < $expected,
            '<' => fn (): bool => $actual < $expected,
            'lte' => fn (): bool => $actual <= $expected,
            '<=' => fn (): bool => $actual <= $expected,
            'in' => fn (): bool => in_array(
                $actual,
                (array) $expected,
                true
            ),
            'not_in' => fn (): bool => ! in_array(
                $actual,
                (array) $expected,
                true
            ),
            'exists' => fn (): bool => $this->evaluateExists(
                $actual,
                $expected
            ),
        ];

        if (! array_key_exists($operator, $operators)) {
            throw new Exception(
                "Unsupported workflow condition operator: {$operator}"
            );
        }

        return $operators[$operator]();
    }

    private function evaluateExists(
        mixed $actual,
        mixed $expected
    ): bool {
        if ($expected) {
            return ! is_null($actual);
        }

        return is_null($actual);
    }

    /**
     * @param  array<int, array<string, mixed>>  $sideEffects
     */
    private function applySideEffects(
        Model $model,
        array $sideEffects
    ): void {
        foreach ($sideEffects as $effect) {
            $type = $effect['type'] ?? null;

            if (! is_string($type) || $type === '') {
                continue;
            }

            if ($type === 'set') {
                $this->applySetEffect($model, $effect);

                continue;
            }

            if ($type === 'merge_meta') {
                $this->applyMergeMetaEffect($model, $effect);

                continue;
            }

            throw new Exception(
                "Unsupported workflow side effect: {$type}"
            );
        }
    }

    /**
     * @param  array<string, mixed>  $effect
     */
    private function applySetEffect(
        Model $model,
        array $effect
    ): void {
        $field = $effect['field'] ?? null;

        if (! is_string($field) || $field === '') {
            throw new Exception(
                'Workflow set side effect requires a field.'
            );
        }

        $model->update([
            $field => $effect['value'] ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $effect
     */
    private function applyMergeMetaEffect(
        Model $model,
        array $effect
    ): void {
        $key = $effect['key'] ?? null;

        if (! is_string($key) || $key === '') {
            throw new Exception(
                'Workflow merge_meta side effect requires a key.'
            );
        }

        $currentMeta = $model->getAttribute('meta');

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
        if (class_basename($model) === 'Quote') {
            return 'quote';
        }

        if (class_basename($model) === 'Claim') {
            return 'claim';
        }

        return strtolower(class_basename($model));
    }
}
