<?php

namespace App\Services\Pricing\Rules;

use App\Services\Pricing\Contracts\PricingRule;

class CarValueRule implements PricingRule
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function apply(
        float $premium,
        array $parameters
    ): float {
        $carValue = $parameters['car_value'] ?? 0;

        if ($carValue > 1_000_000_000) {
            $premium *= 1.05;
        }

        return round($premium);
    }
}
