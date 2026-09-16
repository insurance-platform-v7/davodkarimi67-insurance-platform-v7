<?php

namespace Tests\Feature;

use App\Services\Pricing\ConditionEvaluator;
use Tests\TestCase;

class ConditionEvaluatorTest extends TestCase
{
    private function evaluator(): ConditionEvaluator
    {
        return app(ConditionEvaluator::class);
    }

    public function test_comparison_operators(): void
    {
        $evaluator = $this->evaluator();

        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '=', 'value' => 10], ['x' => 10]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '==', 'value' => 10], ['x' => 10]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '!=', 'value' => 10], ['x' => 20]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '<', 'value' => 20], ['x' => 10]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '<=', 'value' => 10], ['x' => 10]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '>', 'value' => 5], ['x' => 10]));
        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => '>=', 'value' => 10], ['x' => 10]));
    }

    public function test_missing_field_returns_false(): void
    {
        $this->assertFalse(
            $this->evaluator()->evaluate(
                ['field' => 'missing', 'operator' => '>', 'value' => 10],
                ['x' => 20]
            )
        );
    }

    public function test_in_operator(): void
    {
        $evaluator = $this->evaluator();

        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => 'in', 'value' => [10, 20]], ['x' => 10]));
        $this->assertFalse($evaluator->evaluate(['field' => 'x', 'operator' => 'IN', 'value' => [10, 20]], ['x' => 30]));
        $this->assertFalse($evaluator->evaluate(['field' => 'x', 'operator' => 'IN', 'value' => '10'], ['x' => 10]));
    }

    public function test_between_operator(): void
    {
        $evaluator = $this->evaluator();

        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'operator' => 'BETWEEN', 'value' => [10, 20]], ['x' => 15]));
        $this->assertFalse($evaluator->evaluate(['field' => 'x', 'operator' => 'BETWEEN', 'value' => [10, 20]], ['x' => 25]));
        $this->assertFalse($evaluator->evaluate(['field' => 'x', 'operator' => 'BETWEEN', 'value' => [10]], ['x' => 15]));
    }

    public function test_default_and_unknown_operators(): void
    {
        $evaluator = $this->evaluator();

        $this->assertTrue($evaluator->evaluate(['field' => 'x', 'value' => 10], ['x' => 10]));
        $this->assertFalse($evaluator->evaluate(['field' => 'x', 'operator' => 'UNKNOWN', 'value' => 10], ['x' => 10]));
    }

    public function test_evaluate_rule_delegates_to_evaluate(): void
    {
        $this->assertTrue(
            $this->evaluator()->evaluateRule(
                ['field' => 'x', 'operator' => '>=', 'value' => 10],
                ['x' => 10]
            )
        );
    }

    public function test_evaluate_groups(): void
    {
        $evaluator = $this->evaluator();

        $conditions = [
            ['field' => 'x', 'operator' => '>', 'value' => 5],
            ['field' => 'y', 'operator' => '=', 'value' => 20],
        ];

        $parameters = ['x' => 10, 'y' => 20];

        $this->assertTrue($evaluator->evaluateGroup('AND', $conditions, $parameters));
        $this->assertTrue($evaluator->evaluateGroup('OR', $conditions, ['x' => 10, 'y' => 5]));
        $this->assertTrue($evaluator->evaluateGroup('NOT', $conditions, ['x' => 1, 'y' => 5]));
        $this->assertFalse($evaluator->evaluateGroup('UNKNOWN', $conditions, $parameters));
    }
}