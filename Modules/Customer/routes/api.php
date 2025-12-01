<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\ProfileController;
use Modules\Customer\Http\Controllers\AddressController;
use Modules\Customer\Http\Controllers\DocumentController;

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

Route::middleware(['customer.auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])
            ->middleware('customer.permission:customer.profile.view');
        Route::put('/', [ProfileController::class, 'update'])
            ->middleware('customer.permission:customer.profile.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Address Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])
            ->middleware('customer.permission:customer.address.view');
        Route::post('/', [AddressController::class, 'store'])
            ->middleware('customer.permission:customer.address.create');
        Route::get('/{id}', [AddressController::class, 'show'])
            ->middleware('customer.permission:customer.address.view');
        Route::put('/{id}', [AddressController::class, 'update'])
            ->middleware('customer.permission:customer.address.update');
        Route::delete('/{id}', [AddressController::class, 'destroy'])
            ->middleware('customer.permission:customer.address.delete');
        Route::patch('/{id}/primary', [AddressController::class, 'setPrimary'])
            ->middleware('customer.permission:customer.address.set_primary');
    });

    /*
    |--------------------------------------------------------------------------
    | Document Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])
            ->middleware('customer.permission:customer.document.view');
        Route::post('/', [DocumentController::class, 'store'])
            ->middleware('customer.permission:customer.document.upload');
        Route::get('/{id}', [DocumentController::class, 'show'])
            ->middleware('customer.permission:customer.document.view');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])
            ->middleware('customer.permission:customer.document.delete');
    });
});
