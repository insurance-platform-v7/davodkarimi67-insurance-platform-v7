<?php

namespace Tests\Feature;

use App\Services\Formula\DefaultFormulaCalculator;
use Tests\TestCase;

class DefaultFormulaCalculatorTest extends TestCase
{
    public function test_calculates_from_car_value(): void
    {
        $this->assertSame(
            30000,
            app(DefaultFormulaCalculator::class)->calculate([
                'car_value' => 1_000_000,
            ])
        );
    }

    public function test_calculates_from_amount_when_car_value_is_absent(): void
    {
        $this->assertSame(
            15000,
            app(DefaultFormulaCalculator::class)->calculate([
                'amount' => 500_000,
            ])
        );
    }

    public function test_car_value_takes_precedence_over_amount(): void
    {
        $this->assertSame(
            30000,
            app(DefaultFormulaCalculator::class)->calculate([
                'car_value' => 1_000_000,
                'amount' => 500_000,
            ])
        );
    }

    public function test_returns_default_when_no_supported_variable_exists(): void
    {
        $this->assertSame(
            1_000_000,
            app(DefaultFormulaCalculator::class)->calculate()
        );

        $this->assertSame(
            1_000_000,
            app(DefaultFormulaCalculator::class)->calculate([
                'unknown' => 123,
            ])
        );
    }
}