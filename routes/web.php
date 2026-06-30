<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentCallbackController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Payment Callback Web Route
|--------------------------------------------------------------------------
| Optional web callback route.
| If you only use API callback, you can remove this route.
*/

Route::match(['GET', 'POST'], '/payments/callback', [PaymentCallbackController::class, 'handle'])
    ->name('web.payments.callback');
