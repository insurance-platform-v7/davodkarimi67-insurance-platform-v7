<?php

namespace App\Modules\Payments\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentsModuleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
