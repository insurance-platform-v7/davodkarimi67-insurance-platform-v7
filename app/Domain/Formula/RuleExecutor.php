<?php

namespace App\Domain\Formula;

use App\Services\Formula\ExpressionResolver;
use App\Services\Formula\VariableResolver;
use RuntimeException;

class RuleExecutor
{
    public function __construct(
        protected VariableResolver $variableResolver,
        protected ExpressionResolver $expressionResolver,
    ) {}

    public function execute(
        array $rule,
        Context $context
    ): void {
        $type = $rule['type'] ?? 'expression';

        match ($type) {
            'expression' => $this->handleExpression(
                $rule,
                $context
            ),

            default => throw new RuntimeException(
                "Unknown rule type: {$type}"
            ),
        };
    }

    protected function handleExpression(
        array $rule,
        Context $context
    ): void {
        $expression = $rule['expression'] ?? '';

        if ($expression === '') {
            throw new RuntimeException(
                'Formula expression is empty.'
            );
        }

        $expression = $this->variableResolver->replace(
            $expression,
            $context->input()
        );

        $result = $this->expressionResolver->evaluate(
            $expression
        );

        $key = $rule['output'] ?? 'premium';

        $context->set(
            $key,
            $result
        );
    }
}