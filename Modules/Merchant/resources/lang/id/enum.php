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
        'home' => 'Rumah',
        'work' => 'Kantor',
        'business' => 'Bisnis',
        'other' => 'Lainnya',
    ],

    // Document Type Enum
    'document_type' => [
        'id_card' => 'KTP',
        'selfie_with_id_card' => 'Selfie dengan KTP',
        'other' => 'Dokumen Lainnya',
        'merchant' => 'Dokumen Merchant',
        'banner' => 'Gambar Banner',
    ],

    // Gender Enum
    'gender' => [
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
        'other' => 'Lainnya',
    ],

    // Verification Status Enum
    'verification_status' => [
        'pending' => 'Menunggu',
        'on_review' => 'Sedang Ditinjau',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ],
];
