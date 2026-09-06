<?php

namespace App\Services\Quote;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Models\Quote;
use App\Repositories\Quote\QuoteOfferRepository;

class QuoteEngine
{
    public function __construct(
        protected PremiumCalculator $premiumCalculator,
        protected QuoteOfferRepository $offerRepository,
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
            $premium = $this->premiumCalculator->calculate(
                $quote,
                $companyProduct
            );

            $offer = $this->offerRepository->findOrCreate(
                $quote,
                $companyProduct,
                $premium
            );


            $offers[] = $offer;
        }

        return $offers;
    }
}
