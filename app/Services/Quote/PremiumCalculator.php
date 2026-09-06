<?php

namespace App\Services\Quote;

use App\Domain\Formula\FormulaEngine;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Models\Quote;
use App\Services\Formula\FormulaService;
use RuntimeException;

class PremiumCalculator
{
    public function __construct(
        protected FormulaService $formulaService,
        protected FormulaEngine $formulaEngine,
    ) {}

    public function calculate(
        Quote $quote,
        CompanyProduct $companyProduct
    ): int {
        return $this->calculateForInput(
            $companyProduct,
            $quote->input_data ?? []
        );
    }

    public function calculateForInput(
        CompanyProduct $companyProduct,
        array $input
    ): int {
        if (
            function_exists('feature_flag')
            && feature_flag('new_formula')
        ) {
            /** @var ProductFormula|null $productFormula */
            $productFormula = $companyProduct
                ->productFormula()
                ->with('version')
                ->first();
            if (! $productFormula) {
                throw new RuntimeException(
                    'No active product formula found.'
                );
            }
            $formula = null;
            $version = $productFormula->getRelation('version');
            if (
                $version instanceof FormulaVersion
                && ! empty($version->formula_json)
            ) {
                $formula = $version->formula_json;
            }
            if (
                empty($formula)
                && is_array($productFormula->formula_json)
            ) {
                $formula = $productFormula->formula_json;
            }
            if (empty($formula)) {
                throw new RuntimeException(
                    'Product formula is empty.'
                );
            }
            $result = $this->formulaEngine->execute(
                $formula,
                $input
            );
            if (
                ! array_key_exists('premium', $result)
                || ! is_numeric($result['premium'])
            ) {
                throw new RuntimeException(
                    'Formula engine returned an invalid premium.'
                );
            }
            $premium = (float) $result['premium'];
            if ($premium < 0) {
                throw new RuntimeException(
                    'Invalid premium calculated.'
                );
            }

            return (int) round($premium);
        }
        $premium = (float) $this->formulaService
            ->calculateForProduct(
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
