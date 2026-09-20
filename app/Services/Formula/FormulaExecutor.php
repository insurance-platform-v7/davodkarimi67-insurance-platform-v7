<?php

namespace App\Services\Formula;

use App\Domain\Formula\ExpressionResolver;
use App\Domain\Formula\VariableResolver;
use App\Exceptions\Formula\FormulaVersionNotFoundException;
use InvalidArgumentException;

class FormulaExecutor
{
    public function __construct(
        protected VariableResolver $variableResolver,
        protected ExpressionResolver $expressionResolver,
        protected ConditionEvaluator $conditionEvaluator,
        protected FormulaVersionResolver $versionResolver
    ) {}

    /**
     * Backward compatible execute().
     *
     * Accepts:
     * - string expression
     * - JSON formula array
     *
     * @param  array<string, mixed>|string  $formula
     * @param  array<string, mixed>  $variables
     */
    public function execute(
        string|array $formula,
        array $variables = []
    ): float|int {
        $expression = $this->resolveExpression($formula);

        if ($expression === '') {
            throw new InvalidArgumentException(
                'Formula expression cannot be empty.'
            );
        }

        $expression = $this->variableResolver->replace(
            $expression,
            $variables
        );

        return $this->normalizeResult(
            $this->expressionResolver->evaluate($expression),
            $expression
        );
    }

    /**
     * @param  array<string, mixed>|string  $formula
     */
    private function resolveExpression(string|array $formula): string
    {
        if (! is_array($formula)) {
            return $formula;
        }

        $type = $formula['type'] ?? null;

        if (! is_string($type) || $type !== 'expression') {
            throw new InvalidArgumentException(
                'Unsupported formula type ['.
                (is_scalar($type) ? (string) $type : 'unknown').
                ']'
            );
        }

        $expression = $formula['expression'] ?? '';

        return is_string($expression) ? $expression : '';
    }

    private function normalizeResult(
        float|int $result,
        string $expression
    ): float|int {
        if (
            is_float($result)
            && fmod($result, 1.0) === 0.0
            && ! str_contains($expression, '.')
        ) {
            return (int) $result;
        }

        return $result;
    }

    public function executeExpression(string $expression): float|int
    {
        $result = $this->expressionResolver->evaluate($expression);

        if (
            is_float($result)
            && fmod($result, 1.0) === 0.0
            && ! str_contains($expression, '.')
        ) {
            return (int) $result;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function executeVariables(
        string $expression,
        array $variables
    ): float|int {
        return $this->execute($expression, $variables);
    }

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     * @param  array<string, mixed>  $variables
     */
    public function canExecute(
        array $conditions,
        array $variables
    ): bool {
        if (empty($conditions)) {
            return true;
        }

        $group = $conditions[0]['group_type'] ?? 'AND';

        if (! is_string($group)) {
            $group = 'AND';
        }

        return $this->conditionEvaluator->evaluateGroup(
            $group,
            $conditions,
            $variables
        );
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function executeVersion(
        int $formulaId,
        array $variables = []
    ): float|int {
        $version = $this->versionResolver->resolve($formulaId);

        if (! $version) {
            throw new FormulaVersionNotFoundException;
        }

        /** @var array<string, mixed> $formula */
        $formula = $version->formula_json;

        return $this->execute(
            $formula,
            $variables
        );
    }
}
