<?php

namespace App\Services\Quote;

use App\Domain\Formula\FormulaEngine;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Models\Quote;
use App\Services\Formula\FormulaService;
use App\Support\FeatureFlag;
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
        $input = $quote->input_data;

        /** @var array<string, mixed> $input */
        return $this->calculateForInput(
            $companyProduct,
            $input
        );
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function calculateForInput(
        CompanyProduct $companyProduct,
        array $input
    ): int {
        if (FeatureFlag::enabled('formula_engine_v2')) {
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

            /** @var array<string, mixed>|null $formula */
            $formula = null;

            $version = $productFormula->getRelation('version');

            if (
                $version instanceof FormulaVersion
                && $version->formula_json !== []
            ) {
                /** @var array<string, mixed> $versionFormula */
                $versionFormula = $version->formula_json;

                $formula = $versionFormula;
            }

            if (
                $formula === null
                && $productFormula->formula_json !== []
            ) {
                /** @var array<string, mixed> $productFormulaFormula */
                $productFormulaFormula = $productFormula->formula_json;

                $formula = $productFormulaFormula;
            }

            if ($formula === null || $formula === []) {
                throw new RuntimeException(
                    'Product formula is empty.'
                );
            }

            /** @var array<string, mixed> $formula */
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
