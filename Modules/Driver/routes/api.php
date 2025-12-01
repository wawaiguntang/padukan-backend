<?php

use Illuminate\Support\Facades\Route;
use Modules\Driver\Http\Controllers\Profile\GetProfileController;
use Modules\Driver\Http\Controllers\Profile\UpdateProfileController;
use Modules\Driver\Http\Controllers\Profile\SubmitProfileVerificationController;
use Modules\Driver\Http\Controllers\Profile\GetProfileVerificationStatusController;
use Modules\Driver\Http\Controllers\Profile\ResubmitProfileVerificationController;
use Modules\Driver\Http\Controllers\Vehicle\GetVehiclesController;
use Modules\Driver\Http\Controllers\Vehicle\RegisterVehicleController;
use Modules\Driver\Http\Controllers\Vehicle\GetVehicleController;
use Modules\Driver\Http\Controllers\Vehicle\UpdateVehicleController;
use Modules\Driver\Http\Controllers\Vehicle\DeleteVehicleController;
use Modules\Driver\Http\Controllers\Vehicle\SubmitVehicleVerificationController;
use Modules\Driver\Http\Controllers\Vehicle\GetVehicleVerificationStatusController;
use Modules\Driver\Http\Controllers\Vehicle\ResubmitVehicleVerificationController;
use Modules\Driver\Http\Controllers\Vehicle\GetVehiclesVerificationStatusController;
use Modules\Driver\Http\Controllers\DriverStatus\GetDriverStatusController;
use Modules\Driver\Http\Controllers\DriverStatus\UpdateOnlineStatusController;
use Modules\Driver\Http\Controllers\DriverStatus\UpdateLocationController;

/*
|--------------------------------------------------------------------------
| Driver API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Driver module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group and driver-specific middleware.
|
*/

Route::middleware(['driver.auth'])->prefix('v1/driver')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Profile Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', GetProfileController::class)
            ->middleware('driver.permission:driver-profile-view');
        Route::put('/', UpdateProfileController::class)
            ->middleware('driver.permission:driver-profile-update');

        // Profile Verification
        Route::post('/verification/submit', SubmitProfileVerificationController::class)
            ->middleware('driver.permission:driver-profile-submit-verification');
        Route::get('/verification/status', GetProfileVerificationStatusController::class)
            ->middleware('driver.permission:driver-profile-check-verification-status');
        Route::post('/verification/resubmit', ResubmitProfileVerificationController::class)
            ->middleware('driver.permission:driver-profile-resubmit-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Vehicle Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('vehicles')->group(function () {
        // Register vehicle
        Route::post('/register', RegisterVehicleController::class)
            ->middleware('driver.permission:driver-vehicle-register');

        // Get driver's vehicles
        Route::get('/', GetVehiclesController::class)
            ->middleware('driver.permission:driver-vehicle-view');

        // Individual vehicle operations
        Route::get('/{id}', GetVehicleController::class)
            ->middleware('driver.permission:driver-vehicle-view');
        Route::put('/{id}', UpdateVehicleController::class)
            ->middleware('driver.permission:driver-vehicle-update');
        Route::delete('/{id}', DeleteVehicleController::class)
            ->middleware('driver.permission:driver-vehicle-delete');

        // Vehicle verification
        Route::post('/verification/submit', SubmitVehicleVerificationController::class)
            ->middleware('driver.permission:driver-vehicle-submit-verification');
        Route::get('/{vehicleId}/verification/status', GetVehicleVerificationStatusController::class)
            ->middleware('driver.permission:driver-vehicle-check-verification-status');
        Route::post('/verification/resubmit', ResubmitVehicleVerificationController::class)
            ->middleware('driver.permission:driver-vehicle-resubmit-verification');

        // Overall vehicle verification status
        Route::get('/verification/status', GetVehiclesVerificationStatusController::class)
            ->middleware('driver.permission:driver-vehicle-view');
    });


    /*
    |--------------------------------------------------------------------------
    | Driver Status Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('status')->group(function () {
        Route::get('/', GetDriverStatusController::class)
            ->middleware('driver.permission:driver-status-view');
        Route::put('/online', UpdateOnlineStatusController::class)
            ->middleware('driver.permission:driver-status-update');
        Route::put('/location', UpdateLocationController::class)
            ->middleware('driver.permission:driver-status-update');
    });
});
