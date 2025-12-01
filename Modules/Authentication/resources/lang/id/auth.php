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

    'failed' => 'Maaf ya, kredensial ini nggak cocok.',
    'password' => 'Maaf, kata sandi salah.',
    'throttle' => 'Login terlalu sering. Tunggu :seconds detik dulu ya.',

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Messages
    |--------------------------------------------------------------------------
    */

    'registration' => [
        'success' => 'Yeay, pendaftaran berhasil!',
        'failed' => 'Maaf, pendaftaran gagal. Coba lagi ya.',
    ],

    'login' => [
        'success' => 'Selamat datang!',
        'failed' => 'Maaf, login gagal. Periksa kredensial ya.',
    ],

    'logout' => [
        'success' => 'Oke, sudah logout.',
        'failed' => 'Maaf, logout gagal.',
    ],

    'user' => [
        'not_found' => 'Maaf, pengguna nggak ditemukan.',
        'not_authenticated' => 'Silakan login dulu ya.',
        'phone_already_exists' => 'Nomor telepon ini sudah digunakan.',
        'email_already_exists' => 'Email ini sudah digunakan.',
        'already_exists' => 'Pengguna ini sudah ada.',
        'pending_verification' => 'Sedang menunggu verifikasi.',
        'account_suspended' => 'Akun sedang ditangguhkan.',
        'inactive' => 'Akun ini nggak aktif.',
    ],

    'invalid_credentials' => 'Maaf, kredensial nggak valid.',
    'rate_limit' => [
        'exceeded' => 'Permintaan terlalu banyak. Tunggu sebentar ya.',
    ],
    'token' => [
        'invalid_refresh_token' => 'Maaf, token refresh nggak valid.',
        'refresh_failed' => 'Maaf, refresh token gagal.',
        'refreshed' => 'Oke, token sudah diperbarui.',
        'refresh_token_required' => 'Perlu token refresh.',
        'invalid' => 'Maaf, token nggak valid.',
        'missing' => 'Perlu token akses.',
    ],

    'otp' => [
        'sent' => 'Oke, OTP sudah dikirim.',
        'send_failed' => 'Maaf, gagal kirim OTP.',
        'resent' => 'Oke, OTP dikirim ulang.',
        'resend_failed' => 'Maaf, gagal kirim ulang OTP.',
        'validated' => 'Oke, OTP valid.',
        'validation_failed' => 'Maaf, OTP nggak valid.',
        'invalid' => 'Maaf, OTP salah.',
        'invalid_format' => 'OTP harus 6 digit ya.',
        'expired' => 'Maaf, OTP sudah expired.',
        'rate_limit_exceeded' => 'OTP terlalu sering. Tunggu sebentar.',
    ],

    'password_reset' => [
        'sent' => 'Oke, link reset sudah dikirim.',
        'invalid_token' => 'Maaf, token reset nggak valid.',
        'failed' => 'Maaf, reset gagal.',
        'success' => 'Oke, kata sandi sudah direset.',
    ],

    'profile' => [
        'retrieved' => 'Oke, profil sudah diambil.',
        'failed' => 'Maaf, gagal ambil profil.',
    ],

    'validation' => [
        'failed' => 'Maaf, validasi gagal.',
    ]
];