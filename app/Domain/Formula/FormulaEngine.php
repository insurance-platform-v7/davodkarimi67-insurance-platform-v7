<?php

namespace App\Domain\Formula;

use InvalidArgumentException;

class FormulaEngine
{
    public function __construct(
        protected RuleExecutor $ruleExecutor,
    ) {}

    /**
     * @param array<string, mixed> $formula
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(
        array $formula,
        array $input
    ): array {
        if (empty($formula)) {
            throw new InvalidArgumentException(
                'Formula cannot be empty.'
            );
        }

        $context = new Context($input);

        $rules = $formula['rules'] ?? [$formula];

        if (! is_array($rules)) {
            throw new InvalidArgumentException(
                'Invalid formula rules.'
            );
        }

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                throw new InvalidArgumentException(
                    'Invalid formula rule.'
                );
            }

            /** @var array<string, mixed> $typedRule */
            $typedRule = $rule;

            $this->ruleExecutor->execute(
                $typedRule,
                $context
            );
        }

        return $context->result();
    }
}
