<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class for the customer module.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
        'file' => ':attribute harus antara :min dan :max kilobyte.',
        'string' => ':attribute harus antara :min dan :max karakter.',
        'array' => ':attribute harus memiliki antara :min dan :max item.',
    ],
    'boolean' => ':attribute harus bernilai true atau false.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak cocok dengan format :format.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus antara :min dan :max digit.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai duplikat.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari: :values.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute harus memiliki nilai.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
        'file' => ':attribute harus lebih besar dari :value kilobyte.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
        'array' => ':attribute harus memiliki lebih dari :value item.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :value.',
        'file' => ':attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'string' => ':attribute harus lebih besar dari atau sama dengan :value karakter.',
        'array' => ':attribute harus memiliki :value item atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada di :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'lt' => [
        'numeric' => ':attribute harus kurang dari :value.',
        'file' => ':attribute harus kurang dari :value kilobyte.',
        'string' => ':attribute harus kurang dari :value karakter.',
        'array' => ':attribute harus memiliki kurang dari :value item.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
        'file' => ':attribute harus kurang dari atau sama dengan :value kilobyte.',
        'string' => ':attribute harus kurang dari atau sama dengan :value karakter.',
        'array' => ':attribute harus memiliki :value item atau kurang.',
    ],
    'max' => [
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'file' => ':attribute tidak boleh lebih besar dari :max kilobyte.',
        'string' => ':attribute tidak boleh lebih besar dari :max karakter.',
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => ':attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'file' => ':attribute minimal :min kilobyte.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute harus memiliki minimal :min item.',
    ],
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'Kata sandi salah.',
    'present' => ':attribute harus ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi ketika :other adalah :value.',
    'required_unless' => ':attribute wajib diisi kecuali :other ada di :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi ketika :values ada.',
    'required_without' => ':attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi ketika tidak ada :values yang ada.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobyte.',
        'string' => ':attribute harus :size karakter.',
        'array' => ':attribute harus berisi :size item.',
    ],
    'starts_with' => ':attribute harus dimulai dengan salah satu dari: :values.',
    'string' => ':attribute harus berupa string.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'url' => ':attribute harus berupa URL yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

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

    'failed' => 'Data yang diberikan tidak valid.',

    'profile_verification' => [
        'id_card_temp_path' => [
            'required' => 'Path sementara KTP wajib diisi.',
            'string' => 'Path sementara KTP harus berupa string.',
        ],
        'selfie_with_id_card_temp_path' => [
            'required' => 'Path sementara selfie dengan ID card wajib diisi.',
            'string' => 'Path sementara selfie dengan ID card harus berupa string.',
        ],
        'id_card_file' => [
            'required' => 'File KTP wajib diisi.',
            'file' => 'KTP harus berupa file yang valid.',
            'mimes' => 'KTP harus berupa file dengan tipe: :values.',
            'max' => 'KTP tidak boleh lebih besar dari :max kilobyte.',
        ],
        'selfie_with_id_card_file' => [
            'required' => 'File selfie dengan ID card wajib diisi.',
            'file' => 'Selfie dengan ID card harus berupa file yang valid.',
            'mimes' => 'Selfie dengan ID card harus berupa file dengan tipe: :values.',
            'max' => 'Selfie dengan ID card tidak boleh lebih besar dari :max kilobyte.',
        ],
        'id_card_meta' => [
            'required' => 'Metadata KTP wajib diisi.',
            'array' => 'Metadata KTP harus berupa array.',
            'name' => [
                'required' => 'Nama di KTP wajib diisi.',
            ],
            'number' => [
                'required' => 'Nomor KTP wajib diisi.',
            ],
        ],
        'selfie_with_id_card_meta' => [
            'array' => 'Metadata selfie dengan ID card harus berupa array.',
        ],
        'id_card_expiry_date' => [
            'date' => 'Tanggal kedaluwarsa KTP harus berupa tanggal yang valid.',
            'after' => 'Tanggal kedaluwarsa KTP harus setelah hari ini.',
        ],
    ],


    'status' => [
        'online_status' => [
            'required' => 'Status online wajib diisi.',
            'in' => 'Status online harus online atau offline.',
        ],
        'operational_status' => [
            'required' => 'Status operasional wajib diisi.',
            'in' => 'Status operasional harus available, on_order, atau rest.',
        ],
        'active_service' => [
            'required' => 'Layanan aktif wajib diisi.',
            'in' => 'Layanan aktif harus salah satu dari: food, ride, car, send, mart.',
        ],
        'latitude' => [
            'required' => 'Lintang wajib diisi.',
            'numeric' => 'Lintang harus berupa angka.',
            'between' => 'Lintang harus antara -90 sampai 90 derajat.',
        ],
        'longitude' => [
            'required' => 'Bujur wajib diisi.',
            'numeric' => 'Bujur harus berupa angka.',
            'between' => 'Bujur harus antara -180 sampai 180 derajat.',
        ],
    ],

    'attributes' => [
        'first_name' => 'nama depan',
        'last_name' => 'nama belakang',
        'avatar' => 'avatar',
        'gender' => 'jenis kelamin',
        'language' => 'bahasa',
        'type' => 'tipe',
        'brand' => 'merk',
        'model' => 'model',
        'year' => 'tahun',
        'color' => 'warna',
        'license_plate' => 'nomor polisi',
        'label' => 'label',
        'street' => 'jalan',
        'city' => 'kota',
        'province' => 'provinsi',
        'postal_code' => 'kode pos',
        'latitude' => 'lintang',
        'longitude' => 'bujur',
        'is_primary' => 'alamat utama',
        'file' => 'file',
        'expiry_date' => 'tanggal kedaluwarsa',
        'online_status' => 'status online',
        'operational_status' => 'status operasional',
        'active_service' => 'layanan aktif',
    ],
];
