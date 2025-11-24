<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\RegisterController;
use Modules\Authentication\Http\Controllers\LoginController;
use Modules\Authentication\Http\Controllers\OtpController;
use Modules\Authentication\Http\Controllers\PasswordResetController;
use Modules\Authentication\Http\Controllers\TokenController;
use Modules\Authentication\Http\Controllers\ProfileController;

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
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/refresh-token', [TokenController::class, 'refreshToken']);

    // Password reset routes
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // OTP routes with rate limiting
    Route::middleware(['throttle.otp:phone'])->group(function () {
        Route::post('/send-otp', [OtpController::class, 'sendOtp']);
        Route::post('/resend-otp', [OtpController::class, 'resendOtp']);
    });

    Route::post('/validate-otp', [OtpController::class, 'validateOtp']);

    // Protected routes (require authentication)
    Route::middleware(['jwt.auth'])->group(function () {
        Route::post('/logout', [TokenController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'profile']);
    });
});
