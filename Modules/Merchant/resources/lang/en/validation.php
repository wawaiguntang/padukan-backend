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

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
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

    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'avatar' => 'avatar',
        'gender' => 'gender',
        'language' => 'language',
        'type' => 'type',
        'label' => 'label',
        'street' => 'street',
        'city' => 'city',
        'state' => 'state',
        'country' => 'country',
        'postal_code' => 'postal code',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'is_primary' => 'primary address',
        'file' => 'file',
        'expiry_date' => 'expiry date',
        'id_card_file' => 'ID card file',
        'id_card_meta' => 'ID card metadata',
        'id_card_meta.name' => 'ID card name',
        'id_card_meta.number' => 'ID card number',
        'id_card_expiry_date' => 'ID card expiry date',
        'selfie_with_id_card_file' => 'selfie with ID card file',
        'selfie_with_id_card_meta' => 'selfie metadata',
        'avatar_file' => 'avatar file',
        'business_name' => 'business name',
        'business_description' => 'business description',
        'business_category' => 'business category',
        'phone' => 'phone',
        'email' => 'email',
        'website' => 'website',
        'address' => 'address',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation for Profile Verification
    |--------------------------------------------------------------------------
    */

    'profile_verification' => [
        'id_card_file' => [
            'required' => 'ID card file is required.',
            'file' => 'ID card must be a valid file.',
            'mimes' => 'ID card must be a file of type: jpeg, jpg, png, pdf.',
            'max' => 'ID card file may not be greater than 5MB.',
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
        'id_card_expiry_date' => [
            'date' => 'ID card expiry date must be a valid date.',
            'after' => 'ID card expiry date must be a date after today.',
        ],
        'selfie_with_id_card_file' => [
            'required' => 'Selfie with ID card file is required.',
            'file' => 'Selfie must be a valid file.',
            'mimes' => 'Selfie must be a file of type: jpeg, jpg, png.',
            'max' => 'Selfie file may not be greater than 5MB.',
        ],
        'selfie_with_id_card_meta' => [
            'array' => 'Selfie metadata must be an array.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation for Merchant Verification
    |--------------------------------------------------------------------------
    */

    'merchant_verification' => [
        'merchant_document_file' => [
            'required' => 'Merchant document file is required.',
            'file' => 'Merchant document must be a valid file.',
            'mimes' => 'Merchant document must be a file of type: jpeg, jpg, png, pdf.',
            'max' => 'Merchant document file may not be greater than 5MB.',
        ],
        'merchant_document_meta' => [
            'array' => 'Merchant document metadata must be an array.',
        ],
        'banner_file' => [
            'required' => 'Banner file is required.',
            'file' => 'Banner must be a valid file.',
            'mimes' => 'Banner must be a file of type: jpeg, jpg, png.',
            'max' => 'Banner file may not be greater than 5MB.',
        ],
        'banner_meta' => [
            'array' => 'Banner metadata must be an array.',
        ],
        'logo_file' => [
            'required' => 'Logo file is required.',
            'file' => 'Logo must be a valid file.',
            'mimes' => 'Logo must be a file of type: jpeg, jpg, png.',
            'max' => 'Logo file may not be greater than 2MB.',
        ],
    ],

    'first_name' => [
        'string' => 'First name must be a string.',
        'max' => 'First name may not be greater than 255 characters.',
    ],
    'last_name' => [
        'string' => 'Last name must be a string.',
        'max' => 'Last name may not be greater than 255 characters.',
    ],
    'avatar_file' => [
        'file' => 'Avatar must be a valid file.',
        'mimes' => 'Avatar must be a file of type: jpeg, jpg, png.',
        'max' => 'Avatar file may not be greater than 5MB.',
    ],
    'gender' => [
        'in' => 'Selected gender is invalid.',
    ],
    'language' => [
        'string' => 'Language must be a string.',
        'max' => 'Language may not be greater than 10 characters.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Address Validation
    |--------------------------------------------------------------------------
    */

    'type' => [
        'required' => 'Address type is required.',
        'in' => 'Selected address type is invalid.',
    ],
    'label' => [
        'required' => 'Address label is required.',
        'max' => 'Address label may not be greater than :max characters.',
    ],
    'street' => [
        'required' => 'Street address is required.',
        'string' => 'Street address must be a string.',
        'max' => 'Street address may not be greater than :max characters.',
    ],
    'city' => [
        'required' => 'City is required.',
        'max' => 'City may not be greater than :max characters.',
    ],
    'province' => [
        'required' => 'Province is required.',
        'string' => 'Province must be a string.',
        'max' => 'Province may not be greater than :max characters.',
    ],
    'postal_code' => [
        'required' => 'Postal code is required.',
        'max' => 'Postal code may not be greater than :max characters.',
        'regex' => 'Postal code format is invalid.',
    ],
    'latitude' => [
        'numeric' => 'Latitude must be a number.',
        'between' => 'Latitude must be between -90 and 90 degrees.',
    ],
    'longitude' => [
        'numeric' => 'Longitude must be a number.',
        'between' => 'Longitude must be between -180 and 180 degrees.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Merchant Validation
    |--------------------------------------------------------------------------
    */

    'business_name' => [
        'required' => 'Business name is required.',
        'string' => 'Business name must be a string.',
        'max' => 'Business name may not be greater than :max characters.',
    ],
    'business_description' => [
        'string' => 'Business description must be a string.',
        'max' => 'Business description may not be greater than :max characters.',
    ],
    'business_category' => [
        'required' => 'Business category is required.',
        'in' => 'Selected business category is invalid.',
    ],
    'phone' => [
        'required' => 'Phone number is required.',
        'string' => 'Phone number must be a string.',
        'max' => 'Phone number may not be greater than :max characters.',
    ],
    'email' => [
        'email' => 'Email must be a valid email address.',
        'max' => 'Email may not be greater than :max characters.',
    ],
    'website' => [
        'url' => 'Website must be a valid URL.',
        'max' => 'Website may not be greater than :max characters.',
    ],
    'street' => [
        'required' => 'Street address is required.',
        'string' => 'Street address must be a string.',
        'max' => 'Street address may not be greater than :max characters.',
    ],
    'city' => [
        'required' => 'City is required.',
        'string' => 'City must be a string.',
        'max' => 'City may not be greater than :max characters.',
    ],
    'state' => [
        'required' => 'State is required.',
        'string' => 'State must be a string.',
        'max' => 'State may not be greater than :max characters.',
    ],
    'country' => [
        'required' => 'Country is required.',
        'string' => 'Country must be a string.',
        'max' => 'Country may not be greater than :max characters.',
    ],
    'postal_code' => [
        'required' => 'Postal code is required.',
        'string' => 'Postal code must be a string.',
        'max' => 'Postal code may not be greater than :max characters.',
        'regex' => 'Postal code format is invalid.',
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

    /*
    |--------------------------------------------------------------------------
    | Schedule Validation
    |--------------------------------------------------------------------------
    */

    'regular_hours' => [
        'required' => 'Regular hours are required.',
        'array' => 'Regular hours must be an array.',
        'json' => 'Regular hours must be a valid JSON string.',
    ],
    'regular_hours.*.open' => [
        'required' => 'Opening time is required for each day.',
    ],
    'regular_hours.*.close' => [
        'required' => 'Closing time is required for each day.',
    ],
    'regular_hours.*.is_open' => [
        'required' => 'Open status is required for each day.',
    ],
    'special_schedules' => [
        'array' => 'Special schedules must be an array.',
        'json' => 'Special schedules must be a valid JSON string.',
    ],
    'special_schedules.*.date' => [
        'required' => 'Date is required for special schedules.',
    ],
    'special_schedules.*.name' => [
        'required' => 'Name is required for special schedules.',
    ],
    'special_schedules.*.is_open' => [
        'required' => 'Open status is required for special schedules.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Validation
    |--------------------------------------------------------------------------
    */

    'delivery_enabled' => [
        'boolean' => 'Delivery enabled must be true or false.',
    ],
    'delivery_radius_km' => [
        'integer' => 'Delivery radius must be an integer.',
        'min' => 'Delivery radius must be at least :min km.',
        'max' => 'Delivery radius may not be greater than :max km.',
    ],
    'minimum_order_amount' => [
        'numeric' => 'Minimum order amount must be a number.',
        'min' => 'Minimum order amount must be at least :min.',
    ],
    'auto_accept_orders' => [
        'boolean' => 'Auto accept orders must be true or false.',
    ],
    'preparation_time_minutes' => [
        'integer' => 'Preparation time must be an integer.',
        'min' => 'Preparation time must be at least :min minutes.',
        'max' => 'Preparation time may not be greater than :max minutes.',
    ],
    'notifications_enabled' => [
        'boolean' => 'Notifications enabled must be true or false.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Validation
    |--------------------------------------------------------------------------
    */

    'status' => [
        'required' => 'Status is required.',
        'invalid' => 'Selected status is invalid.',
    ],

    'failed' => 'Validation failed.',
];
