<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Messages
    |--------------------------------------------------------------------------
    */

    'registration' => [
        'success' => 'User registered successfully.',
        'failed' => 'User registration failed.',
    ],

    'login' => [
        'success' => 'Login successful.',
        'failed' => 'Login failed.',
    ],

    'logout' => [
        'success' => 'Logout successful.',
        'failed' => 'Logout failed.',
    ],

    'user' => [
        'not_found' => 'User not found.',
        'not_authenticated' => 'User not authenticated.',
        'phone_already_exists' => 'Phone number already exists.',
        'email_already_exists' => 'Email address already exists.',
        'already_exists' => 'User already exists.',
        'pending_verification' => 'User pending verification.',
        'account_suspended' => 'User account is suspended.',
        'inactive' => 'User account is inactive.',
    ],

    'invalid_credentials' => 'Invalid credentials provided.',
    'rate_limit' => [
        'exceeded' => 'Too many requests. Please try again later.',
    ],
    'token' => [
        'invalid_refresh_token' => 'Invalid refresh token.',
        'refresh_failed' => 'Token refresh failed.',
        'refreshed' => 'Token refreshed successfully.',
        'refresh_token_required' => 'Refresh token is required.',
        'invalid' => 'Invalid token.',
        'missing' => 'Access token is required.',
    ],

    'otp' => [
        'sent' => 'OTP sent successfully.',
        'send_failed' => 'Failed to send OTP.',
        'resent' => 'OTP resent successfully.',
        'resend_failed' => 'Failed to resend OTP.',
        'validated' => 'OTP validated successfully.',
        'validation_failed' => 'OTP validation failed.',
        'invalid' => 'Invalid OTP.',
        'invalid_format' => 'OTP must be 6 digits.',
        'expired' => 'OTP has expired.',
        'rate_limit_exceeded' => 'Too many OTP requests. Please try again later.',
    ],

    'password_reset' => [
        'sent' => 'Password reset link sent.',
        'invalid_token' => 'Invalid password reset token.',
        'failed' => 'Password reset failed.',
        'success' => 'Password reset successfully.',
    ],

    'profile' => [
        'retrieved' => 'Profile retrieved successfully.',
        'failed' => 'Failed to retrieve profile.',
    ],

    'validation' => [
        'failed' => 'Data validation failed.',
    ]
];
