<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Merchant Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class for the merchant module.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus tanggal setelah :date.',
    'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
        'file' => ':attribute harus antara :min dan :max kilobyte.',
        'string' => ':attribute harus antara :min dan :max karakter.',
        'array' => ':attribute harus memiliki antara :min dan :max item.',
    ],
    'boolean' => ':attribute harus bernilai true atau false.',
    'confirmed' => 'Konfirmasi :attribute nggak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus tanggal yang sama dengan :date.',
    'date_format' => ':attribute nggak sesuai format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus antara :min dan :max digit.',
    'dimensions' => ':attribute punya dimensi gambar yang nggak valid.',
    'distinct' => ':attribute punya nilai duplikat.',
    'email' => ':attribute harus alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari: :values.',
    'exists' => ':attribute yang dipilih nggak valid.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute harus punya nilai.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file' => ':attribute harus lebih besar dari :value kilobyte.',
        'string' => ':attribute harus lebih dari :value karakter.',
        'array' => ':attribute harus punya lebih dari :value item.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'file' => ':attribute harus lebih besar atau sama dengan :value kilobyte.',
        'string' => ':attribute harus lebih dari atau sama dengan :value karakter.',
        'array' => ':attribute harus punya :value item atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih nggak valid.',
    'in_array' => ':attribute nggak ada di :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus alamat IP yang valid.',
    'ipv4' => ':attribute harus alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus alamat IPv6 yang valid.',
    'json' => ':attribute harus string JSON yang valid.',
    'lt' => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file' => ':attribute harus kurang dari :value kilobyte.',
        'string' => ':attribute harus kurang dari :value karakter.',
        'array' => ':attribute harus punya kurang dari :value item.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'file' => ':attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string' => ':attribute harus kurang dari atau sama dengan :value karakter.',
        'array' => ':attribute harus punya :value item atau kurang.',
    ],
    'max' => [
        'numeric' => ':attribute nggak boleh lebih dari :max.',
        'file' => ':attribute nggak boleh lebih dari :max kilobyte.',
        'string' => ':attribute nggak boleh lebih dari :max karakter.',
        'array' => ':attribute nggak boleh punya lebih dari :max item.',
    ],
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => ':attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute minimal harus :min.',
        'file' => ':attribute minimal harus :min kilobyte.',
        'string' => ':attribute minimal harus :min karakter.',
        'array' => ':attribute minimal harus punya :min item.',
    ],
    'not_in' => ':attribute yang dipilih nggak valid.',
    'not_regex' => 'Format :attribute nggak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'Password salah.',
    'present' => ':attribute harus ada.',
    'regex' => 'Format :attribute nggak valid.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi ketika :other adalah :value.',
    'required_unless' => ':attribute wajib diisi kecuali :other ada di :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi ketika :values ada.',
    'required_without' => ':attribute wajib diisi ketika :values nggak ada.',
    'required_without_all' => ':attribute wajib diisi ketika nggak ada :values.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobyte.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus berisi :size item.',
    ],
    'starts_with' => ':attribute harus diawali dengan salah satu dari: :values.',
    'string' => ':attribute harus berupa string.',
    'timezone' => ':attribute harus zona waktu yang valid.',
    'unique' => ':attribute udah digunakan.',
    'uploaded' => ':attribute gagal diupload.',
    'url' => ':attribute harus URL yang valid.',
    'uuid' => ':attribute harus UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute". This makes it quick to specify a specific
    | custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'first_name' => 'nama depan',
        'last_name' => 'nama belakang',
        'avatar' => 'avatar',
        'gender' => 'jenis kelamin',
        'language' => 'bahasa',
        'type' => 'tipe',
        'label' => 'label',
        'street' => 'jalan',
        'city' => 'kota',
        'province' => 'provinsi',
        'postal_code' => 'kode pos',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'is_primary' => 'alamat utama',
        'file' => 'file',
        'expiry_date' => 'tanggal kedaluwarsa',
        'id_card_file' => 'file KTP',
        'id_card_meta' => 'metadata KTP',
        'id_card_meta.name' => 'nama KTP',
        'id_card_meta.number' => 'nomor KTP',
        'id_card_expiry_date' => 'tanggal kedaluwarsa KTP',
        'selfie_with_id_card_file' => 'file selfie dengan KTP',
        'selfie_with_id_card_meta' => 'metadata selfie',
        'avatar_file' => 'file avatar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation for Profile Verification
    |--------------------------------------------------------------------------
    */

    'profile_verification' => [
        'id_card_file' => [
            'required' => 'File KTP wajib diisi.',
            'file' => 'KTP harus berupa file yang valid.',
            'mimes' => 'KTP harus berupa file dengan tipe: jpeg, jpg, png, pdf.',
            'max' => 'File KTP nggak boleh lebih dari 5MB.',
        ],
        'id_card_meta' => [
            'required' => 'Metadata KTP wajib diisi.',
            'array' => 'Metadata KTP harus berupa array.',
            'name' => [
                'required' => 'Nama KTP wajib diisi.',
            ],
            'number' => [
                'required' => 'Nomor KTP wajib diisi.',
            ],
        ],
        'id_card_expiry_date' => [
            'date' => 'Tanggal kedaluwarsa KTP harus tanggal yang valid.',
            'after' => 'Tanggal kedaluwarsa KTP harus setelah hari ini.',
        ],
        'selfie_with_id_card_file' => [
            'required' => 'File selfie dengan KTP wajib diisi.',
            'file' => 'Selfie harus berupa file yang valid.',
            'mimes' => 'Selfie harus berupa file dengan tipe: jpeg, jpg, png.',
            'max' => 'File selfie nggak boleh lebih dari 5MB.',
        ],
        'selfie_with_id_card_meta' => [
            'array' => 'Metadata selfie harus berupa array.',
        ],
    ],

    'first_name' => [
        'string' => 'Nama depan harus berupa string.',
        'max' => 'Nama depan nggak boleh lebih dari 255 karakter.',
    ],
    'last_name' => [
        'string' => 'Nama belakang harus berupa string.',
        'max' => 'Nama belakang nggak boleh lebih dari 255 karakter.',
    ],
    'avatar_file' => [
        'file' => 'Avatar harus berupa file yang valid.',
        'mimes' => 'Avatar harus berupa file dengan tipe: jpeg, jpg, png.',
        'max' => 'File avatar nggak boleh lebih dari 5MB.',
    ],
    'gender' => [
        'in' => 'Jenis kelamin yang dipilih nggak valid.',
    ],
    'language' => [
        'string' => 'Bahasa harus berupa string.',
        'max' => 'Bahasa nggak boleh lebih dari 10 karakter.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Address Validation
    |--------------------------------------------------------------------------
    */

    'type' => [
        'required' => 'Tipe alamat wajib diisi.',
        'in' => 'Tipe alamat yang dipilih nggak valid.',
    ],
    'label' => [
        'required' => 'Label alamat wajib diisi.',
        'max' => 'Label alamat nggak boleh lebih dari :max karakter.',
    ],
    'street' => [
        'required' => 'Alamat jalan wajib diisi.',
        'max' => 'Alamat jalan nggak boleh lebih dari :max karakter.',
    ],
    'city' => [
        'required' => 'Kota wajib diisi.',
        'max' => 'Kota nggak boleh lebih dari :max karakter.',
    ],
    'province' => [
        'required' => 'Provinsi wajib diisi.',
        'max' => 'Provinsi nggak boleh lebih dari :max karakter.',
    ],
    'postal_code' => [
        'required' => 'Kode pos wajib diisi.',
        'max' => 'Kode pos nggak boleh lebih dari :max karakter.',
        'regex' => 'Format kode pos nggak valid.',
    ],
    'latitude' => [
        'numeric' => 'Latitude harus berupa angka.',
        'between' => 'Latitude harus antara -90 dan 90 derajat.',
    ],
    'longitude' => [
        'numeric' => 'Longitude harus berupa angka.',
        'between' => 'Longitude harus antara -180 dan 180 derajat.',
    ],

    'failed' => 'Waduh, validasi gagal nih.',
];
