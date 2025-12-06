<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerSupport\Http\Controllers\CustomerSupportController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('customersupports', CustomerSupportController::class)->names('customersupport');
});
