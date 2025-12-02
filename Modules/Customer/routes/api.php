<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Profile\GetProfileController;
use Modules\Customer\Http\Controllers\Profile\UpdateProfileController;
use Modules\Customer\Http\Controllers\Profile\SubmitProfileVerificationController;
use Modules\Customer\Http\Controllers\Profile\GetProfileVerificationStatusController;
use Modules\Customer\Http\Controllers\Profile\ResubmitProfileVerificationController;
use Modules\Customer\Http\Controllers\Address\IndexAddressController;
use Modules\Customer\Http\Controllers\Address\StoreAddressController;
use Modules\Customer\Http\Controllers\Address\ShowAddressController;
use Modules\Customer\Http\Controllers\Address\UpdateAddressController;
use Modules\Customer\Http\Controllers\Address\DestroyAddressController;
use Modules\Customer\Http\Controllers\Address\SetPrimaryAddressController;

/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Customer module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group and customer-specific middleware.
|
*/

Route::middleware(['customer.auth'])->prefix('v1/customer')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', GetProfileController::class)
            ->middleware('customer.permission:customer-profile-view');
        Route::post('/', UpdateProfileController::class)
            ->middleware('customer.permission:customer-profile-update');

        // Profile Verification
        Route::post('/verification/submit', SubmitProfileVerificationController::class)
            ->middleware('customer.permission:customer-profile-submit-verification');
        Route::get('/verification/status', GetProfileVerificationStatusController::class)
            ->middleware('customer.permission:customer-profile-check-verification-status');
        Route::post('/verification/resubmit', ResubmitProfileVerificationController::class)
            ->middleware('customer.permission:customer-profile-resubmit-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Address Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('addresses')->group(function () {
        Route::get('/', IndexAddressController::class)
            ->middleware('customer.permission:customer-address-view');
        Route::post('/', StoreAddressController::class)
            ->middleware('customer.permission:customer-address-create');
        Route::get('/{id}', ShowAddressController::class)
            ->middleware('customer.permission:customer-address-view');
        Route::put('/{id}', UpdateAddressController::class)
            ->middleware('customer.permission:customer-address-update');
        Route::delete('/{id}', DestroyAddressController::class)
            ->middleware('customer.permission:customer-address-delete');
        Route::patch('/{id}/primary', SetPrimaryAddressController::class)
            ->middleware('customer.permission:customer-address-set-primary');
    });
});
