<?php

namespace App\Contracts;

use App\Models\QuoteRequest;

interface PricingStrategy
{
    /**
     * Calculates the price based on the quote request and potentially other data.
     *
     * @return array<string, mixed>
     */
    public function calculate(QuoteRequest $quoteRequest): array;
}
