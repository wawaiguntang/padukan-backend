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

    'failed' => 'Kredensial ini tidak cocok dengan catatan kami.',
    'password' => 'Kata sandi yang diberikan salah.',
    'throttle' => 'Terlalu banyak upaya login. Silakan coba lagi dalam :seconds detik.',

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Messages
    |--------------------------------------------------------------------------
    */

    'registration' => [
        'success' => 'Pengguna berhasil didaftarkan.',
        'failed' => 'Pendaftaran pengguna gagal.',
    ],

    'login' => [
        'success' => 'Login berhasil.',
        'failed' => 'Login gagal.',
    ],

    'logout' => [
        'success' => 'Logout berhasil.',
        'failed' => 'Logout gagal.',
    ],

    'user' => [
        'not_found' => 'Pengguna tidak ditemukan.',
        'not_authenticated' => 'Pengguna belum terautentikasi.',
        'phone_already_exists' => 'Nomor telepon sudah digunakan.',
        'email_already_exists' => 'Alamat email sudah digunakan.',
        'already_exists' => 'Pengguna sudah ada.',
    ],

    'invalid_credentials' => 'Kredensial yang diberikan tidak valid.',
    'rate_limit' => [
        'exceeded' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
    ],
    'token' => [
        'invalid_refresh_token' => 'Token refresh tidak valid.',
        'refresh_failed' => 'Refresh token gagal.',
        'refreshed' => 'Token berhasil diperbarui.',
        'refresh_token_required' => 'Token refresh diperlukan.',
        'invalid' => 'Token tidak valid.',
    ],

    'otp' => [
        'sent' => 'OTP berhasil dikirim.',
        'send_failed' => 'Gagal mengirim OTP.',
        'resent' => 'OTP berhasil dikirim ulang.',
        'resend_failed' => 'Gagal mengirim ulang OTP.',
        'validated' => 'OTP berhasil divalidasi.',
        'validation_failed' => 'Validasi OTP gagal.',
        'invalid' => 'OTP tidak valid.',
        'invalid_format' => 'OTP harus terdiri dari 6 digit.',
        'expired' => 'OTP telah kedaluwarsa.',
        'rate_limit_exceeded' => 'Terlalu banyak permintaan OTP. Silakan coba lagi nanti.',
    ],

    'password_reset' => [
        'sent' => 'Tautan reset kata sandi telah dikirim.',
        'invalid_token' => 'Token reset kata sandi tidak valid.',
        'failed' => 'Reset kata sandi gagal.',
        'success' => 'Kata sandi berhasil direset.',
    ],

    'profile' => [
        'retrieved' => 'Profil berhasil diambil.',
        'failed' => 'Gagal mengambil profil.',
    ],
];