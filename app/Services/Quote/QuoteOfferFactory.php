<?php

namespace App\Services\Quote;

use App\Events\QuoteOfferCreated;
use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use Illuminate\Contracts\Events\Dispatcher;

class QuoteOfferFactory
{
    public function __construct(
        private Dispatcher $events,
    ) {}

    public function create(
        Quote $quote,
        CompanyProduct $companyProduct,
        int $premium
    ): QuoteOffer {
        $config = $companyProduct->config ?? [];

        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();

        $formulaVersionId = $productFormula?->version?->id;

        $offer = QuoteOffer::query()->firstOrCreate(
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

        if ($offer->wasRecentlyCreated) {
            $this->events->dispatch(
                new QuoteOfferCreated($offer)
            );
        }

        return $offer;
    }
}
