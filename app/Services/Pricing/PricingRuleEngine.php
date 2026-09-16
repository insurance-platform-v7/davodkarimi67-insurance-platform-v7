<?php

namespace App\Services\Pricing;

class PricingRuleEngine
{
    public function __construct(
        protected PricingRuleRegistry $registry,
        protected ConditionEvaluator $conditionEvaluator
    ) {}

    /**
     * @param array<string, mixed> $parameters
     */
    public function calculate(
        float $premium,
        array $parameters
    ): float {
        foreach ($this->registry->all() as $rule) {
            $premium = $rule->apply(
                $premium,
                $parameters
            );
        }

        return $premium;
    }

    /**
     * @param array<int, array<string, mixed>> $conditions
     * @param array<string, mixed> $parameters
     */
    public function checkConditions(
        array $conditions,
        array $parameters,
        string $group = 'AND'
    ): bool {
        return $this->conditionEvaluator
            ->evaluateGroup(
                $group,
                $conditions,
                $parameters
            );
    }
}
