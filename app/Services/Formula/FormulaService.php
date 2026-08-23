<?php

namespace App\Services\Formula;

use App\Infrastructure\Formula\NewFormulaAdapter;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
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

        /*
         * No product formula.
         */
        if (! $productFormula) {
            return $this->defaultCalculator->calculate($variables);
        }

        /*
         * Resolve FormulaVersion explicitly.
         *
         * ProductFormula has:
         *
         * formula_version_id -> formula_versions.id
         *
         * and also a "version" string column.
         *
         * Therefore we must make sure that the value used here
         * is actually the FormulaVersion model.
         */
        $version = $productFormula->getRelation('version');

        if (! $version instanceof FormulaVersion) {

            $version = null;

            /*
             * Fallback: resolve active version from formula_id.
             */
            if ($productFormula->formula_id) {
                $version = $this->versionResolver->resolve(
                    (int) $productFormula->formula_id
                );
            }
        }

        /*
         * No valid FormulaVersion.
         */
        if (! $version instanceof FormulaVersion) {
            return $this->defaultCalculator->calculate($variables);
        }

        /*
         * New Formula Engine.
         */
        if (FeatureFlag::enabled('formula_engine_v2')) {

            $formula = $version->formula_json;

            if (! is_array($formula) || empty($formula)) {
                return $this->defaultCalculator->calculate($variables);
            }

            return $this->adapter->calculate(
                $formula,
                $variables
            );
        }

        /*
         * Legacy Formula Engine.
         */
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
