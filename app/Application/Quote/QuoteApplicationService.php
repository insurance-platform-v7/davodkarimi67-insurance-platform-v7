<?php

namespace App\Application\Quote;

use App\Models\Quote;
use App\Services\Quote\QuoteEngine;

class QuoteApplicationService
{
    public function __construct(
        protected QuoteEngine $quoteEngine,
    ) {}

    public function execute(Quote $quote): array
    {
        return $this->quoteEngine->generateOffers($quote);
    }
}