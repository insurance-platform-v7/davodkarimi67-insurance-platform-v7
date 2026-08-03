<?php

namespace App\Services\Quote;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Models\Quote;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class QuoteEngine
{
    public function __construct(
        protected QuoteCalculator $quoteCalculator,
        protected QuoteOfferFactory $offerFactory,
        protected CompanyProductRepository $companyProductRepository,
        protected CacheRepository $cache,
    ) {}

    public function generateOffers(Quote $quote): array
    {
        $cacheKey = "quote:company-products:{$quote->insurance_product_id}";

        $companyProducts = $this->cache->remember(
            $cacheKey,
            now()->addMinutes(10),
            fn () => $this->companyProductRepository
                ->getActiveByInsuranceProduct($quote->insurance_product_id)
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
