<?php

namespace App\Services\Formula;

class DefaultFormulaCalculator
{
    public function calculate(array $variables = []): int
    {
        if (isset($variables['car_value'])) {
            return (int) round($variables['car_value'] * 0.03);
        }

        if (isset($variables['amount'])) {
            return (int) round($variables['amount'] * 0.03);
        }

        return 1_000_000;
    }
}
