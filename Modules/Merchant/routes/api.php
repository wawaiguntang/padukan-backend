<?php

use Illuminate\Support\Facades\Route;
use Modules\Merchant\Http\Controllers\MerchantController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('merchants', MerchantController::class)->names('merchant');
});
