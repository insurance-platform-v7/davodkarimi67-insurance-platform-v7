<?php

namespace App\Providers;

use App\Services\Quote\QuoteEngine;
use Illuminate\Support\ServiceProvider;

class QuoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteEngine::class, function ($app) {
            return new QuoteEngine(
                $app->make(\App\Domain\Quote\QuoteCalculator::class),
                $app->make(\App\Repositories\Quote\QuoteOfferRepository::class),
                $app->make(\App\Domain\CompanyProduct\CompanyProductRepository::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
