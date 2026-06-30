<?php

namespace App\Services\Pricing\Rules;

use App\Services\Pricing\Contracts\PricingRule;

class BasePremiumRule implements PricingRule
{
    public function apply(
        float $premium,
        array $parameters
    ): float {

        return $premium;
    }
}
