<?php

namespace App\Modules\Quotes\Providers;

use Illuminate\Support\ServiceProvider;

class QuotesModuleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
