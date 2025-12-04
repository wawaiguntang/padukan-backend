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
        'id_card' => 'KTP',
        'sim' => 'Surat Izin Mengemudi (SIM)',
        'stnk' => 'Surat Tanda Nomor Kendaraan (STNK)',
        'vehicle_photo' => 'Foto Kendaraan',
        'selfie_with_id_card' => 'Selfie dengan KTP',
    ],

    // Online Status Enum
    'online_status' => [
        'online' => 'Online',
        'offline' => 'Offline',
    ],

    // Operational Status Enum
    'operational_status' => [
        'available' => 'Tersedia',
        'on_order' => 'Sedang Order',
        'rest' => 'Istirahat',
        'suspended' => 'Ditangguhkan',
    ],

    // Verification Status Enum
    'verification_status' => [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'on_review' => 'Sedang Ditinjau',
    ],

    // Gender Enum
    'gender' => [
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
        'other' => 'Lainnya',
    ],

    // Vehicle Type Enum
    'vehicle_type' => [
        'motorcycle' => 'Motor',
        'car' => 'Mobil',
    ],
];
