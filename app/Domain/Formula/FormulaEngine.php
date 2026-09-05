<?php

namespace App\Domain\Formula;

use InvalidArgumentException;

class FormulaEngine
{
    public function __construct(
        protected RuleExecutor $ruleExecutor,
    ) {}

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

            $this->ruleExecutor->execute(
                $rule,
                $context
            );
        }

        return $context->result();
    }
}