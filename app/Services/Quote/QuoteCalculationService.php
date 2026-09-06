<?php

namespace App\Services\Quote;

use App\Models\CompanyProduct;

class QuoteCalculationService
{
    public function __construct(
        protected PremiumCalculator $premiumCalculator,
    ) {}

    public function calculate(
        CompanyProduct $companyProduct,
        array $data = []
    ): int {
        return $this->premiumCalculator->calculateForInput(
            $companyProduct,
            $data
        );
    }

    public function calculatePremium(
        float|int $base,
        float|int $discount = 0,
        float|int $tax = 0
    ): float {
        $baseVal = (float) $base;
        $discVal = (float) $discount;
        $taxVal = (float) $tax;

        $subtotal = max(0.0, $baseVal - $discVal);

        return $subtotal + $taxVal;
    }
}
