<?php

use Illuminate\Support\Facades\Route;
use Modules\FraudDetection\Http\Controllers\FraudDetectionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('frauddetections', FraudDetectionController::class)->names('frauddetection');
});
