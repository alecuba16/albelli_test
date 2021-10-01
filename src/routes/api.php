<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestAuthController as RestAuthController;
use App\Http\Controllers\RestOfferController as RestOfferController;
use App\Http\Controllers\RestAdvertisementController as RestAdvertisementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['cors'])->group( function () {
    Route::post('login', [RestAuthController::class, 'login']);
    Route::post('register', [RestAuthController::class, 'register']);
});

Route::middleware(['cors','auth:sanctum'])->group( function () {
    Route::resource('offers', RestOfferController::class);
    Route::resource('advertisements', RestAdvertisementController::class);
    Route::get('logout', [RestAuthController::class, 'logout']);
    Route::get('checkLogin', [RestAuthController::class, 'checkLogin']);
});
