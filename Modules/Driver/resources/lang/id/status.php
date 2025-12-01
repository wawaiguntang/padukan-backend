<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Status Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for driver status related
    | messages in the driver module.
    |
    */

    // Status Messages
    'retrieved_successfully' => 'Status driver berhasil diambil',
    'online_status_updated' => 'Status online berhasil diperbarui',
    'operational_status_updated' => 'Status operasional berhasil diperbarui',
    'active_service_updated' => 'Layanan aktif berhasil diperbarui',
    'location_updated' => 'Lokasi berhasil diperbarui',
    'update_failed' => 'Gagal memperbarui status',

    // Permission Messages
    'cannot_update_online_status' => 'Anda tidak dapat memperbarui status online',
    'cannot_update_operational_status' => 'Anda tidak dapat memperbarui status operasional',
    'operational_status_system_controlled' => 'Status operasional dikontrol oleh sistem',
    'cannot_set_active_service' => 'Anda tidak dapat mengatur layanan aktif ini',
    'service_not_available_for_vehicles' => 'Layanan ini tidak tersedia dengan kendaraan terverifikasi Anda',
    'vehicle_not_found_or_not_verified' => 'Kendaraan tidak ditemukan atau belum terverifikasi',
    'vehicle_id' => [
        'required' => 'ID Kendaraan diperlukan saat online',
        'uuid' => 'ID Kendaraan harus berupa UUID yang valid',
        'exists' => 'Kendaraan yang dipilih tidak ada',
    ],
    'cannot_update_active_service' => 'Anda tidak dapat memperbarui layanan aktif',
    'cannot_update_location' => 'Anda tidak dapat memperbarui lokasi',

    // Attributes
    'online_status' => 'Status Online',
    'operational_status' => 'Status Operasional',
    'active_service' => 'Layanan Aktif',
    'vehicle_id' => 'ID Kendaraan',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',

    // Validation Messages
    'validation' => [
        'online_status' => [
            'required' => 'Status online wajib diisi',
            'in' => 'Status online harus online atau offline',
        ],
        'operational_status' => [
            'required' => 'Status operasional wajib diisi',
            'in' => 'Status operasional harus available, on_order, atau rest',
        ],
        'active_service' => [
            'required' => 'Layanan aktif wajib diisi',
            'in' => 'Layanan aktif harus salah satu dari: food, ride, car, send, mart',
        ],
        'latitude' => [
            'required' => 'Latitude wajib diisi',
            'numeric' => 'Latitude harus berupa angka',
            'between' => 'Latitude harus antara -90 sampai 90 derajat',
        ],
        'longitude' => [
            'required' => 'Longitude wajib diisi',
            'numeric' => 'Longitude harus berupa angka',
            'between' => 'Longitude harus antara -180 sampai 180 derajat',
        ],
    ],
];
