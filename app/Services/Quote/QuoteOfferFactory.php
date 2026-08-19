<?php

namespace App\Services\Quote;

use App\Events\QuoteOfferCreated;
use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Repositories\Quote\QuoteOfferRepository;
use Illuminate\Contracts\Events\Dispatcher;

class QuoteOfferFactory
{
    public function __construct(
        private QuoteOfferRepository $repository,
        private Dispatcher $events,
    ) {}

    public function create(
        Quote $quote,
        CompanyProduct $companyProduct,
        int $premium
    ): QuoteOffer {
        $offer = $this->repository->findOrCreate(
            $quote,
            $companyProduct,
            $premium
        );

        if ($offer->wasRecentlyCreated) {
            $this->events->dispatch(
                new QuoteOfferCreated($offer)
            );
        }

        return $offer;
    }
}
