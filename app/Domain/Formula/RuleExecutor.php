<?php

namespace App\Domain\Formula;

use RuntimeException;

class RuleExecutor
{
    public function __construct(
        protected VariableResolver $variableResolver,
        protected ExpressionResolver $expressionResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $rule
     */
    public function execute(
        array $rule,
        Context $context
    ): void {
        $type = $rule['type'] ?? 'expression';

        if (! is_string($type)) {
            throw new RuntimeException(
                'Formula rule type must be a string.'
            );
        }

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

    /**
     * @param  array<string, mixed>  $rule
     */
    protected function handleExpression(
        array $rule,
        Context $context
    ): void {
        $expression = $rule['expression'] ?? '';

        if (! is_string($expression) || trim($expression) === '') {
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

        if (! is_string($key) || trim($key) === '') {
            throw new RuntimeException(
                'Formula output key must be a string.'
            );
        }

        $context->set(
            $key,
            $result
        );
    }
}
