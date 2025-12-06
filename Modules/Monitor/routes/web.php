<?php

use Illuminate\Support\Facades\Route;
use Modules\Monitor\Http\Controllers\MonitorController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('monitors', MonitorController::class)->names('monitor');
});
