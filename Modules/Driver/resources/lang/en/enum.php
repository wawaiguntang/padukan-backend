<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Enum Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for enum values in the driver module.
    | These translations provide human-readable labels for enum values.
    |
    */

    // Document Type Enum
    'document_type' => [
        'id_card' => 'ID Card',
        'sim' => 'Driver License (SIM)',
        'stnk' => 'Vehicle Registration (STNK)',
        'vehicle_photo' => 'Vehicle Photo',
        'selfie_with_id_card' => 'Selfie with ID Card',
    ],

    // Online Status Enum
    'online_status' => [
        'online' => 'Online',
        'offline' => 'Offline',
    ],

    // Operational Status Enum
    'operational_status' => [
        'available' => 'Available',
        'on_order' => 'On Order',
        'rest' => 'Rest',
        'suspended' => 'Suspended',
    ],

    // Verification Status Enum
    'verification_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'on_review' => 'On Review',
    ],

    // Gender Enum
    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
    ],

    // Vehicle Type Enum
    'vehicle_type' => [
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
    ],
];
