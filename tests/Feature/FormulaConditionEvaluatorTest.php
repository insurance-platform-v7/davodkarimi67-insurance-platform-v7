<?php

namespace Tests\Feature;

use App\Services\Formula\ConditionEvaluator;
use Tests\TestCase;

class FormulaConditionEvaluatorTest extends TestCase
{
    public function test_missing_field_returns_false(): void
    {
        $this->assertFalse(
            app(ConditionEvaluator::class)->evaluate(
                ['field' => 'age', 'operator' => '>', 'value' => 18],
                ['name' => 'test']
            )
        );
    }

    public function test_comparison_operators(): void
    {
        $evaluator = app(ConditionEvaluator::class);

        $cases = [
            ['=', 10, 10, true],
            ['==', 10, '10', true],
            ['!=', 10, 20, true],
            ['<', 10, 20, true],
            ['<=', 10, 10, true],
            ['>', 20, 10, true],
            ['>=', 20, 20, true],
            ['unknown', 10, 20, false],
        ];

        foreach ($cases as [$operator, $actual, $expected, $result]) {
            $this->assertSame(
                $result,
                $evaluator->evaluate(
                    [
                        'field' => 'value',
                        'operator' => $operator,
                        'value' => $expected,
                    ],
                    ['value' => $actual]
                )
            );
        }
    }

    public function test_in_operator_requires_array_and_matches_strictly(): void
    {
        $evaluator = app(ConditionEvaluator::class);

        $this->assertTrue(
            $evaluator->evaluate(
                ['field' => 'type', 'operator' => 'IN', 'value' => ['car', 'motorcycle']],
                ['type' => 'car']
            )
        );

        $this->assertFalse(
            $evaluator->evaluate(
                ['field' => 'type', 'operator' => 'IN', 'value' => ['car', 'motorcycle']],
                ['type' => 'truck']
            )
        );

        $this->assertFalse(
            $evaluator->evaluate(
                ['field' => 'type', 'operator' => 'IN', 'value' => 'car'],
                ['type' => 'car']
            )
        );
    }

    public function test_between_operator(): void
    {
        $evaluator = app(ConditionEvaluator::class);

        $this->assertTrue(
            $evaluator->evaluate(
                ['field' => 'age', 'operator' => 'BETWEEN', 'value' => [18, 30]],
                ['age' => 25]
            )
        );

        $this->assertTrue(
            $evaluator->evaluate(
                ['field' => 'age', 'operator' => 'BETWEEN', 'value' => [18, 30]],
                ['age' => 18]
            )
        );

        $this->assertFalse(
            $evaluator->evaluate(
                ['field' => 'age', 'operator' => 'BETWEEN', 'value' => [18]],
                ['age' => 25]
            )
        );

        $this->assertFalse(
            $evaluator->evaluate(
                ['field' => 'age', 'operator' => 'BETWEEN', 'value' => '18-30'],
                ['age' => 25]
            )
        );
    }

    public function test_evaluate_rule_delegates_to_condition_evaluation(): void
    {
        $this->assertTrue(
            app(ConditionEvaluator::class)->evaluateRule(
                ['field' => 'score', 'operator' => '>=', 'value' => 80],
                ['score' => 90]
            )
        );
    }

    public function test_groups_support_and_or_not_and_unknown(): void
    {
        $evaluator = app(ConditionEvaluator::class);

        $conditions = [
            ['field' => 'age', 'operator' => '>=', 'value' => 18],
            ['field' => 'score', 'operator' => '>', 'value' => 80],
        ];

        $parameters = [
            'age' => 25,
            'score' => 90,
        ];

        $this->assertTrue(
            $evaluator->evaluateGroup('AND', $conditions, $parameters)
        );

        $this->assertTrue(
            $evaluator->evaluateGroup('OR', $conditions, $parameters)
        );

        $this->assertFalse(
            $evaluator->evaluateGroup('NOT', $conditions, $parameters)
        );

        $this->assertFalse(
            $evaluator->evaluateGroup('UNKNOWN', $conditions, $parameters)
        );
    }

    public function test_group_handles_false_results(): void
    {
        $evaluator = app(ConditionEvaluator::class);

        $conditions = [
            ['field' => 'age', 'operator' => '>', 'value' => 30],
            ['field' => 'score', 'operator' => '>', 'value' => 100],
        ];

        $parameters = [
            'age' => 25,
            'score' => 90,
        ];

        $this->assertFalse(
            $evaluator->evaluateGroup('AND', $conditions, $parameters)
        );

        $this->assertFalse(
            $evaluator->evaluateGroup('OR', $conditions, $parameters)
        );

        $this->assertTrue(
            $evaluator->evaluateGroup('NOT', $conditions, $parameters)
        );
    }
}