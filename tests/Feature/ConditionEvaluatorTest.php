<?php

namespace Tests\Feature;

use App\Services\Pricing\ConditionEvaluator;
use Tests\TestCase;

class ConditionEvaluatorTest extends TestCase
{
    public function test_condition_evaluator()
    {
        $evaluator = app(
            ConditionEvaluator::class
        );

        $result = $evaluator->evaluate(
            [
                'field' => 'car_value',
                'operator' => '>',
                'value' => 1000,
            ],
            [
                'car_value' => 2000,
            ]
        );

        $this->assertTrue($result);
    }
}
