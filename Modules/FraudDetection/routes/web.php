<?php

use Illuminate\Support\Facades\Route;
use Modules\FraudDetection\Http\Controllers\FraudDetectionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('frauddetections', FraudDetectionController::class)->names('frauddetection');
});
