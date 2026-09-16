<?php

namespace Tests\Feature;

use App\Services\Pricing\ConditionEvaluator;
use App\Services\Pricing\PricingRuleEngine;
use App\Services\Pricing\PricingRuleRegistry;
use App\Services\Pricing\Rules\BasePremiumRule;
use App\Services\Pricing\Rules\CarValueRule;
use Mockery;
use Tests\TestCase;

class PricingRuleEngineTest extends TestCase
{
    public function test_base_premium_rule_returns_original_premium(): void
    {
        $rule = new BasePremiumRule();

        $this->assertSame(
            1000.0,
            $rule->apply(1000.0, [])
        );
    }

    public function test_car_value_rule_applies_surcharge_above_threshold(): void
    {
        $rule = new CarValueRule();

        $this->assertSame(
            1050.0,
            $rule->apply(1000.0, [
                'car_value' => 1_000_000_001,
            ])
        );
    }

    public function test_car_value_rule_keeps_premium_at_or_below_threshold(): void
    {
        $rule = new CarValueRule();

        $this->assertSame(
            1000.0,
            $rule->apply(1000.0, [
                'car_value' => 1_000_000_000,
            ])
        );
    }

    public function test_registry_returns_registered_pricing_rules(): void
    {
        $registry = app(PricingRuleRegistry::class);

        $rules = $registry->all();

        $this->assertCount(2, $rules);
        $this->assertInstanceOf(BasePremiumRule::class, $rules[0]);
        $this->assertInstanceOf(CarValueRule::class, $rules[1]);
    }

    public function test_engine_calculates_using_registered_rules(): void
    {
        $registry = Mockery::mock(PricingRuleRegistry::class);

        $rule = Mockery::mock();
        $rule->shouldReceive('apply')
            ->once()
            ->with(1000.0, ['car_value' => 500])
            ->andReturn(1100.0);

        $registry->shouldReceive('all')
            ->once()
            ->andReturn([$rule]);

        $conditionEvaluator = Mockery::mock(ConditionEvaluator::class);

        $engine = new PricingRuleEngine(
            $registry,
            $conditionEvaluator
        );

        $this->assertSame(
            1100.0,
            $engine->calculate(1000.0, ['car_value' => 500])
        );
    }

    public function test_engine_delegates_condition_check(): void
    {
        $registry = Mockery::mock(PricingRuleRegistry::class);
        $conditionEvaluator = Mockery::mock(ConditionEvaluator::class);

        $conditionEvaluator->shouldReceive('evaluateGroup')
            ->once()
            ->with(
                'OR',
                [['field' => 'car_value', 'operator' => '>', 'value' => 100]],
                ['car_value' => 500]
            )
            ->andReturnTrue();

        $engine = new PricingRuleEngine(
            $registry,
            $conditionEvaluator
        );

        $this->assertTrue(
            $engine->checkConditions(
                [['field' => 'car_value', 'operator' => '>', 'value' => 100]],
                ['car_value' => 500],
                'OR'
            )
        );
    }
}