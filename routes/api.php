<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\IssuanceController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\ReinsuranceReportController;
/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/


Route::get(
    '/reinsurance/report',
    [ReinsuranceReportController::class, 'index']
);

Route::post('/quotes', [QuoteController::class, 'store']);

Route::post(
    '/issuance/{policyId}',
    [IssuanceController::class, 'issue']
);

Route::post(
    '/payments/create',
    [PaymentController::class, 'create']
);

Route::post(
    '/payments/callback',
    [PaymentController::class, 'callback']
);

/*
|--------------------------------------------------------------------------
| Protected
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post(
        '/payments/create',
        [PaymentController::class, 'create']
    );

    // backward compatibility
    Route::post(
        '/payments/initiate',
        [PaymentController::class, 'create']
    );

    Route::post(
        '/policies/issue',
        [PolicyController::class, 'issue']
    );

    Route::get(
        '/admin/dashboard',
        [AdminDashboardController::class, 'index']
    );
});
