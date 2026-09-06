<?php

namespace App\Repositories\Quote;

use App\Models\CompanyProduct;
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
        $config = $companyProduct->config ?? [];
        /** @var ProductFormula|null $productFormula */
        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();
        $version = $productFormula?->getRelation('version');
        $formulaVersionId = $version?->id;

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
                    'company_score' => (float) ($config['company_score'] ?? 0),
                    'coverage_score' => (float) ($config['coverage_score'] ?? 0),
                ],
            ]
        );
    }
}
