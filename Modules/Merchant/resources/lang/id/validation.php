<?php

return [
    'first_name' => [
        'string' => 'Nama depan harus berupa string',
        'max' => 'Nama depan tidak boleh lebih dari :max karakter',
    ],
    'last_name' => [
        'string' => 'Nama belakang harus berupa string',
        'max' => 'Nama belakang tidak boleh lebih dari :max karakter',
    ],
    'gender' => [
        'in' => 'Jenis kelamin yang dipilih tidak valid',
    ],
    'language' => [
        'string' => 'Bahasa harus berupa string',
        'max' => 'Bahasa tidak boleh lebih dari :max karakter',
    ],
    'avatar_file' => [
        'image' => 'Avatar harus berupa gambar',
        'mimes' => 'Avatar harus berupa file dengan tipe: :values',
        'max' => 'Avatar tidak boleh lebih dari :max kilobyte',
    ],
];
