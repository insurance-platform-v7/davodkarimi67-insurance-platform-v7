<?php

namespace App\Domain\Quote;

use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Services\Formula\FormulaService;
use RuntimeException;

class QuoteCalculator
{
    public function __construct(
        protected FormulaService $formulaService,
    ) {}

    public function calculate(
        Quote $quote,
        CompanyProduct $companyProduct
    ): int {
        $premium = $this->formulaService->calculateForProduct(
            $companyProduct,
            $quote->input_data ?? []
        );

        if (! is_numeric($premium)) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        $premium = (float) $premium;

        if ($premium < 0) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) round($premium);
    }
}
