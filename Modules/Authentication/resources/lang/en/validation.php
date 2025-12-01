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

    'accepted' => 'Please accept the :attribute.',
    'accepted_if' => 'Please accept the :attribute when :other is :value.',
    'active_url' => 'Please provide a valid URL for :attribute.',
    'after' => 'Please choose a date after :date for :attribute.',
    'after_or_equal' => 'Please choose a date after or equal to :date for :attribute.',
    'alpha' => 'Please use only letters in :attribute.',
    'alpha_dash' => 'Please use only letters, numbers, dashes, and underscores in :attribute.',
    'alpha_num' => 'Please use only letters and numbers in :attribute.',
    'array' => 'Please provide an array for :attribute.',
    'ascii' => 'Please use only single-byte alphanumeric characters and symbols in :attribute.',
    'before' => 'Please choose a date before :date for :attribute.',
    'before_or_equal' => 'Please choose a date before or equal to :date for :attribute.',
    'between' => [
        'array' => 'Please provide between :min and :max items for :attribute.',
        'file' => 'Please provide a file between :min and :max kilobytes for :attribute.',
        'numeric' => 'Please provide a number between :min and :max for :attribute.',
        'string' => 'Please provide between :min and :max characters for :attribute.',
    ],
    'boolean' => 'Please choose true or false for :attribute.',
    'can' => 'The :attribute contains an unauthorized value.',
    'confirmed' => 'The confirmation for :attribute doesn\'t match.',
    'current_password' => 'That password is incorrect.',
    'date' => 'Please provide a valid date for :attribute.',
    'date_equals' => 'Please choose :date for :attribute.',
    'date_format' => 'Please use the format :format for :attribute.',
    'decimal' => 'Please provide :decimal decimal places for :attribute.',
    'declined' => 'Please decline the :attribute.',
    'declined_if' => 'Please decline the :attribute when :other is :value.',
    'different' => 'Please make :attribute different from :other.',
    'digits' => 'Please provide :digits digits for :attribute.',
    'digits_between' => 'Please provide between :min and :max digits for :attribute.',
    'dimensions' => 'Please provide an image with valid dimensions for :attribute.',
    'distinct' => 'Please remove duplicate values from :attribute.',
    'doesnt_end_with' => 'Please don\'t end :attribute with :values.',
    'doesnt_start_with' => 'Please don\'t start :attribute with :values.',
    'email' => 'Please provide a valid email address for :attribute.',
    'ends_with' => 'Please end :attribute with :values.',
    'enum' => 'Please choose a valid option for :attribute.',
    'exists' => 'Please choose a valid option for :attribute.',
    'file' => 'Please provide a file for :attribute.',
    'filled' => 'Please provide a value for :attribute.',
    'gt' => [
        'array' => 'Please provide more than :value items for :attribute.',
        'file' => 'Please provide a file larger than :value kilobytes for :attribute.',
        'numeric' => 'Please provide a number greater than :value for :attribute.',
        'string' => 'Please provide more than :value characters for :attribute.',
    ],
    'gte' => [
        'array' => 'Please provide :value items or more for :attribute.',
        'file' => 'Please provide a file :value kilobytes or larger for :attribute.',
        'numeric' => 'Please provide a number :value or greater for :attribute.',
        'string' => 'Please provide :value characters or more for :attribute.',
    ],
    'image' => 'Please provide an image for :attribute.',
    'in' => 'Please choose a valid option for :attribute.',
    'in_array' => 'Please choose a value from :other for :attribute.',
    'integer' => 'Please provide an integer for :attribute.',
    'ip' => 'Please provide a valid IP address for :attribute.',
    'ipv4' => 'Please provide a valid IPv4 address for :attribute.',
    'ipv6' => 'Please provide a valid IPv6 address for :attribute.',
    'json' => 'Please provide a valid JSON string for :attribute.',
    'lowercase' => 'Please provide lowercase for :attribute.',
    'lt' => [
        'array' => 'Please provide less than :value items for :attribute.',
        'file' => 'Please provide a file smaller than :value kilobytes for :attribute.',
        'numeric' => 'Please provide a number less than :value for :attribute.',
        'string' => 'Please provide less than :value characters for :attribute.',
    ],
    'lte' => [
        'array' => 'Please provide no more than :value items for :attribute.',
        'file' => 'Please provide a file no larger than :value kilobytes for :attribute.',
        'numeric' => 'Please provide a number no greater than :value for :attribute.',
        'string' => 'Please provide no more than :value characters for :attribute.',
    ],
    'mac_address' => 'Please provide a valid MAC address for :attribute.',
    'max' => [
        'array' => 'Please provide no more than :max items for :attribute.',
        'file' => 'Please provide a file no larger than :max kilobytes for :attribute.',
        'numeric' => 'Please provide a number no greater than :max for :attribute.',
        'string' => 'Please provide no more than :max characters for :attribute.',
    ],
    'max_digits' => 'Please provide no more than :max digits for :attribute.',
    'mimes' => 'Please provide a file of type :values for :attribute.',
    'mimetypes' => 'Please provide a file of type :values for :attribute.',
    'min' => [
        'array' => 'Please provide at least :min items for :attribute.',
        'file' => 'Please provide a file at least :min kilobytes for :attribute.',
        'numeric' => 'Please provide a number at least :min for :attribute.',
        'string' => 'Please provide at least :min characters for :attribute.',
    ],
    'min_digits' => 'Please provide at least :min digits for :attribute.',
    'missing' => 'Please do not include :attribute.',
    'missing_if' => 'Please do not include :attribute when :other is :value.',
    'missing_unless' => 'Please do not include :attribute unless :other is :value.',
    'missing_with' => 'Please do not include :attribute when :values is present.',
    'missing_with_all' => 'Please do not include :attribute when :values are present.',
    'multiple_of' => 'Please provide a multiple of :value for :attribute.',
    'not_in' => 'Please choose a valid option for :attribute.',
    'not_regex' => 'Please provide a valid format for :attribute.',
    'numeric' => 'Please provide a number for :attribute.',
    'password' => 'That password is incorrect.',
    'present' => 'Please include :attribute.',
    'prohibited' => 'Please do not include :attribute.',
    'prohibited_if' => 'Please do not include :attribute when :other is :value.',
    'prohibited_unless' => 'Please do not include :attribute unless :other is in :values.',
    'prohibits' => 'Please do not include :other when :attribute is present.',
    'regex' => 'Please provide a valid format for :attribute.',
    'required' => 'Please provide the :attribute.',
    'required_array_keys' => 'Please include entries for :values in :attribute.',
    'required_if' => 'Please provide the :attribute when :other is :value.',
    'required_if_accepted' => 'Please provide the :attribute when :other is accepted.',
    'required_unless' => 'Please provide the :attribute unless :other is in :values.',
    'required_with' => 'Please provide the :attribute when :values is present.',
    'required_with_all' => 'Please provide the :attribute when :values are present.',
    'required_without' => 'Please provide the :attribute when :values is not present.',
    'required_without_all' => 'Please provide the :attribute when none of :values are present.',
    'same' => 'Please make :attribute match :other.',
    'size' => [
        'array' => 'Please provide exactly :size items for :attribute.',
        'file' => 'Please provide a file of :size kilobytes for :attribute.',
        'numeric' => 'Please provide the number :size for :attribute.',
        'string' => 'Please provide exactly :size characters for :attribute.',
    ],
    'starts_with' => 'Please start :attribute with :values.',
    'string' => 'Please provide a string for :attribute.',
    'timezone' => 'Please provide a valid timezone for :attribute.',
    'unique' => 'That :attribute is already taken.',
    'uploaded' => 'Failed to upload :attribute.',
    'uppercase' => 'Please provide uppercase for :attribute.',
    'url' => 'Please provide a valid URL for :attribute.',
    'ulid' => 'Please provide a valid ULID for :attribute.',
    'uuid' => 'Please provide a valid UUID for :attribute.',

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
        'phone' => 'phone number',
        'email' => 'email address',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
        'identifier' => 'email or phone number',
        'user_id' => 'user ID',
        'token' => 'verification token',
        'type' => 'verification type',
        'refresh_token' => 'refresh token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Messages
    |--------------------------------------------------------------------------
    */

    'identifier' => [
        'required' => 'Either email or phone number is required.',
    ],
];