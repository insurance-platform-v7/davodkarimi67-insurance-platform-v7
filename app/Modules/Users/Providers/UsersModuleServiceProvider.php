<?php

namespace App\Modules\Users\Providers;

use Illuminate\Support\ServiceProvider;

class UsersModuleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }
}
