<?php

namespace App\Services\Quote;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Domain\Quote\QuoteCalculator;
use App\Models\Quote;

class QuoteEngine
{
    public function __construct(
        protected QuoteCalculator $quoteCalculator,
        protected QuoteOfferFactory $offerFactory,
        protected CompanyProductRepository $companyProductRepository,
    ) {}

    public function generateOffers(Quote $quote): array
    {
        $companyProducts = $this->companyProductRepository
            ->getActiveByInsuranceProduct(
                $quote->insurance_product_id,
                $quote->tenant_id
            );

        if ($companyProducts->isEmpty()) {
            return [];
        }

        $offers = [];

        foreach ($companyProducts as $companyProduct) {
            $premium = $this->quoteCalculator->calculate(
                $quote,
                $companyProduct
            );

            $offers[] = $this->offerFactory->create(
                $quote,
                $companyProduct,
                $premium
            );
        }

        return $offers;
    }
}
