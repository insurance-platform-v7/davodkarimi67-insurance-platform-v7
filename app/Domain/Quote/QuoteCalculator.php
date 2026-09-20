<?php

namespace App\Domain\Quote;

class QuoteCalculator
{
    public function calculate(float|int $basePremium, float|int $discount = 0, float|int $tax = 0): float
    {
        $base = (float) $basePremium;
        $disc = (float) $discount;
        $tx = (float) $tax;
        $total = ($base - $disc) + $tx;

        return max(0.0, $total);
    }
}
