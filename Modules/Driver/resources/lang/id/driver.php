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
        'user_not_authenticated' => 'Pengguna tidak terautentikasi.',
        'insufficient_permissions' => 'Anda tidak memiliki izin yang cukup untuk melakukan tindakan ini.',
        'token' => [
            'missing' => 'Token akses diperlukan.',
            'invalid' => 'Token tersebut tidak valid.',
        ],
    ],

    'status' => [
        'retrieved_successfully' => 'Status driver berhasil diambil.',
        'updated_successfully' => 'Status driver berhasil diperbarui.',
        'online' => 'Driver sekarang online.',
        'offline' => 'Driver sekarang offline.',
        'operational' => 'Driver sekarang beroperasi.',
        'not_operational' => 'Driver sekarang tidak beroperasi.',
        'location_updated' => 'Lokasi driver berhasil diperbarui.',
        'service_updated' => 'Tipe layanan driver berhasil diperbarui.',
    ],

    'availability' => [
        'retrieved_successfully' => 'Ketersediaan driver berhasil diambil.',
        'updated_successfully' => 'Ketersediaan driver berhasil diperbarui.',
        'available' => 'Driver sekarang tersedia.',
        'unavailable' => 'Driver sekarang tidak tersedia.',
        'not_found' => 'Status ketersediaan driver tidak ditemukan.',
    ],

    'errors' => [
        'driver_not_found' => 'Driver tidak ditemukan.',
        'driver_inactive' => 'Akun driver tidak aktif.',
        'driver_suspended' => 'Akun driver ditangguhkan.',
        'profile_not_found' => 'Profil driver tidak ditemukan.',
        'vehicle_not_found' => 'Kendaraan tidak ditemukan.',
        'document_not_found' => 'Dokumen tidak ditemukan.',
        'invalid_status' => 'Status driver tidak valid.',
        'invalid_availability' => 'Ketersediaan driver tidak valid.',
        'location_update_failed' => 'Gagal memperbarui lokasi driver.',
        'status_update_failed' => 'Gagal memperbarui status driver.',
    ],

    'validation' => [
        'phone_required' => 'Nomor telepon diperlukan.',
        'phone_invalid' => 'Format nomor telepon tidak valid.',
        'address_required' => 'Alamat diperlukan.',
        'city_required' => 'Kota diperlukan.',
        'postal_code_required' => 'Kode pos diperlukan.',
        'license_number_required' => 'Nomor lisensi diperlukan.',
        'license_number_invalid' => 'Format nomor lisensi tidak valid.',
        'license_expired' => 'Lisensi telah kedaluwarsa.',
        'vehicle_type_required' => 'Tipe kendaraan diperlukan.',
        'vehicle_plate_required' => 'Nomor plat kendaraan diperlukan.',
        'vehicle_plate_invalid' => 'Format nomor plat kendaraan tidak valid.',
        'vehicle_year_required' => 'Tahun kendaraan diperlukan.',
        'vehicle_year_invalid' => 'Tahun kendaraan harus antara 1900 dan tahun sekarang.',
        'vehicle_color_required' => 'Warna kendaraan diperlukan.',
        'vehicle_model_required' => 'Model kendaraan diperlukan.',
        'vehicle_brand_required' => 'Merek kendaraan diperlukan.',
    ],

    'success' => [
        'profile_created' => 'Profil driver berhasil dibuat.',
        'profile_updated' => 'Profil driver berhasil diperbarui.',
        'vehicle_registered' => 'Kendaraan berhasil didaftarkan.',
        'vehicle_updated' => 'Kendaraan berhasil diperbarui.',
        'document_uploaded' => 'Dokumen berhasil diunggah.',
        'verification_submitted' => 'Verifikasi berhasil dikirim.',
        'status_changed' => 'Status driver berhasil diubah.',
    ],

    'info' => [
        'profile_pending_verification' => 'Profil Anda sedang menunggu verifikasi.',
        'vehicle_pending_verification' => 'Kendaraan Anda sedang menunggu verifikasi.',
        'documents_required' => 'Silakan unggah semua dokumen yang diperlukan.',
        'onboarding_complete' => 'Proses orientasi selesai.',
        'welcome_message' => 'Selamat datang di platform driver kami.',
    ],
];
