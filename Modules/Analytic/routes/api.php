<?php

use Illuminate\Support\Facades\Route;
use Modules\Analytic\Http\Controllers\AnalyticController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('analytics', AnalyticController::class)->names('analytic');
});
