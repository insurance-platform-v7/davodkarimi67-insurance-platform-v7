<?php

use App\Http\Controllers\Api\PolicyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'tenant', 'auth:sanctum'])
    ->prefix('api/v1')
    ->group(function (): void {
        Route::post('/policies/issue', [
            PolicyController::class,
            'issue',
        ]);
    });
