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

    'failed' => 'We couldn\'t find a match for those credentials.',
    'password' => 'That password doesn\'t seem right.',
    'throttle' => 'Too many login attempts. Please wait :seconds seconds before trying again.',

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Messages
    |--------------------------------------------------------------------------
    */

    'registration' => [
        'success' => 'Welcome! Your registration is complete.',
        'failed' => 'Sorry, registration failed. Please try again.',
    ],

    'login' => [
        'success' => 'You\'re logged in!',
        'failed' => 'Login failed. Please check your credentials.',
    ],

    'logout' => [
        'success' => 'You\'ve been logged out.',
        'failed' => 'Logout failed.',
    ],

    'user' => [
        'not_found' => 'We couldn\'t find that user.',
        'not_authenticated' => 'Please log in first.',
        'phone_already_exists' => 'That phone number is already in use.',
        'email_already_exists' => 'That email is already taken.',
        'already_exists' => 'That user already exists.',
        'pending_verification' => 'Your account is pending verification.',
        'account_suspended' => 'Your account has been suspended.',
        'inactive' => 'Your account is inactive.',
    ],

    'invalid_credentials' => 'Those credentials aren\'t valid.',
    'rate_limit' => [
        'exceeded' => 'Too many requests. Please try again later.',
    ],
    'token' => [
        'invalid_refresh_token' => 'That refresh token isn\'t valid.',
        'refresh_failed' => 'Token refresh failed.',
        'refreshed' => 'Token refreshed successfully.',
        'refresh_token_required' => 'A refresh token is required.',
        'invalid' => 'That token isn\'t valid.',
        'missing' => 'An access token is required.',
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
