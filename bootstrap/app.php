<?php

use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RequestContextMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Auth\AuthenticationException;
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

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*')) {
                return null;
            }

            return route('login');
        });

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

        $exceptions->shouldRenderJsonWhen(function ($request, $exception) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });
    })
    ->create();
