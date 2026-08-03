<?php

namespace App\Services\Quote;

use App\Infrastructure\Formula\FormulaEngineAdapter;
use App\Models\CompanyProduct;
use App\Services\Formula\FormulaService;

class QuoteCalculationService
{
    public function __construct(
        protected FormulaService $formulaService,
        protected FormulaEngineAdapter $engineAdapter,
    ) {}

    public function calculate(
        CompanyProduct $companyProduct,
        array $data
    ): int {

        if (config('formula.use_new_engine')) {

            $productFormula = $companyProduct
                ->productFormula()
                ->with('version')
                ->first();

            if ($productFormula?->version?->formula_json !== null) {
                return $this->engineAdapter->calculate(
                    $productFormula->version->formula_json,
                    $data
                );
            }
        }

        return (int) $this->formulaService->calculateForProduct(
            $companyProduct,
            $data
        );
    }
}
