<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during driver authentication
    | for various messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'We couldn\'t find a match for those driver credentials.',
    'password' => 'That password doesn\'t seem right.',
    'throttle' => 'Too many driver login attempts. Please wait :seconds seconds before trying again.',

    /*
    |--------------------------------------------------------------------------
    | Custom Driver Authentication Messages
    |--------------------------------------------------------------------------
    */

    'driver' => [
        'dashboard_retrieved' => 'Driver dashboard retrieved successfully.',
        'onboarding_status_retrieved' => 'Onboarding status retrieved successfully.',
        'onboarding_completed' => 'Onboarding completed successfully. Your application has been submitted for verification.',
        'onboarding_incomplete' => 'Please complete all requirements before submitting for verification.',
        'verification_pending' => 'Your verification is pending review.',
        'verification_approved' => 'Your account has been verified and approved.',
        'verification_rejected' => 'Your verification was rejected. Please check your documents and try again.',
        'not_found' => 'Driver not found.',
        'inactive' => 'Your driver account is inactive.',
        'suspended' => 'Your driver account has been suspended.',
        'onboarding_required' => 'Please complete the driver onboarding process.',
    ],

    'profile' => [
        'retrieved_successfully' => 'Profile retrieved successfully.',
        'updated_successfully' => 'Profile updated successfully.',
        'update_failed' => 'Failed to update profile.',
        'not_found' => 'Profile not found.',
        'already_exists' => 'Profile already exists for this user.',
        'verification' => [
            'submitted_successfully' => 'Profile verification submitted successfully.',
            'submission_failed' => 'Failed to submit profile verification.',
            'resubmitted_successfully' => 'Profile verification resubmitted successfully.',
            'resubmission_failed' => 'Failed to resubmit profile verification.',
            'status_retrieved' => 'Profile verification status retrieved successfully.',
            'cannot_submit' => 'Cannot submit verification at this time.',
            'resubmit_not_allowed' => 'Resubmission is not allowed.',
        ],
    ],

    'document' => [
        'retrieved_successfully' => 'Documents retrieved successfully.',
        'uploaded_successfully' => 'Document uploaded successfully.',
        'upload_failed' => 'Failed to upload document.',
        'updated_successfully' => 'Document updated successfully.',
        'update_failed' => 'Failed to update document.',
        'deleted_successfully' => 'Document deleted successfully.',
        'delete_failed' => 'Failed to delete document.',
        'not_found' => 'Document not found.',
        'access_denied' => 'Access denied to this document.',
    ],

    'vehicle' => [
        'retrieved_successfully' => 'Vehicles retrieved successfully.',
        'created_successfully' => 'Vehicle created successfully.',
        'create_failed' => 'Failed to create vehicle.',
        'updated_successfully' => 'Vehicle updated successfully.',
        'update_failed' => 'Failed to update vehicle.',
        'deleted_successfully' => 'Vehicle deleted successfully.',
        'delete_failed' => 'Failed to delete vehicle.',
        'not_found' => 'Vehicle not found.',
        'access_denied' => 'Access denied to this vehicle.',
        'registered_successfully' => 'Vehicle registered successfully.',
        'registration_failed' => 'Failed to register vehicle.',
        'limit_exceeded' => 'Maximum vehicle limit exceeded.',
        'update_not_allowed' => 'Vehicle update is not allowed at this time.',
        'verification' => [
            'submitted_successfully' => 'Vehicle verification submitted successfully.',
            'submission_failed' => 'Failed to submit vehicle verification.',
            'resubmitted_successfully' => 'Vehicle verification resubmitted successfully.',
            'resubmission_failed' => 'Failed to resubmit vehicle verification.',
            'status_retrieved' => 'Vehicle verification status retrieved successfully.',
            'cannot_submit' => 'Cannot submit vehicle verification at this time.',
            'resubmit_not_allowed' => 'Vehicle verification resubmission is not allowed.',
        ],
    ],

    'file' => [
        'upload_failed' => 'File upload failed.',
        'too_large' => 'File is too large.',
        'invalid_type' => 'Invalid file type.',
        'avatar' => [
            'not_image' => 'Avatar must be an image file.',
            'invalid_dimensions' => 'Avatar dimensions are invalid.',
        ],
        'document' => [
            'invalid_type' => 'Invalid document file type.',
        ],
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
    'user_not_authenticated' => 'User is not authenticated.',
    'insufficient_permissions' => 'You do not have sufficient permissions to perform this action.',
];
