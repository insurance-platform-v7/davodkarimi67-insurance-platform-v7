<?php

namespace App\Services\Pricing;

class ConditionEvaluator
{
    /**
     * @param  array<string, mixed>  $condition
     * @param  array<string, mixed>  $parameters
     */
    public function evaluate(
        array $condition,
        array $parameters
    ): bool {
        $field = $condition['field'] ?? null;

        if (! is_string($field)) {
            return false;
        }

        if (! array_key_exists($field, $parameters)) {
            return false;
        }

        $operator = $condition['operator'] ?? '=';

        if (! is_string($operator)) {
            $operator = '=';
        }

        return $this->evaluateOperator(
            $parameters[$field],
            strtoupper($operator),
            $condition['value'] ?? null
        );
    }

    private function evaluateOperator(
        mixed $actual,
        string $operator,
        mixed $expected
    ): bool {
        return match ($operator) {
            'IN' => $this->evaluateIn($actual, $expected),
            'BETWEEN' => $this->evaluateBetween($actual, $expected),
            default => $this->evaluateComparison(
                $actual,
                $operator,
                $expected
            ),
        };
    }

    private function evaluateComparison(
        mixed $actual,
        string $operator,
        mixed $expected
    ): bool {
        return match ($operator) {
            '=', '==' => $actual == $expected,
            '!=' => $actual != $expected,
            '<' => $actual < $expected,
            '<=' => $actual <= $expected,
            '>' => $actual > $expected,
            '>=' => $actual >= $expected,
            default => false,
        };
    }

    private function evaluateIn(
        mixed $actual,
        mixed $expected
    ): bool {
        return is_array($expected)
            && in_array($actual, $expected, true);
    }

    private function evaluateBetween(
        mixed $actual,
        mixed $expected
    ): bool {
        return is_array($expected)
            && count($expected) === 2
            && $actual >= $expected[0]
            && $actual <= $expected[1];
    }

    /**
     * @param  array<string, mixed>  $rule
     * @param  array<string, mixed>  $parameters
     */
    public function evaluateRule(
        array $rule,
        array $parameters
    ): bool {
        return $this->evaluate(
            $rule,
            $parameters
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     * @param  array<string, mixed>  $parameters
     */
    public function evaluateGroup(
        string $group,
        array $conditions,
        array $parameters
    ): bool {
        $results = [];

        foreach ($conditions as $condition) {
            $results[] = $this->evaluate(
                $condition,
                $parameters
            );
        }

        return match (strtoupper($group)) {
            'AND' => ! in_array(false, $results, true),
            'OR' => in_array(true, $results, true),
            'NOT' => ! in_array(true, $results, true),
            default => false,
        };
    }
}
