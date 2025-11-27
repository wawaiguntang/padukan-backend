<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\ProfileController;
use Modules\Profile\Http\Controllers\Customer\GetProfileController;
use Modules\Profile\Http\Controllers\Customer\UpdateProfileController;
use Modules\Profile\Http\Controllers\Customer\GetAddressesController;
use Modules\Profile\Http\Controllers\Customer\CreateAddressController;
use Modules\Profile\Http\Controllers\Customer\GetAddressController;
use Modules\Profile\Http\Controllers\Customer\UpdateAddressController;
use Modules\Profile\Http\Controllers\Customer\DeleteAddressController;
use Modules\Profile\Http\Controllers\Customer\GetDocumentsController;
use Modules\Profile\Http\Controllers\Customer\CreateDocumentController;
use Modules\Profile\Http\Controllers\Customer\GetDocumentController;
use Modules\Profile\Http\Controllers\Customer\UpdateDocumentController;
use Modules\Profile\Http\Controllers\Customer\DeleteDocumentController;
use Modules\Profile\Http\Controllers\Customer\GetDocumentFileController;
use Modules\Profile\Http\Controllers\Driver\GetProfileController as GetDriverProfileController;
use Modules\Profile\Http\Controllers\Driver\UpdateProfileController as UpdateDriverProfileController;
use Modules\Profile\Http\Controllers\Merchant\MerchantVerificationController;
use Modules\Profile\Http\Controllers\Merchant\MerchantBankController;
use Modules\Profile\Http\Controllers\Merchant\MerchantAddressController;

