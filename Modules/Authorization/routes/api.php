<?php

use Illuminate\Support\Facades\Route;
use Modules\Authorization\Http\Controllers\CheckRoleController;
use Modules\Authorization\Http\Controllers\AssignRoleController;

Route::middleware(['jwt.auth'])->prefix('v1/authz')->group(function () {
    Route::get('/check-role', [CheckRoleController::class, 'checkRole']);
    Route::post('/assign-role/{roleType}', [AssignRoleController::class, 'assignRole'])
        ->where('roleType', 'customer|driver|merchant');
});