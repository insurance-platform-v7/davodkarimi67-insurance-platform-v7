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
        $premium = (float) $this->formulaService->calculateForProduct(
            $companyProduct,
            $quote->input_data ?? []
        );

        if ($premium < 0) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) $premium;
    }
}
