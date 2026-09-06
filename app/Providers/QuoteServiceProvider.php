<?php

namespace App\Providers;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Repositories\Quote\QuoteOfferRepository;
use App\Services\Quote\PremiumCalculator;
use App\Services\Quote\QuoteEngine;
use Illuminate\Support\ServiceProvider;

class QuoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteEngine::class, function ($app) {
            return new QuoteEngine(
                $app->make(PremiumCalculator::class),
                $app->make(QuoteOfferRepository::class),
                $app->make(CompanyProductRepository::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
