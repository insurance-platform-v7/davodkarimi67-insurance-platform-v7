<?php

namespace App\Services\Pricing\Rules;

use App\Services\Pricing\Contracts\PricingRule;

class BasePremiumRule implements PricingRule
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function apply(
        float $premium,
        array $parameters
    ): float {
        return $premium;
    }
}
