<?php

use App\Http\Middleware\RequestContextMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->append(
            SecurityHeadersMiddleware::class
        );

        $middleware->alias([
            'tenant' => TenantMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);

        $middleware->append(
            RequestContextMiddleware::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {

        if (class_exists(Integration::class)) {
            Integration::handles($exceptions);
        }

    })
    ->withSchedule(function (Schedule $schedule) {

        $schedule->command('backup:clean')
            ->daily()
            ->at('01:00');

        $schedule->command('backup:monitor')
            ->daily()
            ->at('08:00');
    })
    ->create();
