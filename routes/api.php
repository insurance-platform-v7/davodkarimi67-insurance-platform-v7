<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\IssuanceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReinsuranceReportController;
use Illuminate\Support\Facades\Route;

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
                PaymentController::class,
                'callback',
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
            | Policies
            |--------------------------------------------------------------------------
            */

            Route::post('/policies/issue', [
                PolicyController::class,
                'issue',
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
