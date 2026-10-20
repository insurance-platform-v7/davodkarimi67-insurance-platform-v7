<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health/live', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/health/ready', function () {
    return response()->json(['status' => 'ready']);
});
