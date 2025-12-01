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

    'failed' => 'Kami tidak dapat menemukan kecocokan untuk kredensial driver tersebut.',
    'password' => 'Password tersebut sepertinya tidak benar.',
    'throttle' => 'Terlalu banyak percobaan login driver. Silakan tunggu :seconds detik sebelum mencoba lagi.',

    /*
    |--------------------------------------------------------------------------
    | Custom Driver Authentication Messages
    |--------------------------------------------------------------------------
    */

    'driver' => [
        'dashboard_retrieved' => 'Dashboard driver berhasil diambil.',
        'onboarding_status_retrieved' => 'Status onboarding berhasil diambil.',
        'onboarding_completed' => 'Onboarding berhasil diselesaikan. Aplikasi Anda telah dikirim untuk verifikasi.',
        'onboarding_incomplete' => 'Silakan lengkapi semua persyaratan sebelum mengirim untuk verifikasi.',
        'verification_pending' => 'Verifikasi Anda sedang menunggu peninjauan.',
        'verification_approved' => 'Akun Anda telah diverifikasi dan disetujui.',
        'verification_rejected' => 'Verifikasi Anda ditolak. Silakan periksa dokumen Anda dan coba lagi.',
        'not_found' => 'Driver tidak ditemukan.',
        'inactive' => 'Akun driver Anda tidak aktif.',
        'suspended' => 'Akun driver Anda telah ditangguhkan.',
        'onboarding_required' => 'Silakan lengkapi proses onboarding driver.',
    ],

    'profile' => [
        'retrieved_successfully' => 'Profil berhasil diambil.',
        'updated_successfully' => 'Profil berhasil diperbarui.',
        'update_failed' => 'Gagal memperbarui profil.',
        'not_found' => 'Profil tidak ditemukan.',
        'already_exists' => 'Profil sudah ada untuk pengguna ini.',
        'verification' => [
            'submitted_successfully' => 'Verifikasi profil berhasil dikirim.',
            'submission_failed' => 'Gagal mengirim verifikasi profil.',
            'resubmitted_successfully' => 'Verifikasi profil berhasil dikirim ulang.',
            'resubmission_failed' => 'Gagal mengirim ulang verifikasi profil.',
            'status_retrieved' => 'Status verifikasi profil berhasil diambil.',
            'cannot_submit' => 'Tidak dapat mengirim verifikasi saat ini.',
            'resubmit_not_allowed' => 'Pengiriman ulang tidak diizinkan.',
        ],
    ],

    'document' => [
        'retrieved_successfully' => 'Dokumen berhasil diambil.',
        'uploaded_successfully' => 'Dokumen berhasil diunggah.',
        'upload_failed' => 'Gagal mengunggah dokumen.',
        'updated_successfully' => 'Dokumen berhasil diperbarui.',
        'update_failed' => 'Gagal memperbarui dokumen.',
        'deleted_successfully' => 'Dokumen berhasil dihapus.',
        'delete_failed' => 'Gagal menghapus dokumen.',
        'not_found' => 'Dokumen tidak ditemukan.',
        'access_denied' => 'Akses ditolak untuk dokumen ini.',
    ],

    'vehicle' => [
        'retrieved_successfully' => 'Kendaraan berhasil diambil.',
        'created_successfully' => 'Kendaraan berhasil dibuat.',
        'create_failed' => 'Gagal membuat kendaraan.',
        'updated_successfully' => 'Kendaraan berhasil diperbarui.',
        'update_failed' => 'Gagal memperbarui kendaraan.',
        'deleted_successfully' => 'Kendaraan berhasil dihapus.',
        'delete_failed' => 'Gagal menghapus kendaraan.',
        'not_found' => 'Kendaraan tidak ditemukan.',
        'access_denied' => 'Akses ditolak untuk kendaraan ini.',
        'registered_successfully' => 'Kendaraan berhasil didaftarkan.',
        'registration_failed' => 'Gagal mendaftarkan kendaraan.',
        'limit_exceeded' => 'Batas maksimal kendaraan terlampaui.',
        'update_not_allowed' => 'Pembaruan kendaraan tidak diizinkan saat ini.',
        'verification' => [
            'submitted_successfully' => 'Verifikasi kendaraan berhasil dikirim.',
            'submission_failed' => 'Gagal mengirim verifikasi kendaraan.',
            'resubmitted_successfully' => 'Verifikasi kendaraan berhasil dikirim ulang.',
            'resubmission_failed' => 'Gagal mengirim ulang verifikasi kendaraan.',
            'status_retrieved' => 'Status verifikasi kendaraan berhasil diambil.',
            'cannot_submit' => 'Tidak dapat mengirim verifikasi kendaraan saat ini.',
            'resubmit_not_allowed' => 'Pengiriman ulang verifikasi kendaraan tidak diizinkan.',
        ],
    ],

    'file' => [
        'upload_failed' => 'Unggah file gagal.',
        'too_large' => 'File terlalu besar.',
        'invalid_type' => 'Tipe file tidak valid.',
        'avatar' => [
            'not_image' => 'Avatar harus berupa file gambar.',
            'invalid_dimensions' => 'Dimensi avatar tidak valid.',
        ],
        'document' => [
            'invalid_type' => 'Tipe file dokumen tidak valid.',
        ],
    ],

    'token' => [
        'missing' => 'Token autentikasi diperlukan.',
        'invalid' => 'Token yang diberikan tidak valid atau sudah kedaluwarsa.',
        'invalid_refresh_token' => 'Token refresh tersebut tidak valid.',
        'refresh_failed' => 'Pembaruan token gagal.',
        'refreshed' => 'Token berhasil diperbarui.',
        'refresh_token_required' => 'Token refresh diperlukan.',
    ],

    'otp' => [
        'sent' => 'OTP berhasil dikirim.',
        'send_failed' => 'Gagal mengirim OTP.',
        'resent' => 'OTP berhasil dikirim ulang.',
        'resend_failed' => 'Gagal mengirim ulang OTP.',
        'validated' => 'OTP berhasil divalidasi.',
        'validation_failed' => 'Validasi OTP gagal.',
        'invalid' => 'OTP tidak valid.',
        'invalid_format' => 'OTP harus berupa 6 digit.',
        'expired' => 'OTP telah kedaluwarsa.',
        'rate_limit_exceeded' => 'Terlalu banyak permintaan OTP. Silakan coba lagi nanti.',
    ],

    'user' => [
        'not_found' => 'Kami tidak dapat menemukan pengguna tersebut.',
        'not_authenticated' => 'Silakan masuk terlebih dahulu.',
        'phone_already_exists' => 'Nomor telepon tersebut sudah digunakan.',
        'email_already_exists' => 'Email tersebut sudah digunakan.',
        'already_exists' => 'Pengguna tersebut sudah ada.',
        'pending_verification' => 'Akun Anda menunggu verifikasi.',
        'account_suspended' => 'Akun Anda telah ditangguhkan.',
        'inactive' => 'Akun Anda tidak aktif.',
    ],

    'invalid_credentials' => 'Kredensial tersebut tidak valid.',
    'rate_limit' => [
        'exceeded' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
    ],
    'user_not_authenticated' => 'Pengguna tidak terautentikasi.',
    'insufficient_permissions' => 'Anda tidak memiliki izin yang cukup untuk melakukan tindakan ini.',
];
