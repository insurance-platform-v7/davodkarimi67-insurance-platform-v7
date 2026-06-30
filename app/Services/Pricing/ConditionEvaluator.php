<?php

namespace App\Services\Pricing;

class ConditionEvaluator
{
    public function evaluate(
        array $condition,
        array $parameters
    ): bool {

        $field = $condition['field'] ?? null;

        if (! array_key_exists($field, $parameters)) {
            return false;
        }

        $actual = $parameters[$field];
        $expected = $condition['value'] ?? null;

        return match (
        strtoupper($condition['operator'] ?? '=')
        ) {

            '=', '==' => $actual == $expected,

            '!=' => $actual != $expected,

            '<' => $actual < $expected,

            '<=' => $actual <= $expected,

            '>' => $actual > $expected,

            '>=' => $actual >= $expected,

            'IN' => is_array($expected)
                && in_array(
                    $actual,
                    $expected,
                    true
                ),

            'BETWEEN' => is_array($expected)
                && count($expected) === 2
                && $actual >= $expected[0]
                && $actual <= $expected[1],

            default => false,
        };
    }

    public function evaluateRule(
        array $rule,
        array $parameters
    ): bool {

        return $this->evaluate(
            $rule,
            $parameters
        );
    }

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

        return match (
        strtoupper($group)
        ) {

            'AND' => ! in_array(
                false,
                $results,
                true
            ),

            'OR' => in_array(
                true,
                $results,
                true
            ),

            'NOT' => ! in_array(
                true,
                $results,
                true
            ),

            default => false,
        };
    }
}
