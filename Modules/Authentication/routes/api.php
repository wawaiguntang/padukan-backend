<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthenticationController;

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
|
| Here are all the authentication related API routes for the application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::prefix('v1/auth')->group(function () {

    // Public authentication routes
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/refresh-token', [AuthenticationController::class, 'refreshToken']);

    // Password reset routes
    Route::post('/forgot-password', [AuthenticationController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthenticationController::class, 'resetPassword']);

    // OTP routes with rate limiting
    Route::middleware(['throttle.otp:phone'])->group(function () {
        Route::post('/send-otp', [AuthenticationController::class, 'sendOtp']);
        Route::post('/resend-otp', [AuthenticationController::class, 'resendOtp']);
    });

    Route::post('/validate-otp', [AuthenticationController::class, 'validateOtp']);

    // Protected routes (require authentication)
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthenticationController::class, 'logout']);
        Route::get('/profile', [AuthenticationController::class, 'profile']);
    });
});
