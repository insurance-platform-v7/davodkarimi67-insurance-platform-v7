<?php

namespace App\Modules\Policies\Providers;

use Illuminate\Support\ServiceProvider;

class PoliciesModuleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
