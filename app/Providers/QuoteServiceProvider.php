<?php

namespace App\Providers;

use App\Services\Quote\QuoteEngine;
use Illuminate\Support\ServiceProvider;

class QuoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuoteEngine::class);
    }

    public function boot(): void
    {
        //
    }
}