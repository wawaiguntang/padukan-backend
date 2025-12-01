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

    'accepted' => 'Terima :attribute dulu.',
    'accepted_if' => 'Terima :attribute jika :other :value.',
    'active_url' => ':attribute harus URL valid.',
    'after' => ':attribute harus setelah :date.',
    'after_or_equal' => ':attribute harus setelah atau sama :date.',
    'alpha' => ':attribute hanya huruf.',
    'alpha_dash' => ':attribute hanya huruf, angka, strip, underscore.',
    'alpha_num' => ':attribute hanya huruf dan angka.',
    'array' => ':attribute harus array.',
    'ascii' => ':attribute hanya alfanumerik dan simbol.',
    'before' => ':attribute harus sebelum :date.',
    'before_or_equal' => ':attribute harus sebelum atau sama :date.',
    'between' => [
        'array' => ':attribute harus :min sampai :max item.',
        'file' => ':attribute harus :min sampai :max KB.',
        'numeric' => ':attribute harus :min sampai :max.',
        'string' => ':attribute harus :min sampai :max karakter.',
    ],
    'boolean' => ':attribute harus true atau false.',
    'can' => ':attribute nilai tidak sah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':attribute harus tanggal valid.',
    'date_equals' => ':attribute harus sama :date.',
    'date_format' => ':attribute harus format :format.',
    'decimal' => ':attribute harus :decimal desimal.',
    'declined' => 'Tolak :attribute.',
    'declined_if' => 'Tolak :attribute jika :other :value.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus :digits digit.',
    'digits_between' => ':attribute harus :min sampai :max digit.',
    'dimensions' => ':attribute dimensi gambar tidak valid.',
    'distinct' => ':attribute nilai duplikat.',
    'doesnt_end_with' => ':attribute jangan diakhiri :values.',
    'doesnt_start_with' => ':attribute jangan dimulai :values.',
    'email' => ':attribute harus email valid.',
    'ends_with' => ':attribute harus diakhiri :values.',
    'enum' => ':attribute pilihan tidak valid.',
    'exists' => ':attribute pilihan tidak valid.',
    'file' => ':attribute harus file.',
    'filled' => 'Isi :attribute.',
    'gt' => [
        'array' => ':attribute harus lebih dari :value item.',
        'file' => ':attribute harus lebih dari :value KB.',
        'numeric' => ':attribute harus lebih dari :value.',
        'string' => ':attribute harus lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus :value item atau lebih.',
        'file' => ':attribute harus :value KB atau lebih.',
        'numeric' => ':attribute harus :value atau lebih.',
        'string' => ':attribute harus :value karakter atau lebih.',
    ],
    'image' => ':attribute harus gambar.',
    'in' => ':attribute pilihan tidak valid.',
    'in_array' => ':attribute harus di :other.',
    'integer' => ':attribute harus bilangan bulat.',
    'ip' => ':attribute harus IP valid.',
    'ipv4' => ':attribute harus IPv4 valid.',
    'ipv6' => ':attribute harus IPv6 valid.',
    'json' => ':attribute harus JSON valid.',
    'lowercase' => ':attribute harus huruf kecil.',
    'lt' => [
        'array' => ':attribute harus kurang dari :value item.',
        'file' => ':attribute harus kurang dari :value KB.',
        'numeric' => ':attribute harus kurang dari :value.',
        'string' => ':attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute maksimal :value item.',
        'file' => ':attribute maksimal :value KB.',
        'numeric' => ':attribute maksimal :value.',
        'string' => ':attribute maksimal :value karakter.',
    ],
    'mac_address' => ':attribute harus MAC address valid.',
    'max' => [
        'array' => ':attribute maksimal :max item.',
        'file' => ':attribute maksimal :max KB.',
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'max_digits' => ':attribute maksimal :max digit.',
    'mimes' => ':attribute harus tipe :values.',
    'mimetypes' => ':attribute harus tipe :values.',
    'min' => [
        'array' => ':attribute minimal :min item.',
        'file' => ':attribute minimal :min KB.',
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'min_digits' => ':attribute minimal :min digit.',
    'missing' => ':attribute harus hilang.',
    'missing_if' => ':attribute harus hilang jika :other :value.',
    'missing_unless' => ':attribute harus hilang kecuali :other :value.',
    'missing_with' => ':attribute harus hilang jika :values ada.',
    'missing_with_all' => ':attribute harus hilang jika :values ada.',
    'multiple_of' => ':attribute harus kelipatan :value.',
    'not_in' => ':attribute pilihan tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus angka.',
    'password' => 'Kata sandi salah.',
    'present' => ':attribute harus ada.',
    'prohibited' => ':attribute dilarang.',
    'prohibited_if' => ':attribute dilarang jika :other :value.',
    'prohibited_unless' => ':attribute dilarang kecuali :other di :values.',
    'prohibits' => ':attribute melarang :other.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => 'Isi :attribute ya.',
    'required_array_keys' => ':attribute harus berisi :values.',
    'required_if' => 'Isi :attribute jika :other :value.',
    'required_if_accepted' => 'Isi :attribute jika :other diterima.',
    'required_unless' => 'Isi :attribute kecuali :other di :values.',
    'required_with' => 'Isi :attribute jika :values ada.',
    'required_with_all' => 'Isi :attribute jika :values ada.',
    'required_without' => 'Isi :attribute jika :values tidak ada.',
    'required_without_all' => 'Isi :attribute jika tidak ada :values.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus :size item.',
        'file' => ':attribute harus :size KB.',
        'numeric' => ':attribute harus :size.',
        'string' => ':attribute harus :size karakter.',
    ],
    'starts_with' => ':attribute harus dimulai :values.',
    'string' => ':attribute harus string.',
    'timezone' => ':attribute harus zona waktu valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal upload.',
    'uppercase' => ':attribute harus huruf besar.',
    'url' => ':attribute harus URL valid.',
    'ulid' => ':attribute harus ULID valid.',
    'uuid' => ':attribute harus UUID valid.',

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
        'phone' => 'nomor telepon',
        'email' => 'alamat email',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'identifier' => 'email atau nomor telepon',
        'user_id' => 'ID pengguna',
        'token' => 'token verifikasi',
        'type' => 'tipe verifikasi',
        'refresh_token' => 'token refresh',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages
    |--------------------------------------------------------------------------
    */

    'identifier' => [
        'required' => 'Email atau nomor telepon wajib diisi.',
    ],
];