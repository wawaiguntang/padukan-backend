<?php

use Illuminate\Support\Facades\Route;
use Modules\Merchant\Http\Controllers\Merchant\Address\GetMerchantAddressController;
use Modules\Merchant\Http\Controllers\Merchant\Address\UpdateMerchantAddressController;
use Modules\Merchant\Http\Controllers\Merchant\CreateMerchantController;
use Modules\Merchant\Http\Controllers\Merchant\GetMerchantController;
use Modules\Merchant\Http\Controllers\Merchant\GetMerchantVerificationStatusController;
use Modules\Merchant\Http\Controllers\Merchant\ListMerchantsController;
use Modules\Merchant\Http\Controllers\Merchant\ResubmitMerchantVerificationController;
use Modules\Merchant\Http\Controllers\Merchant\Schedule\GetMerchantScheduleController;
use Modules\Merchant\Http\Controllers\Merchant\Schedule\UpdateMerchantScheduleController;
use Modules\Merchant\Http\Controllers\Merchant\Setting\GetMerchantSettingsController;
use Modules\Merchant\Http\Controllers\Merchant\Setting\UpdateMerchantSettingsController;
use Modules\Merchant\Http\Controllers\Merchant\SubmitMerchantVerificationController;
use Modules\Merchant\Http\Controllers\Merchant\UpdateMerchantController;
use Modules\Merchant\Http\Controllers\Merchant\UpdateMerchantStatusController;
use Modules\Merchant\Http\Controllers\Profile\GetProfileController;
use Modules\Merchant\Http\Controllers\Profile\GetProfileVerificationStatusController;
use Modules\Merchant\Http\Controllers\Profile\ResubmitProfileVerificationController;
use Modules\Merchant\Http\Controllers\Profile\SubmitProfileVerificationController;
use Modules\Merchant\Http\Controllers\Profile\UpdateProfileController;


Route::middleware(['merchant.auth'])->prefix('v1/merchant')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', GetProfileController::class)
            ->middleware('merchant.permission:merchant-profile-view');
        Route::post('/', UpdateProfileController::class)
            ->middleware('merchant.permission:merchant-profile-update');

        // Profile Verification
        Route::post('/verification/submit', SubmitProfileVerificationController::class)
            ->middleware('merchant.permission:merchant-profile-submit-verification');
        Route::get('/verification/status', GetProfileVerificationStatusController::class)
            ->middleware('merchant.permission:merchant-profile-check-verification-status');
        Route::post('/verification/resubmit', ResubmitProfileVerificationController::class)
            ->middleware('merchant.permission:merchant-profile-resubmit-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Merchant Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('merchants')->group(function () {
        // List all merchants for authenticated user
        Route::get('/', ListMerchantsController::class)
            ->middleware('merchant.permission:merchant-merchant-view');

        // Create new merchant
        Route::post('/', CreateMerchantController::class)
            ->middleware('merchant.permission:merchant-merchant-create');

        // Get specific merchant (with ownership validation)
        Route::get('/{merchantId}', GetMerchantController::class)
            ->middleware('merchant.permission:merchant-merchant-view');

        // Update specific merchant (with ownership validation)
        Route::put('/{merchantId}', UpdateMerchantController::class)
            ->middleware('merchant.permission:merchant-merchant-update');

        // Merchant verification routes
        Route::post('/{merchantId}/verification/submit', SubmitMerchantVerificationController::class)
            ->middleware('merchant.permission:merchant-merchant-verification-submit');
        Route::post('/{merchantId}/verification/resubmit', ResubmitMerchantVerificationController::class)
            ->middleware('merchant.permission:merchant-merchant-verification-resubmit');
        Route::get('/{merchantId}/verification/status', GetMerchantVerificationStatusController::class)
            ->middleware('merchant.permission:merchant-merchant-verification-view');

        // Update merchant status (open/closed)
        Route::put('/{merchantId}/status', UpdateMerchantStatusController::class)
            ->middleware('merchant.permission:merchant-merchant-status-update');

        // Merchant address management
        Route::get('/{merchantId}/address', GetMerchantAddressController::class)
            ->middleware('merchant.permission:merchant-address-view');
        Route::put('/{merchantId}/address', UpdateMerchantAddressController::class)
            ->middleware('merchant.permission:merchant-address-update');

        // Merchant schedule management
        Route::get('/{merchantId}/schedule', GetMerchantScheduleController::class)
            ->middleware('merchant.permission:merchant-schedule-view');
        Route::put('/{merchantId}/schedule', UpdateMerchantScheduleController::class)
            ->middleware('merchant.permission:merchant-schedule-update');

        // Merchant settings management
        Route::get('/{merchantId}/settings', GetMerchantSettingsController::class)
            ->middleware('merchant.permission:merchant-setting-view');
        Route::put('/{merchantId}/settings', UpdateMerchantSettingsController::class)
            ->middleware('merchant.permission:merchant-setting-update');
    });
});
