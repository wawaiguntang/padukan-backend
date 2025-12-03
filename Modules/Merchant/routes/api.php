<?php

use Illuminate\Support\Facades\Route;
use Modules\Merchant\Http\Controllers\MerchantController;
use Modules\Merchant\Http\Controllers\Profile\GetProfileController;
use Modules\Merchant\Http\Controllers\Profile\UpdateProfileController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('merchants', MerchantController::class)->names('merchant');

    /*
    |--------------------------------------------------------------------------
    | Profile Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('merchant/profile')->group(function () {
        Route::get('/', GetProfileController::class)
            ->middleware('merchant.permission:merchant-profile-view');
        Route::post('/', UpdateProfileController::class)
            ->middleware('merchant.permission:merchant-profile-update');
    });
});
