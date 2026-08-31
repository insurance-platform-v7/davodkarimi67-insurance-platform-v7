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
    public function execute(string|array $formula, array $variables = []): float|int
    {
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

        $expression = $this->variableResolver->replace(
            $expression,
            $variables
        );

        return $this->expressionResolver->evaluate($expression);
    }

    public function executeExpression(string $expression): float|int
    {
        return $this->expressionResolver->evaluate($expression);
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
