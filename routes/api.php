<?php

use App\Http\Controllers\Api\DeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\BearerTokenAuth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/devices/register', [DeviceController::class, 'register'])->middleware(BearerTokenAuth::class);
