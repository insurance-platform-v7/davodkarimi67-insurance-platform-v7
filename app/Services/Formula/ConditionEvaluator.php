<?php

namespace App\Services\Formula;

class ConditionEvaluator
{
    public function evaluate(
        array $condition,
        array $variables
    ): bool {

        $field = $condition['field'] ?? null;

        if (! array_key_exists($field, $variables)) {
            return false;
        }

        return $this->evaluateRule(
            $condition,
            $variables[$field]
        );
    }

    public function evaluateRule(
        array $condition,
        mixed $actual
    ): bool {

        $comparison = strtoupper(
            $condition['comparison'] ?? '='
        );

        $expected = $condition['value'] ?? null;

        return match ($comparison) {

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

    public function evaluateGroup(
        string $group,
        array $conditions,
        array $variables
    ): bool {

        $results = [];

        foreach ($conditions as $condition) {
            $results[] = $this->evaluate(
                $condition,
                $variables
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
