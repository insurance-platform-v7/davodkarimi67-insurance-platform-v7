<?php

namespace App\Services\Pricing\Contracts;

interface PricingRule
{
    /**
     * Apply rule and return updated premium.
     */
    public function apply(
        float $premium,
        array $parameters
    ): float;
}
