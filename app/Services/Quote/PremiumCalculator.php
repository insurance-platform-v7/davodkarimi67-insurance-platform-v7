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

    /**
     * Calculate premium for a quote.
     */
    public function calculate(
        Quote $quote,
        CompanyProduct $companyProduct
    ): int {
        return $this->calculateForInput(
            $companyProduct,
            $this->extractParameters($quote)
        );
    }

    /**
     * Calculate premium directly from input parameters.
     *
     * @param array<string, mixed> $parameters
     */
    public function calculateForInput(
        CompanyProduct $companyProduct,
        array $parameters
    ): int {
        $parameters = $this->normalizeParameters($parameters);

        return FeatureFlag::enabled('formula_engine_v2')
            ? $this->calculateWithV2($companyProduct, $parameters)
            : $this->calculateWithLegacy($companyProduct, $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function calculateWithV2(
        CompanyProduct $companyProduct,
        array $parameters
    ): int {
        $productFormula = $this->loadProductFormula($companyProduct);
        $formula = $this->resolveFormula($productFormula);

        $result = $this->formulaEngine->execute(
            $formula,
            $parameters
        );

        if (
            ! array_key_exists('premium', $result)
            || ! is_numeric($result['premium'])
        ) {
            throw new RuntimeException(
                'Formula engine returned an invalid premium.'
            );
        }

        return $this->validatePremium(
            (float) $result['premium']
        );
    }

    private function loadProductFormula(
        CompanyProduct $companyProduct
    ): ProductFormula {
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

        return $productFormula;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFormula(
        ProductFormula $productFormula
    ): array {
        $version = $productFormula->getRelation('version');

        if ($version instanceof FormulaVersion) {
            $formula = $version->formula_json;

            if ($formula !== []) {
                /** @var array<string, mixed> $formula */
                return $formula;
            }
        }

        $formula = $productFormula->formula_json;

        if ($formula !== []) {
            /** @var array<string, mixed> $formula */
            return $formula;
        }

        throw new RuntimeException(
            'Product formula is empty.'
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function calculateWithLegacy(
        CompanyProduct $companyProduct,
        array $parameters
    ): int {
        $premium = (float) $this->formulaService
            ->calculateForProduct(
                $companyProduct,
                $parameters
            );

        return $this->validatePremium($premium);
    }

    private function validatePremium(float $premium): int
    {
        if ($premium < 0) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) round($premium);
    }

    /**
     * Extract quote parameters from all supported storage locations.
     *
     * @return array<string, mixed>
     */
    private function extractParameters(Quote $quote): array
    {
        $parameters = $this->extractFromSources(
            $this->quoteParameterSources($quote)
        );

        if ($parameters !== []) {
            return $parameters;
        }

        return $this->extractFromAttributes(
            $quote->getAttributes()
        );
    }

    /**
     * @return array<int, mixed>
     */
    private function quoteParameterSources(Quote $quote): array
    {
        return [
            $quote->input_data ?? null,
            $quote->parameters ?? null,
            $quote->meta ?? null,
        ];
    }

    /**
     * @param array<int, mixed> $sources
     * @return array<string, mixed>
     */
    private function extractFromSources(array $sources): array
    {
        foreach ($sources as $source) {
            $parameters = $this->normalizeSource($source);

            if ($parameters !== []) {
                return $parameters;
            }
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeSource(mixed $source): array
    {
        if (! is_array($source)) {
            return [];
        }

        /** @var array<string, mixed> $source */
        return $this->normalizeParameters($source);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    private function extractFromAttributes(array $attributes): array
    {
        foreach ([
                     'input_data',
                     'parameters',
                     'meta',
                 ] as $attribute) {
            if (
                ! array_key_exists($attribute, $attributes)
                || $attributes[$attribute] === null
            ) {
                continue;
            }

            $parameters = $this->normalizeAttribute(
                $attributes[$attribute]
            );

            if ($parameters !== []) {
                return $parameters;
            }
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeAttribute(mixed $value): array
    {
        if (is_array($value)) {
            /** @var array<string, mixed> $value */
            return $this->normalizeParameters($value);
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return [];
        }

        /** @var array<string, mixed> $decoded */
        return $this->normalizeParameters($decoded);
    }

    /**
     * Normalize all supported parameter shapes.
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    private function normalizeParameters(array $parameters): array
    {
        if (
            isset($parameters['parameters'])
            && is_array($parameters['parameters'])
        ) {
            /** @var array<string, mixed> $nestedParameters */
            $nestedParameters = $parameters['parameters'];

            return $nestedParameters;
        }

        if (
            isset($parameters['input_data'])
            && is_array($parameters['input_data'])
        ) {
            /** @var array<string, mixed> $inputData */
            $inputData = $parameters['input_data'];

            return $inputData;
        }

        return $parameters;
    }
}
