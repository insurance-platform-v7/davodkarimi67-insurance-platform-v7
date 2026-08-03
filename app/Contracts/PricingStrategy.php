<?php

namespace App\Contracts;

use App\Models\QuoteRequest;

interface PricingStrategy
{
    /**
     * Calculates the price based on the quote request and potentially other data.
     *
     * @return array Returns an array containing 'final_price' and 'details'.
     */
    public function calculate(QuoteRequest $quoteRequest): array;
}
