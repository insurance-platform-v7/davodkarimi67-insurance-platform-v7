<?php

namespace App\Services\Quote;

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
        $input = $quote->input_data;
        /** @var array<string, mixed> $input */
        $premium = (float) $this->formulaService->calculateForProduct(
            $companyProduct,
            $input
        );

        if ($premium < 0) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) round($premium);
    }
}
