<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerSupport\Http\Controllers\CustomerSupportController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('customersupports', CustomerSupportController::class)->names('customersupport');
});
