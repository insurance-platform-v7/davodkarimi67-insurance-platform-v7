<?php

namespace App\Services\Quote;

use App\Models\CompanyProduct;
use App\Services\Formula\FormulaService;

class QuoteCalculationService
{
    public function __construct(
        protected FormulaService $formulaService,
    ) {}

    public function calculate(
        CompanyProduct $companyProduct,
        array $data
    ): int {
        $premium = $this->formulaService->calculateForProduct(
            $companyProduct,
            $data
        );

        if (! is_numeric($premium)) {
            throw new \RuntimeException(
                'Invalid premium calculated.'
            );
        }

        $premium = (float) $premium;

        if ($premium < 0) {
            throw new \RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) round($premium);
    }
}
