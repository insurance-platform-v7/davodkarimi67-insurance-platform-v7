<?php

namespace App\Domain\Quote;

use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Services\Quote\PremiumCalculator;

class QuoteCalculator
{
    public function __construct(
        protected PremiumCalculator $premiumCalculator
    ) {}

    public function calculate(
        Quote $quote,
        CompanyProduct $product
    ): int {
        return $this->premiumCalculator->calculate(
            $quote,
            $product
        );
    }
}
