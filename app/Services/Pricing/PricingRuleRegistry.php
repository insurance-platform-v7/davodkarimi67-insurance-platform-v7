<?php

namespace App\Services\Pricing;

use App\Services\Pricing\Contracts\PricingRule;
use App\Services\Pricing\Rules\BasePremiumRule;
use App\Services\Pricing\Rules\CarValueRule;

class PricingRuleRegistry
{
    /**
     * @return PricingRule[]
     */
    public function all(): array
    {
        return [
            app(BasePremiumRule::class),
            app(CarValueRule::class),
        ];
    }
}
