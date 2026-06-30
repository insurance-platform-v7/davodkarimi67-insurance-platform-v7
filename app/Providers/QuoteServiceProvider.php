<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Quote\QuoteEngine;

class QuoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteEngine::class, function () {
            return new QuoteEngine();
        });
    }

    public function boot(): void
    {
        //
    }
}
