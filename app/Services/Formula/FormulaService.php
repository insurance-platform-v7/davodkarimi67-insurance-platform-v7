<?php

namespace App\Services\Formula;

use App\Infrastructure\Formula\FormulaEngineAdapter;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Support\FeatureFlag;
use RuntimeException;

class FormulaService
{
    public function __construct(
        protected FormulaVersionResolver $versionResolver,
        protected FormulaConditionLoader $conditionLoader,
        protected FormulaExecutor $executor,
        protected DefaultFormulaCalculator $defaultCalculator,
        protected FormulaEngineAdapter $adapter,
    ) {}

    /**
     * @param  array<string, mixed>  $variables
     */
    public function calculateForProduct(
        CompanyProduct $companyProduct,
        array $variables = []
    ): float|int {
        /** @var ProductFormula|null $productFormula */
        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();

        if (! $productFormula) {
            return $this->defaultCalculator->calculate($variables);
        }

        $version = $productFormula->getRelation('version');

        if (! $version instanceof FormulaVersion) {
            $version = null;

            if ($productFormula->formula_id) {
                $version = $this->versionResolver->resolve(
                    (int) $productFormula->formula_id
                );
            }
        }

        if (! $version instanceof FormulaVersion) {
            return $this->defaultCalculator->calculate($variables);
        }

        if (FeatureFlag::enabled('formula_engine_v2')) {
            /** @var array<string, mixed> $formula */
            $formula = $version->formula_json;

            if (empty($formula)) {
                return $this->defaultCalculator->calculate($variables);
            }

            return $this->adapter->calculate(
                $formula,
                $variables
            );
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

        /** @var array<string, mixed> $formula */
        $formula = $version->formula_json;

        return $this->executor->execute(
            $formula,
            $variables
        );
    }
}
