<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during driver operations
    | for various messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'auth' => [
        'user_not_authenticated' => 'User is not authenticated.',
        'insufficient_permissions' => 'You do not have sufficient permissions to perform this action.',
        'token' => [
            'missing' => 'An access token is required.',
            'invalid' => 'That token isn\'t valid.',
        ],
    ],

    'status' => [
        'retrieved_successfully' => 'Driver status retrieved successfully.',
        'updated_successfully' => 'Driver status updated successfully.',
        'online' => 'Driver is now online.',
        'offline' => 'Driver is now offline.',
        'operational' => 'Driver is now operational.',
        'not_operational' => 'Driver is now not operational.',
        'location_updated' => 'Driver location updated successfully.',
        'service_updated' => 'Driver service type updated successfully.',
    ],

    'availability' => [
        'retrieved_successfully' => 'Driver availability retrieved successfully.',
        'updated_successfully' => 'Driver availability updated successfully.',
        'available' => 'Driver is now available.',
        'unavailable' => 'Driver is now unavailable.',
        'not_found' => 'Driver availability status not found.',
    ],

    'errors' => [
        'driver_not_found' => 'Driver not found.',
        'driver_inactive' => 'Driver account is inactive.',
        'driver_suspended' => 'Driver account is suspended.',
        'profile_not_found' => 'Driver profile not found.',
        'vehicle_not_found' => 'Vehicle not found.',
        'document_not_found' => 'Document not found.',
        'invalid_status' => 'Invalid driver status.',
        'invalid_availability' => 'Invalid driver availability.',
        'location_update_failed' => 'Failed to update driver location.',
        'status_update_failed' => 'Failed to update driver status.',
    ],

    'validation' => [
        'phone_required' => 'Phone number is required.',
        'phone_invalid' => 'Phone number format is invalid.',
        'address_required' => 'Address is required.',
        'city_required' => 'City is required.',
        'postal_code_required' => 'Postal code is required.',
        'license_number_required' => 'License number is required.',
        'license_number_invalid' => 'License number format is invalid.',
        'license_expired' => 'License has expired.',
        'vehicle_type_required' => 'Vehicle type is required.',
        'vehicle_plate_required' => 'Vehicle plate number is required.',
        'vehicle_plate_invalid' => 'Vehicle plate number format is invalid.',
        'vehicle_year_required' => 'Vehicle year is required.',
        'vehicle_year_invalid' => 'Vehicle year must be between 1900 and current year.',
        'vehicle_color_required' => 'Vehicle color is required.',
        'vehicle_model_required' => 'Vehicle model is required.',
        'vehicle_brand_required' => 'Vehicle brand is required.',
    ],

    'success' => [
        'profile_created' => 'Driver profile created successfully.',
        'profile_updated' => 'Driver profile updated successfully.',
        'vehicle_registered' => 'Vehicle registered successfully.',
        'vehicle_updated' => 'Vehicle updated successfully.',
        'document_uploaded' => 'Document uploaded successfully.',
        'verification_submitted' => 'Verification submitted successfully.',
        'status_changed' => 'Driver status changed successfully.',
    ],

    'info' => [
        'profile_pending_verification' => 'Your profile is pending verification.',
        'vehicle_pending_verification' => 'Your vehicle is pending verification.',
        'documents_required' => 'Please upload all required documents.',
        'onboarding_complete' => 'Onboarding process completed.',
        'welcome_message' => 'Welcome to our driver platform.',
    ],
];
