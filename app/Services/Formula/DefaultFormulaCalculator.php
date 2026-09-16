<?php

namespace App\Services\Formula;

class DefaultFormulaCalculator
{
    /**
     * @param array<string, mixed> $variables
     */
    public function calculate(array $variables = []): int
    {
        if (isset($variables['car_value']) && is_numeric($variables['car_value'])) {
            return (int) round(
                (float) $variables['car_value'] * 0.03
            );
        }

        if (isset($variables['amount']) && is_numeric($variables['amount'])) {
            return (int) round(
                (float) $variables['amount'] * 0.03
            );
        }

        return 1_000_000;
    }
}