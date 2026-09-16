<?php

namespace App\Infrastructure\Formula;

use App\Models\CompanyProduct;
use App\Services\Formula\FormulaService;

class LegacyFormulaAdapter
{
    public function __construct(
        protected FormulaService $formulaService
    ) {}

    /**
     * @param array<string, mixed> $input
     */
    public function calculate(
        CompanyProduct $companyProduct,
        array $input
    ): float|int {
        return $this->formulaService->calculateForProduct(
            $companyProduct,
            $input
        );
    }
}
