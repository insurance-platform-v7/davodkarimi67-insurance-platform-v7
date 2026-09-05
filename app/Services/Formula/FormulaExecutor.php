<?php

namespace App\Services\Formula;

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
     */
    public function execute(
        string|array $formula,
        array $variables = []
    ): float|int {
        if (is_array($formula)) {
            $type = $formula['type'] ?? null;

            switch ($type) {
                case 'expression':
                    $expression = $formula['expression'] ?? '';
                    break;

                default:
                    throw new InvalidArgumentException(
                        "Unsupported formula type [{$type}]"
                    );
            }
        } else {
            $expression = $formula;
        }

        if ($expression === '') {
            throw new InvalidArgumentException(
                'Formula expression cannot be empty.'
            );
        }

        $expression = $this->variableResolver->replace(
            $expression,
            $variables
        );

        $result = $this->expressionResolver->evaluate($expression);

        /*
         * Preserve integer results for the legacy FormulaExecutor API.
         *
         * Example:
         * 15 + 25 => 40
         *
         * while decimal results remain decimal:
         * 1000000 * 0.02 => 20000.0
         */
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

    public function executeVariables(
        string $expression,
        array $variables
    ): float|int {
        return $this->execute($expression, $variables);
    }

    public function canExecute(
        array $conditions,
        array $variables
    ): bool {
        if (empty($conditions)) {
            return true;
        }

        $group = $conditions[0]['group_type'] ?? 'AND';

        return $this->conditionEvaluator->evaluateGroup(
            $group,
            $conditions,
            $variables
        );
    }

    public function executeVersion(
        int $formulaId,
        array $variables = []
    ): float|int {
        $version = $this->versionResolver->resolve($formulaId);

        if (! $version) {
            throw new \RuntimeException(
                'Active formula version not found.'
            );
        }

        return $this->execute(
            $version->formula_json,
            $variables
        );
    }
}
