 <?php

    return [
        /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class for the driver module.
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
        'confirmed' => 'Konfirmasi :attribute tidak cocok.',
        'date' => ':attribute bukan tanggal yang valid.',
        'date_equals' => ':attribute harus tanggal yang sama dengan :date.',
        'date_format' => ':attribute tidak sesuai format :format.',
        'different' => ':attribute dan :other harus berbeda.',
        'digits' => ':attribute harus :digits digit.',
        'digits_between' => ':attribute harus antara :min dan :max digit.',
        'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
        'distinct' => ':attribute memiliki nilai duplikat.',
        'email' => ':attribute harus alamat email yang valid.',
        'ends_with' => ':attribute harus diakhiri dengan salah satu dari: :values.',
        'exists' => ':attribute yang dipilih tidak valid.',
        'file' => ':attribute harus berupa file.',
        'filled' => ':attribute harus memiliki nilai.',
        'gt' => [
            'numeric' => ':attribute harus lebih besar dari :value.',
            'file' => ':attribute harus lebih besar dari :value kilobyte.',
            'string' => ':attribute harus lebih dari :value karakter.',
            'array' => ':attribute harus memiliki lebih dari :value item.',
        ],
        'gte' => [
            'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
            'file' => ':attribute harus lebih besar atau sama dengan :value kilobyte.',
            'string' => ':attribute harus lebih dari atau sama dengan :value karakter.',
            'array' => ':attribute harus memiliki :value item atau lebih.',
        ],
        'image' => ':attribute harus berupa gambar.',
        'in' => ':attribute yang dipilih tidak valid.',
        'in_array' => ':attribute tidak ada di :other.',
        'integer' => ':attribute harus berupa bilangan bulat.',
        'ip' => ':attribute harus alamat IP yang valid.',
        'ipv4' => ':attribute harus alamat IPv4 yang valid.',
        'ipv6' => ':attribute harus alamat IPv6 yang valid.',
        'json' => ':attribute harus string JSON yang valid.',
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
            'numeric' => ':attribute tidak boleh lebih dari :max.',
            'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
            'string' => ':attribute tidak boleh lebih dari :max karakter.',
            'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
        ],
        'mimes' => ':attribute harus berupa file dengan tipe: :values.',
        'mimetypes' => ':attribute harus berupa file dengan tipe: :values.',
        'min' => [
            'numeric' => ':attribute minimal harus :min.',
            'file' => ':attribute minimal harus :min kilobyte.',
            'string' => ':attribute minimal harus :min karakter.',
            'array' => ':attribute minimal harus memiliki :min item.',
        ],
        'not_in' => ':attribute yang dipilih tidak valid.',
        'not_regex' => 'Format :attribute tidak valid.',
        'numeric' => ':attribute harus berupa angka.',
        'password' => 'Password salah.',
        'present' => ':attribute harus ada.',
        'regex' => 'Format :attribute tidak valid.',
        'required' => ':attribute wajib diisi.',
        'required_if' => ':attribute wajib diisi ketika :other adalah :value.',
        'required_unless' => ':attribute wajib diisi kecuali :other ada di :values.',
        'required_with' => ':attribute wajib diisi ketika :values ada.',
        'required_with_all' => ':attribute wajib diisi ketika :values ada.',
        'required_without' => ':attribute wajib diisi ketika :values tidak ada.',
        'required_without_all' => ':attribute wajib diisi ketika tidak ada :values.',
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
        'unique' => ':attribute sudah digunakan.',
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

        'failed' => 'Data yang diberikan tidak valid.',

        'first_name' => [
            'string' => 'Nama depan harus berupa string.',
            'max' => 'Nama depan tidak boleh lebih dari :max karakter.',
        ],

        'last_name' => [
            'string' => 'Nama belakang harus berupa string.',
            'max' => 'Nama belakang tidak boleh lebih dari :max karakter.',
        ],

        'avatar' => [
            'string' => 'Avatar harus berupa string.',
            'max' => 'Path avatar tidak boleh lebih dari :max karakter.',
            'mimes' => 'Avatar harus berupa file dengan tipe: :values.',
            'file' => 'Avatar harus berupa file yang valid.',
        ],

        'gender' => [
            'in' => 'Jenis kelamin yang dipilih tidak valid.',
        ],

        'language' => [
            'string' => 'Bahasa harus berupa string.',
            'max' => 'Bahasa tidak boleh lebih dari :max karakter.',
        ],

        'file' => [
            'required' => 'File wajib diisi.',
            'file' => 'Harus berupa file yang valid.',
            'max' => 'Ukuran file tidak boleh lebih dari :max kilobyte.',
        ],

        'type' => [
            'required' => 'Tipe wajib diisi.',
            'in' => 'Tipe yang dipilih tidak valid.',
        ],

        'expiry_date' => [
            'date' => 'Tanggal kedaluwarsa harus tanggal yang valid.',
            'after' => 'Tanggal kedaluwarsa harus setelah hari ini.',
        ],

        'profile_verification' => [
            'id_card_temp_path' => [
                'required' => 'Path sementara KTP wajib diisi.',
                'string' => 'Path sementara KTP harus berupa string.',
            ],
            'selfie_with_id_card_temp_path' => [
                'required' => 'Path sementara selfie dengan KTP wajib diisi.',
                'string' => 'Path sementara selfie dengan KTP harus berupa string.',
            ],
            'id_card_file' => [
                'required' => 'File KTP wajib diisi.',
                'file' => 'KTP harus berupa file yang valid.',
                'mimes' => 'KTP harus berupa file dengan tipe: :values.',
                'max' => 'KTP tidak boleh lebih dari :max kilobyte.',
            ],
            'selfie_with_id_card_file' => [
                'required' => 'File selfie dengan KTP wajib diisi.',
                'file' => 'Selfie dengan KTP harus berupa file yang valid.',
                'mimes' => 'Selfie dengan KTP harus berupa file dengan tipe: :values.',
                'max' => 'Selfie dengan KTP tidak boleh lebih dari :max kilobyte.',
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
            'selfie_with_id_card_meta' => [
                'array' => 'Metadata selfie dengan KTP harus berupa array.',
            ],
            'id_card_expiry_date' => [
                'date' => 'Tanggal kedaluwarsa KTP harus tanggal yang valid.',
                'after' => 'Tanggal kedaluwarsa KTP harus setelah hari ini.',
            ],
        ],

        'vehicle_verification' => [
            'vehicle_id' => [
                'required' => 'ID kendaraan wajib diisi.',
                'exists' => 'Kendaraan yang dipilih tidak ada.',
            ],
            'sim_file' => [
                'required' => 'File SIM wajib diisi.',
                'file' => 'SIM harus berupa file yang valid.',
                'mimes' => 'SIM harus berupa file dengan tipe: :values.',
                'max' => 'SIM tidak boleh lebih dari :max kilobyte.',
            ],
            'sim_meta' => [
                'required' => 'Metadata SIM wajib diisi.',
                'array' => 'Metadata SIM harus berupa array.',
                'number' => [
                    'required' => 'Nomor SIM wajib diisi.',
                ],
            ],
            'sim_expiry_date' => [
                'required' => 'Tanggal kedaluwarsa SIM wajib diisi.',
                'date' => 'Tanggal kedaluwarsa SIM harus tanggal yang valid.',
                'after' => 'Tanggal kedaluwarsa SIM harus setelah hari ini.',
            ],
            'stnk_file' => [
                'required' => 'File STNK wajib diisi.',
                'file' => 'STNK harus berupa file yang valid.',
                'mimes' => 'STNK harus berupa file dengan tipe: :values.',
                'max' => 'STNK tidak boleh lebih dari :max kilobyte.',
            ],
            'stnk_expiry_date' => [
                'required' => 'Tanggal kedaluwarsa STNK wajib diisi.',
                'date' => 'Tanggal kedaluwarsa STNK harus tanggal yang valid.',
                'after' => 'Tanggal kedaluwarsa STNK harus setelah hari ini.',
            ],
            'vehicle_photos' => [
                'required' => 'Foto kendaraan wajib diisi.',
                'array' => 'Foto kendaraan harus berupa array.',
                'min' => 'Minimal :min foto kendaraan diperlukan.',
                'max' => 'Maksimal :max foto kendaraan diperbolehkan.',
                'file' => 'Setiap foto kendaraan harus berupa file yang valid.',
                'mimes' => 'Setiap foto kendaraan harus berupa file dengan tipe: :values.',
                'max' => 'Setiap foto kendaraan tidak boleh lebih dari :max kilobyte.',
            ]
        ],

        'vehicle' => [
            'type' => [
                'required' => 'Tipe kendaraan wajib diisi.',
                'in' => 'Tipe kendaraan yang dipilih tidak valid.',
            ],
            'brand' => [
                'required' => 'Merek kendaraan wajib diisi.',
                'max' => 'Merek kendaraan tidak boleh lebih dari :max karakter.',
            ],
            'model' => [
                'required' => 'Model kendaraan wajib diisi.',
                'max' => 'Model kendaraan tidak boleh lebih dari :max karakter.',
            ],
            'year' => [
                'required' => 'Tahun kendaraan wajib diisi.',
                'integer' => 'Tahun kendaraan harus berupa bilangan bulat.',
                'min' => 'Tahun kendaraan minimal harus :min.',
                'max' => 'Tahun kendaraan tidak boleh lebih dari :max.',
            ],
            'color' => [
                'required' => 'Warna kendaraan wajib diisi.',
                'max' => 'Warna kendaraan tidak boleh lebih dari :max karakter.',
            ],
            'license_plate' => [
                'required' => 'Plat nomor wajib diisi.',
                'max' => 'Plat nomor tidak boleh lebih dari :max karakter.',
                'unique' => 'Plat nomor ini sudah terdaftar.',
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
            'active_services' => [
                'required' => 'Layanan aktif wajib diisi.',
                'array' => 'Layanan aktif harus berupa array.',
                'min' => 'Minimal satu layanan aktif harus dipilih.',
                'max' => 'Maksimal :max layanan aktif diperbolehkan.',
            ],
            'active_service' => [
                'required' => 'Layanan aktif wajib diisi.',
                'in' => 'Layanan aktif harus salah satu dari: food, ride, car, send, mart.',
            ],
            'latitude' => [
                'required' => 'Latitude wajib diisi.',
                'numeric' => 'Latitude harus berupa angka.',
                'between' => 'Latitude harus antara -90 dan 90 derajat.',
            ],
            'longitude' => [
                'required' => 'Longitude wajib diisi.',
                'numeric' => 'Longitude harus berupa angka.',
                'between' => 'Longitude harus antara -180 dan 180 derajat.',
            ],
            'vehicle_id' => [
                'required' => 'ID kendaraan wajib diisi.',
                'uuid' => 'ID kendaraan harus berupa UUID yang valid.',
                'exists' => 'Kendaraan yang dipilih tidak ada.',
            ]
        ],
    ];
