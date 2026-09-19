<?php

namespace App\Repositories\Quote;

use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Models\Quote;
use App\Models\QuoteOffer;

class QuoteOfferRepository
{
    public function findOrCreate(
        Quote $quote,
        CompanyProduct $companyProduct,
        int $premium
    ): QuoteOffer {
        /** @var array<string, mixed> $config */
        $config = $companyProduct->config ?? [];

        /** @var ProductFormula|null $productFormula */
        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();

        /** @var FormulaVersion|null $version */
        $version = $productFormula?->getRelation('version');

        $formulaVersionId = $version?->id;

        $companyScore = $config['company_score'] ?? 0;
        $coverageScore = $config['coverage_score'] ?? 0;

        return QuoteOffer::query()->updateOrCreate(
            [
                'quote_id' => $quote->id,
                'company_product_id' => $companyProduct->id,
            ],
            [
                'tenant_id' => $quote->tenant_id,
                'insurance_company_id' => $companyProduct->insurance_company_id,
                'formula_version_id' => $formulaVersionId,
                'premium' => $premium,
                'status' => 'offered',
                'meta' => [
                    'company_score' => is_numeric($companyScore)
                        ? (float) $companyScore
                        : 0.0,
                    'coverage_score' => is_numeric($coverageScore)
                        ? (float) $coverageScore
                        : 0.0,
                ],
            ]
        );
    }
}
