<?php

namespace App\Services\Formula;

use App\Infrastructure\Formula\NewFormulaAdapter;
use App\Models\CompanyProduct;
use App\Support\FeatureFlag;
use RuntimeException;

class FormulaService
{
    public function __construct(
        protected FormulaVersionResolver $versionResolver,
        protected FormulaConditionLoader $conditionLoader,
        protected FormulaExecutor $executor,
        protected DefaultFormulaCalculator $defaultCalculator,
        protected NewFormulaAdapter $adapter,
    ) {}

    public function calculateForProduct(
        CompanyProduct $companyProduct,
        array $variables = []
    ): float|int {

        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();

        if (! $productFormula) {
            return $this->defaultCalculator->calculate($variables);
        }

        if (FeatureFlag::enabled('formula_engine_v2')) {

            if (! $productFormula->version) {
                return $this->defaultCalculator->calculate($variables);
            }

            return $this->adapter->calculate(
                $productFormula->version->formula_json,
                $variables
            );
        }

        $version = $productFormula->version
            ?? $this->versionResolver->resolve(
                $productFormula->formula_id
            );

        if (! $version) {
            return $this->defaultCalculator->calculate($variables);
        }

        $conditions = $this->conditionLoader->load($version);

        if (! $this->executor->canExecute(
            $conditions,
            $variables
        )) {
            throw new RuntimeException(
                'Formula conditions failed.'
            );
        }

        return $this->executor->execute(
            $version->formula_json,
            $variables
        );
    }
}
