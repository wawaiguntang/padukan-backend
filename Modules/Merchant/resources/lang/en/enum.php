<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Merchant Enum Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for enum values in the merchant module.
    | These translations provide human-readable labels for enum values.
    |
    */

    // Address Type Enum
    'address_type' => [
        'home' => 'Home',
        'work' => 'Work',
        'business' => 'Business',
        'other' => 'Other',
    ],

    // Document Type Enum
    'document_type' => [
        'id_card' => 'ID Card',
        'selfie_with_id_card' => 'Selfie with ID Card',
        'other' => 'Other Document',
        'merchant' => 'Merchant Document',
        'banner' => 'Banner Image',
    ],

    // Gender Enum
    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
    ],

    // Verification Status Enum
    'verification_status' => [
        'pending' => 'Pending',
        'on_review' => 'On Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],
];
