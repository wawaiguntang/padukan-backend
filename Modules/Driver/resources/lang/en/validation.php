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

        'accepted' => 'The :attribute must be accepted.',
        'active_url' => 'The :attribute is not a valid URL.',
        'after' => 'The :attribute must be a date after :date.',
        'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
        'alpha' => 'The :attribute must only contain letters.',
        'alpha_dash' => 'The :attribute must only contain letters, numbers, dashes and underscores.',
        'alpha_num' => 'The :attribute must only contain letters and numbers.',
        'array' => 'The :attribute must be an array.',
        'before' => 'The :attribute must be a date before :date.',
        'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
        'between' => [
            'numeric' => 'The :attribute must be between :min and :max.',
            'file' => 'The :attribute must be between :min and :max kilobytes.',
            'string' => 'The :attribute must be between :min and :max characters.',
            'array' => 'The :attribute must have between :min and :max items.',
        ],
        'boolean' => 'The :attribute field must be true or false.',
        'confirmed' => 'The :attribute confirmation does not match.',
        'date' => 'The :attribute is not a valid date.',
        'date_equals' => 'The :attribute must be a date equal to :date.',
        'date_format' => 'The :attribute does not match the format :format.',
        'different' => 'The :attribute and :other must be different.',
        'digits' => 'The :attribute must be :digits digits.',
        'digits_between' => 'The :attribute must be between :min and :max digits.',
        'dimensions' => 'The :attribute has invalid image dimensions.',
        'distinct' => 'The :attribute field has a duplicate value.',
        'email' => 'The :attribute must be a valid email address.',
        'ends_with' => 'The :attribute must end with one of the following: :values.',
        'exists' => 'The selected :attribute is invalid.',
        'file' => 'The :attribute must be a file.',
        'filled' => 'The :attribute field must have a value.',
        'gt' => [
            'numeric' => 'The :attribute must be greater than :value.',
            'file' => 'The :attribute must be greater than :value kilobytes.',
            'string' => 'The :attribute must be greater than :value characters.',
            'array' => 'The :attribute must have more than :value items.',
        ],
        'gte' => [
            'numeric' => 'The :attribute must be greater than or equal :value.',
            'file' => 'The :attribute must be greater than or equal :value kilobytes.',
            'string' => 'The :attribute must be greater than or equal :value characters.',
            'array' => 'The :attribute must have :value items or more.',
        ],
        'image' => 'The :attribute must be an image.',
        'in' => 'The selected :attribute is invalid.',
        'in_array' => 'The :attribute field does not exist in :other.',
        'integer' => 'The :attribute must be an integer.',
        'ip' => 'The :attribute must be a valid IP address.',
        'ipv4' => 'The :attribute must be a valid IPv4 address.',
        'ipv6' => 'The :attribute must be a valid IPv6 address.',
        'json' => 'The :attribute must be a valid JSON string.',
        'lt' => [
            'numeric' => 'The :attribute must be less than :value.',
            'file' => 'The :attribute must be less than :value kilobytes.',
            'string' => 'The :attribute must be less than :value characters.',
            'array' => 'The :attribute must have less than :value items.',
        ],
        'lte' => [
            'numeric' => 'The :attribute must be less than or equal :value.',
            'file' => 'The :attribute must be less than or equal :value kilobytes.',
            'string' => 'The :attribute must be less than or equal :value characters.',
            'array' => 'The :attribute must have :value items or less.',
        ],
        'max' => [
            'numeric' => 'The :attribute may not be greater than :max.',
            'file' => 'The :attribute may not be greater than :max kilobytes.',
            'string' => 'The :attribute may not be greater than :max characters.',
            'array' => 'The :attribute may not have more than :max items.',
        ],
        'mimes' => 'The :attribute must be a file of type: :values.',
        'mimetypes' => 'The :attribute must be a file of type: :values.',
        'min' => [
            'numeric' => 'The :attribute must be at least :min.',
            'file' => 'The :attribute must be at least :min kilobytes.',
            'string' => 'The :attribute must be at least :min characters.',
            'array' => 'The :attribute must have at least :min items.',
        ],
        'not_in' => 'The selected :attribute is invalid.',
        'not_regex' => 'The :attribute format is invalid.',
        'numeric' => 'The :attribute must be a number.',
        'password' => 'The password is incorrect.',
        'present' => 'The :attribute field must be present.',
        'regex' => 'The :attribute format is invalid.',
        'required' => 'The :attribute field is required.',
        'required_if' => 'The :attribute field is required when :other is :value.',
        'required_unless' => 'The :attribute field is required unless :other is in :values.',
        'required_with' => 'The :attribute field is required when :values is present.',
        'required_with_all' => 'The :attribute field is required when :values are present.',
        'required_without' => 'The :attribute field is required when :values is not present.',
        'required_without_all' => 'The :attribute field is required when none of :values are present.',
        'same' => 'The :attribute and :other must match.',
        'size' => [
            'numeric' => 'The :attribute must be :size.',
            'file' => 'The :attribute must be :size kilobytes.',
            'string' => 'The :attribute must be :size characters.',
            'array' => 'The :attribute must contain :size items.',
        ],
        'starts_with' => 'The :attribute must start with one of the following: :values.',
        'string' => 'The :attribute must be a string.',
        'timezone' => 'The :attribute must be a valid timezone.',
        'unique' => 'The :attribute has already been taken.',
        'uploaded' => 'The :attribute failed to upload.',
        'url' => 'The :attribute must be a valid URL.',
        'uuid' => 'The :attribute must be a valid UUID.',

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

        'failed' => 'The given data was invalid.',

        'first_name' => [
            'string' => 'First name must be a string.',
            'max' => 'First name may not be greater than :max characters.',
        ],

        'last_name' => [
            'string' => 'Last name must be a string.',
            'max' => 'Last name may not be greater than :max characters.',
        ],

        'avatar' => [
            'string' => 'Avatar must be a string.',
            'max' => 'Avatar path may not be greater than :max characters.',
        ],

        'gender' => [
            'in' => 'Selected gender is invalid.',
        ],

        'language' => [
            'string' => 'Language must be a string.',
            'max' => 'Language may not be greater than :max characters.',
        ],

        'file' => [
            'required' => 'File is required.',
            'file' => 'Must be a valid file.',
            'max' => 'File size may not be greater than :max kilobytes.',
        ],

        'type' => [
            'required' => 'Type is required.',
            'in' => 'Selected type is invalid.',
        ],

        'expiry_date' => [
            'date' => 'Expiry date must be a valid date.',
            'after' => 'Expiry date must be after today.',
        ],

        'profile_verification' => [
            'id_card_temp_path' => [
                'required' => 'ID card temporary path is required.',
                'string' => 'ID card temporary path must be a string.',
            ],
            'selfie_with_id_card_temp_path' => [
                'required' => 'Selfie with ID card temporary path is required.',
                'string' => 'Selfie with KTP temporary path must be a string.',
            ],
            'id_card_file' => [
                'required' => 'ID card file is required.',
                'file' => 'ID card must be a valid file.',
                'mimes' => 'ID card must be a file of type: :values.',
                'max' => 'ID card may not be greater than :max kilobytes.',
            ],
            'selfie_with_id_card_file' => [
                'required' => 'Selfie with ID card file is required.',
                'file' => 'Selfie with KTP must be a valid file.',
                'mimes' => 'Selfie with KTP must be a file of type: :values.',
                'max' => 'Selfie with KTP may not be greater than :max kilobytes.',
            ],
            'id_card_meta' => [
                'required' => 'ID card metadata is required.',
                'array' => 'ID card metadata must be an array.',
                'name' => [
                    'required' => 'ID card name is required.',
                ],
                'number' => [
                    'required' => 'ID card number is required.',
                ],
            ],
            'selfie_with_id_card_meta' => [
                'array' => 'Selfie with ID card metadata must be an array.',
            ],
            'id_card_expiry_date' => [
                'date' => 'ID card expiry date must be a valid date.',
                'after' => 'ID card expiry date must be after today.',
            ],
        ],

        'vehicle' => [
            'type' => [
                'required' => 'Vehicle type is required.',
                'in' => 'Selected vehicle type is invalid.',
            ],
            'brand' => [
                'required' => 'Vehicle brand is required.',
                'max' => 'Vehicle brand may not be greater than :max characters.',
            ],
            'model' => [
                'required' => 'Vehicle model is required.',
                'max' => 'Vehicle model may not be greater than :max characters.',
            ],
            'year' => [
                'required' => 'Vehicle year is required.',
                'integer' => 'Vehicle year must be an integer.',
                'min' => 'Vehicle year must be at least :min.',
                'max' => 'Vehicle year may not be greater than :max.',
            ],
            'color' => [
                'required' => 'Vehicle color is required.',
                'max' => 'Vehicle color may not be greater than :max characters.',
            ],
            'license_plate' => [
                'required' => 'License plate is required.',
                'max' => 'License plate may not be greater than :max characters.',
                'unique' => 'This license plate is already registered.',
            ],
        ],


        'status' => [
            'online_status' => [
                'required' => 'Online status is required.',
                'in' => 'Online status must be either online or offline.',
            ],
            'operational_status' => [
                'required' => 'Operational status is required.',
                'in' => 'Operational status must be available, on_order, or rest.',
            ],
            'active_service' => [
                'required' => 'Active service is required.',
                'in' => 'Active service must be one of: food, ride, car, send, mart.',
            ],
            'latitude' => [
                'required' => 'Latitude is required.',
                'numeric' => 'Latitude must be a number.',
                'between' => 'Latitude must be between -90 and 90 degrees.',
            ],
            'longitude' => [
                'required' => 'Longitude is required.',
                'numeric' => 'Longitude must be a number.',
                'between' => 'Longitude must be between -180 and 180 degrees.',
            ],
        ],

        'attributes' => [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'avatar' => 'avatar',
            'gender' => 'gender',
            'language' => 'language',
            'type' => 'type',
            'brand' => 'brand',
            'model' => 'model',
            'year' => 'year',
            'color' => 'color',
            'license_plate' => 'license plate',
            'label' => 'label',
            'street' => 'street',
            'city' => 'city',
            'province' => 'province',
            'postal_code' => 'postal code',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'is_primary' => 'primary address',
            'file' => 'file',
            'expiry_date' => 'expiry date',
            'online_status' => 'online status',
            'operational_status' => 'operational status',
            'active_service' => 'active service',
        ],
    ];
