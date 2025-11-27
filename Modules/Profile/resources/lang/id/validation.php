<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Bidang :attribute harus diterima.',
    'accepted_if' => 'Bidang :attribute harus diterima ketika :other adalah :value.',
    'active_url' => 'Bidang :attribute harus berupa URL yang valid.',
    'after' => 'Bidang :attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => 'Bidang :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Bidang :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Bidang :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Bidang :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Bidang :attribute harus berupa array.',
    'ascii' => 'Bidang :attribute hanya boleh berisi karakter alfanumerik dan simbol byte tunggal.',
    'before' => 'Bidang :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => 'Bidang :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Bidang :attribute harus memiliki antara :min dan :max item.',
        'file' => 'Bidang :attribute harus antara :min dan :max kilobyte.',
        'numeric' => 'Bidang :attribute harus antara :min dan :max.',
        'string' => 'Bidang :attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => 'Bidang :attribute harus berupa true atau false.',
    'can' => 'Bidang :attribute berisi nilai yang tidak sah.',
    'confirmed' => 'Konfirmasi bidang :attribute tidak cocok.',
    'current_password' => 'Kata sandi salah.',
    'date' => 'Bidang :attribute harus berupa tanggal yang valid.',
    'date_equals' => 'Bidang :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => 'Bidang :attribute harus sesuai dengan format :format.',
    'decimal' => 'Bidang :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'Bidang :attribute harus ditolak.',
    'declined_if' => 'Bidang :attribute harus ditolak ketika :other adalah :value.',
    'different' => 'Bidang :attribute dan :other harus berbeda.',
    'digits' => 'Bidang :attribute harus :digits digit.',
    'digits_between' => 'Bidang :attribute harus antara :min dan :max digit.',
    'dimensions' => 'Bidang :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'Bidang :attribute memiliki nilai duplikat.',
    'doesnt_end_with' => 'Bidang :attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with' => 'Bidang :attribute tidak boleh dimulai dengan salah satu dari berikut: :values.',
    'email' => 'Bidang :attribute harus berupa alamat email yang valid.',
    'ends_with' => 'Bidang :attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => 'Bidang :attribute yang dipilih tidak valid.',
    'exists' => 'Bidang :attribute yang dipilih tidak valid.',
    'file' => 'Bidang :attribute harus berupa file.',
    'filled' => 'Bidang :attribute harus memiliki nilai.',
    'gt' => [
        'array' => 'Bidang :attribute harus memiliki lebih dari :value item.',
        'file' => 'Bidang :attribute harus lebih besar dari :value kilobyte.',
        'numeric' => 'Bidang :attribute harus lebih besar dari :value.',
        'string' => 'Bidang :attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Bidang :attribute harus memiliki :value item atau lebih.',
        'file' => 'Bidang :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'numeric' => 'Bidang :attribute harus lebih besar dari atau sama dengan :value.',
        'string' => 'Bidang :attribute harus lebih besar dari atau sama dengan :value karakter.',
    ],
    'image' => 'Bidang :attribute harus berupa gambar.',
    'in' => 'Bidang :attribute yang dipilih tidak valid.',
    'in_array' => 'Bidang :attribute harus ada di :other.',
    'integer' => 'Bidang :attribute harus berupa bilangan bulat.',
    'ip' => 'Bidang :attribute harus berupa alamat IP yang valid.',
    'ipv4' => 'Bidang :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => 'Bidang :attribute harus berupa alamat IPv6 yang valid.',
    'json' => 'Bidang :attribute harus berupa string JSON yang valid.',
    'lowercase' => 'Bidang :attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => 'Bidang :attribute harus memiliki kurang dari :value item.',
        'file' => 'Bidang :attribute harus kurang dari :value kilobyte.',
        'numeric' => 'Bidang :attribute harus kurang dari :value.',
        'string' => 'Bidang :attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Bidang :attribute tidak boleh memiliki lebih dari :value item.',
        'file' => 'Bidang :attribute harus kurang dari atau sama dengan :value kilobyte.',
        'numeric' => 'Bidang :attribute harus kurang dari atau sama dengan :value.',
        'string' => 'Bidang :attribute harus kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Bidang :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Bidang :attribute tidak boleh memiliki lebih dari :max item.',
        'file' => 'Bidang :attribute tidak boleh lebih besar dari :max kilobyte.',
        'numeric' => 'Bidang :attribute tidak boleh lebih besar dari :max.',
        'string' => 'Bidang :attribute tidak boleh lebih besar dari :max karakter.',
    ],
    'max_digits' => 'Bidang :attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => 'Bidang :attribute harus berupa file dengan tipe: :values.',
    'mimetypes' => 'Bidang :attribute harus berupa file dengan tipe: :values.',
    'min' => [
        'array' => 'Bidang :attribute harus memiliki setidaknya :min item.',
        'file' => 'Bidang :attribute harus setidaknya :min kilobyte.',
        'numeric' => 'Bidang :attribute harus setidaknya :min.',
        'string' => 'Bidang :attribute harus setidaknya :min karakter.',
    ],
    'min_digits' => 'Bidang :attribute harus memiliki setidaknya :min digit.',
    'missing' => 'Bidang :attribute harus hilang.',
    'missing_if' => 'Bidang :attribute harus hilang ketika :other adalah :value.',
    'missing_unless' => 'Bidang :attribute harus hilang kecuali :other adalah :value.',
    'missing_with' => 'Bidang :attribute harus hilang ketika :values ada.',
    'missing_with_all' => 'Bidang :attribute harus hilang ketika :values ada.',
    'multiple_of' => 'Bidang :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Bidang :attribute yang dipilih tidak valid.',
    'not_regex' => 'Format bidang :attribute tidak valid.',
    'numeric' => 'Bidang :attribute harus berupa angka.',
    'password' => 'Kata sandi salah.',
    'present' => 'Bidang :attribute harus ada.',
    'prohibited' => 'Bidang :attribute dilarang.',
    'prohibited_if' => 'Bidang :attribute dilarang ketika :other adalah :value.',
    'prohibited_unless' => 'Bidang :attribute dilarang kecuali :other ada di :values.',
    'prohibits' => 'Bidang :attribute melarang :other untuk ada.',
    'regex' => 'Format bidang :attribute tidak valid.',
    'required' => 'Bidang :attribute wajib diisi.',
    'required_array_keys' => 'Bidang :attribute harus berisi entri untuk: :values.',
    'required_if' => 'Bidang :attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => 'Bidang :attribute wajib diisi ketika :other diterima.',
    'required_unless' => 'Bidang :attribute wajib diisi kecuali :other ada di :values.',
    'required_with' => 'Bidang :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Bidang :attribute wajib diisi ketika :values ada.',
    'required_without' => 'Bidang :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Bidang :attribute wajib diisi ketika tidak ada :values yang ada.',
    'same' => 'Bidang :attribute harus cocok dengan :other.',
    'size' => [
        'array' => 'Bidang :attribute harus berisi :size item.',
        'file' => 'Bidang :attribute harus :size kilobyte.',
        'numeric' => 'Bidang :attribute harus :size.',
        'string' => 'Bidang :attribute harus :size karakter.',
    ],
    'starts_with' => 'Bidang :attribute harus dimulai dengan salah satu dari berikut: :values.',
    'string' => 'Bidang :attribute harus berupa string.',
    'timezone' => 'Bidang :attribute harus berupa zona waktu yang valid.',
    'unique' => 'Bidang :attribute sudah digunakan.',
    'uploaded' => 'Bidang :attribute gagal diupload.',
    'uppercase' => 'Bidang :attribute harus berupa huruf besar.',
    'url' => 'Bidang :attribute harus berupa URL yang valid.',
    'ulid' => 'Bidang :attribute harus berupa ULID yang valid.',
    'uuid' => 'Bidang :attribute harus berupa UUID yang valid.',

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
        'file' => 'file',
        'expiry_date' => 'tanggal kedaluwarsa',
        'street' => 'jalan',
        'city' => 'kota',
        'province' => 'provinsi',
        'postal_code' => 'kode pos',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'is_primary' => 'alamat utama',
        'business_name' => 'nama bisnis',
        'business_type' => 'tipe bisnis',
        'business_phone' => 'telepon bisnis',
        'tax_id' => 'ID pajak',
        'business_license_number' => 'nomor lisensi bisnis',
        'bank_account_number' => 'nomor rekening bank',
        'preferences' => 'preferensi',
        'loyalty_points' => 'poin loyalitas',
        'membership_level' => 'tingkat keanggotaan',
        'brand' => 'merk',
        'model' => 'model',
        'year' => 'tahun',
        'color' => 'warna',
        'license_plate' => 'plat nomor',
        'sim_number' => 'nomor SIM',
        'sim_expiry_date' => 'tanggal kedaluwarsa SIM',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Error Messages for Exceptions
    |--------------------------------------------------------------------------
    |
    | These are used by exceptions in the Profile module
    |
    */

    'profile_not_found' => 'Profil tidak ditemukan.',
    'profile_already_exists' => 'Profil sudah ada untuk pengguna ini.',
    'address_not_found' => 'Alamat tidak ditemukan.',
    'invalid_address_type' => 'Tipe alamat tidak valid.',
    'invalid_coordinates' => 'Koordinat latitude atau longitude tidak valid.',
    'primary_address_required' => 'Setidaknya satu alamat utama diperlukan.',
    'bank_not_found' => 'Bank tidak ditemukan.',
    'bank_code_exists' => 'Kode bank sudah ada.',
    'invalid_bank_code' => 'Kode bank harus alfanumerik dan huruf besar.',
    'driver_profile_not_found' => 'Profil driver tidak ditemukan.',
    'vehicle_not_found' => 'Kendaraan tidak ditemukan.',
    'document_not_found' => 'Dokumen tidak ditemukan.',
    'invalid_vehicle_type' => 'Tipe kendaraan tidak valid.',
    'invalid_document_type' => 'Tipe dokumen tidak valid.',
    'missing_required_documents' => 'Dokumen yang diperlukan untuk verifikasi belum lengkap.',
    'no_vehicle_registered' => 'Setidaknya satu kendaraan harus terdaftar.',
    'already_verified' => 'Profil sudah diverifikasi.',
    'verification_pending' => 'Verifikasi masih dalam proses.',
    'merchant_profile_not_found' => 'Profil merchant tidak ditemukan.',
    'invalid_business_type' => 'Tipe bisnis tidak valid.',
    'customer_profile_not_found' => 'Profil customer tidak ditemukan.',
    'file_upload_failed' => 'Upload file gagal.',
    'invalid_avatar_file' => 'File avatar tidak valid. Hanya file JPEG, PNG, JPG di bawah 5MB yang diperbolehkan.',
    'avatar_upload_failed' => 'Upload avatar gagal.',
    'invalid_document_file' => 'File dokumen tidak valid untuk tipe: :type.',
    'document_upload_failed' => 'Upload dokumen gagal untuk tipe: :type.',
    'error_occurred' => 'Terjadi kesalahan saat memproses permintaan Anda.',
    'type_required' => 'Tipe diperlukan.',
    'expiry_date_must_be_future' => 'Tanggal kedaluwarsa harus di masa depan.',
    'update_failed' => 'Pembaruan gagal.',
    'failed' => 'Operasi gagal.',
    'invalid_gender' => 'Jenis kelamin tidak valid.',
    'invalid_verification_status' => 'Status verifikasi tidak valid.',
    'file_required' => 'File diperlukan.',
    'invalid_file_type' => 'Tipe file tidak valid.',
    'file_too_large' => 'Ukuran file melebihi batas maksimum.',
    'invalid_token' => 'Token autentikasi tidak valid atau tidak ada.',
    'access_denied' => 'Akses ditolak. Izin tidak mencukupi.',
    'verification_requested' => 'Permintaan verifikasi berhasil diajukan.',
    'verification_status_retrieved' => 'Status verifikasi berhasil diambil.',
    'documents_submitted' => 'Dokumen berhasil dikirim.',
    'documents_retrieved' => 'Dokumen berhasil diambil.',
    'vehicles_retrieved' => 'Kendaraan berhasil diambil.',
    'vehicle_registered' => 'Kendaraan berhasil didaftarkan.',
    'vehicle_updated' => 'Kendaraan berhasil diperbarui.',
    'vehicle_removed' => 'Kendaraan berhasil dihapus.',
    'all_documents_required' => 'Semua dokumen yang diperlukan harus dikirim: :types.',
    'document_verification_pending' => 'Verifikasi dokumen masih dalam proses.',
    'driver_not_verified' => 'Profil driver belum diverifikasi.',

    // Merchant validation messages
    'id_card_file_required' => 'File KTP diperlukan.',
    'id_card_file_invalid_format' => 'File KTP harus berupa JPEG, PNG, JPG, atau PDF.',
    'id_card_number_required' => 'Nomor KTP diperlukan.',
    'id_card_number_invalid' => 'Nomor KTP harus 16 digit.',
    'id_card_expiry_required' => 'Tanggal kedaluwarsa KTP diperlukan.',
    'store_file_required' => 'File lisensi toko/bisnis diperlukan.',
    'store_file_invalid_format' => 'File toko harus berupa JPEG, PNG, JPG, atau PDF.',
    'license_number_required' => 'Nomor lisensi diperlukan.',
    'license_expiry_required' => 'Tanggal kedaluwarsa lisensi diperlukan.',
    'bank_id_required' => 'Pemilihan bank diperlukan.',
    'bank_id_invalid' => 'Bank yang dipilih tidak valid.',
    'bank_not_found' => 'Bank yang dipilih tidak ditemukan.',
    'account_number_required' => 'Nomor rekening diperlukan.',
    'account_number_invalid' => 'Nomor rekening hanya boleh berisi angka.',
    'account_number_too_short' => 'Nomor rekening minimal 10 digit.',
    'account_number_too_long' => 'Nomor rekening maksimal 20 digit.',
    'is_primary_invalid' => 'Flag rekening utama harus true atau false.',
    'street_required' => 'Alamat jalan diperlukan.',
    'street_too_long' => 'Alamat jalan maksimal 255 karakter.',
    'city_required' => 'Kota diperlukan.',
    'city_too_long' => 'Kota maksimal 100 karakter.',
    'province_required' => 'Provinsi diperlukan.',
    'province_too_long' => 'Provinsi maksimal 100 karakter.',
    'postal_code_required' => 'Kode pos diperlukan.',
    'postal_code_invalid' => 'Kode pos harus 5 digit.',
    'latitude_required' => 'Latitude diperlukan.',
    'latitude_invalid' => 'Latitude harus berupa angka yang valid.',
    'latitude_out_of_range' => 'Latitude harus antara -90 dan 90.',
    'longitude_required' => 'Longitude diperlukan.',
    'longitude_invalid' => 'Longitude harus berupa angka yang valid.',
    'longitude_out_of_range' => 'Longitude harus antara -180 dan 180.',
];