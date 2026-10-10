```php
<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\IssuanceController;
use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReinsuranceReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Checks
|--------------------------------------------------------------------------
*/

Route::get('/health/live', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/health/ready', function () {
    return response()->json(['status' => 'ready']);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('tenant')
    ->prefix('v1')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        Route::post('/auth/login', [
            AuthController::class,
            'login',
        ]);

        Route::middleware('auth:sanctum')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            Route::post('/auth/logout', [
                AuthController::class,
                'logout',
            ]);

            Route::get('/auth/me', [
                AuthController::class,
                'me',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Claims
            |--------------------------------------------------------------------------
            */

            Route::post('/claims', [
                ClaimController::class,
                'store',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Issuance
            |--------------------------------------------------------------------------
            */

            Route::post('/issuance/{policyId}', [
                IssuanceController::class,
                'issue',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */

            Route::post('/payments/callback', [
                PaymentCallbackController::class,
                'handle',
            ]);

            Route::post('/payments/create', [
                PaymentController::class,
                'create',
            ]);

            Route::post('/payments/initiate', [
                PaymentController::class,
                'create',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */

            Route::get('/admin/dashboard', [
                AdminDashboardController::class,
                'index',
            ])->middleware('permission:admin.dashboard');
        });

        /*
        |--------------------------------------------------------------------------
        | Public API
        |--------------------------------------------------------------------------
        */

        Route::post('/quotes', [
            QuoteController::class,
            'store',
        ]);

        Route::get('/reinsurance/report', [
            ReinsuranceReportController::class,
            'index',
        ]);
    });