Route::middleware(['profile.auth'])->prefix('v1')->group(function () {
    // Customer routes
    Route::prefix('customer')->group(function () {
        // Profile management
        Route::get('/profile', [GetProfileController::class, 'getProfile'])
            ->middleware('profile.can:customer.profile.view');
        Route::put('/profile', [UpdateProfileController::class, 'updateProfile'])
            ->middleware('profile.can:customer.profile.update');

        // Address management
        Route::get('/addresses', [GetAddressesController::class, 'getAddresses'])
            ->middleware('profile.can:customer.address.view');
        Route::post('/addresses', [CreateAddressController::class, 'createAddress'])
            ->middleware('profile.can:customer.address.create');
        Route::get('/addresses/{addressId}', [GetAddressController::class, 'getAddress'])
            ->middleware('profile.can:customer.address.view');
        Route::put('/addresses/{addressId}', [UpdateAddressController::class, 'updateAddress'])
            ->middleware('profile.can:customer.address.update');
        Route::delete('/addresses/{addressId}', [DeleteAddressController::class, 'deleteAddress'])
            ->middleware('profile.can:customer.address.delete');

        // Document management
        Route::get('/documents', [GetDocumentsController::class, 'getDocuments'])
            ->middleware('profile.can:customer.document.view');
        Route::post('/documents', [CreateDocumentController::class, 'createDocument'])
            ->middleware('profile.can:customer.document.upload');
        Route::get('/documents/{documentId}', [GetDocumentController::class, 'getDocument'])
            ->middleware('profile.can:customer.document.view');
        Route::put('/documents/{documentId}', [UpdateDocumentController::class, 'updateDocument'])
            ->middleware('profile.can:customer.document.update');
        Route::delete('/documents/{documentId}', [DeleteDocumentController::class, 'deleteDocument'])
            ->middleware('profile.can:customer.document.delete');
        Route::get('/documents/{documentId}/download', [GetDocumentFileController::class, 'getDocumentFile'])
            ->middleware('profile.can:customer.document.view');
    });

    // Driver routes
    Route::prefix('driver')->group(function () {
        // Profile management
        Route::get('/profile', [GetDriverProfileController::class, 'getProfile'])
            ->middleware('profile.can:driver.profile.view');
        Route::put('/profile', [UpdateDriverProfileController::class, 'updateProfile'])
            ->middleware('profile.can:driver.profile.update');

        // Vehicle management (business logic)
        Route::get('/vehicles', [\Modules\Profile\Http\Controllers\Driver\DriverVehicleController::class, 'getVehicles'])
            ->middleware('profile.can:driver.vehicle.view');
        Route::post('/vehicles/register', [\Modules\Profile\Http\Controllers\Driver\DriverVehicleController::class, 'registerVehicle'])
            ->middleware('profile.can:driver.vehicle.create');
        Route::put('/vehicles/{vehicleId}', [\Modules\Profile\Http\Controllers\Driver\DriverVehicleController::class, 'updateVehicle'])
            ->middleware('profile.can:driver.vehicle.update');
        Route::delete('/vehicles/{vehicleId}', [\Modules\Profile\Http\Controllers\Driver\DriverVehicleController::class, 'removeVehicle'])
            ->middleware('profile.can:driver.vehicle.delete');

        // Document management (business logic - all types mandatory)
        Route::get('/documents', [\Modules\Profile\Http\Controllers\Driver\DriverVerificationController::class, 'getDocuments'])
            ->middleware('profile.can:driver.document.view');
        Route::post('/documents/submit-all', [\Modules\Profile\Http\Controllers\Driver\DriverVerificationController::class, 'submitDocuments'])
            ->middleware('profile.can:driver.document.upload');
        Route::get('/documents/{documentId}/download', [\Modules\Profile\Http\Controllers\Driver\DriverVerificationController::class, 'getDocumentFile'])
            ->middleware('profile.can:driver.document.view');

        // Verification (business logic)
        Route::post('/verification/request', [\Modules\Profile\Http\Controllers\Driver\DriverVerificationController::class, 'requestVerification'])
            ->middleware('profile.can:driver.verification.request');
        Route::get('/verification/status', [\Modules\Profile\Http\Controllers\Driver\DriverVerificationController::class, 'getVerificationStatus'])
            ->middleware('profile.can:driver.verification.view');
    });

    // Merchant routes
    Route::prefix('merchant')->group(function () {
        // Business address management
        Route::get('/address', [MerchantAddressController::class, 'getAddress'])
            ->middleware('profile.can:merchant.address.view');
        Route::post('/address', [MerchantAddressController::class, 'createAddress'])
            ->middleware('profile.can:merchant.address.create');
        Route::put('/address', [MerchantAddressController::class, 'updateAddress'])
            ->middleware('profile.can:merchant.address.update');

        // Bank account management
        Route::get('/banks', [MerchantBankController::class, 'getBanks'])
            ->middleware('profile.can:merchant.bank.view');
        Route::post('/banks', [MerchantBankController::class, 'createBank'])
            ->middleware('profile.can:merchant.bank.create');
        Route::put('/banks/{bankId}', [MerchantBankController::class, 'updateBank'])
            ->middleware('profile.can:merchant.bank.update');
        Route::delete('/banks/{bankId}', [MerchantBankController::class, 'deleteBank'])
            ->middleware('profile.can:merchant.bank.delete');

        // Document management (business logic - mandatory for verification)
        Route::get('/documents', [MerchantVerificationController::class, 'getDocuments'])
            ->middleware('profile.can:merchant.document.view');
        Route::post('/documents/submit-all', [MerchantVerificationController::class, 'submitDocuments'])
            ->middleware('profile.can:merchant.document.upload');
        Route::get('/documents/{documentId}/download', [MerchantVerificationController::class, 'getDocumentFile'])
            ->middleware('profile.can:merchant.document.view');

        // Verification (business logic)
        Route::post('/verification/request', [MerchantVerificationController::class, 'requestVerification'])
            ->middleware('profile.can:merchant.verification.request');
        Route::get('/verification/status', [MerchantVerificationController::class, 'getVerificationStatus'])
            ->middleware('profile.can:merchant.verification.view');
    });
});
