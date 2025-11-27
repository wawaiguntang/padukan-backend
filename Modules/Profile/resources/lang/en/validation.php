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

    'accepted' => 'The :attribute field must be accepted.',
    'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
    'active_url' => 'The :attribute field must be a valid URL.',
    'after' => 'The :attribute field must be a date after :date.',
    'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
    'alpha' => 'The :attribute field must only contain letters.',
    'alpha_dash' => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
    'alpha_num' => 'The :attribute field must only contain letters and numbers.',
    'array' => 'The :attribute field must be an array.',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'The :attribute field must be a date before :date.',
    'before_or_equal' => 'The :attribute field must be a date before or equal to :date.',
    'between' => [
        'array' => 'The :attribute field must have between :min and :max items.',
        'file' => 'The :attribute field must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute field must be between :min and :max.',
        'string' => 'The :attribute field must be between :min and :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'can' => 'The :attribute field contains an unauthorized value.',
    'confirmed' => 'The :attribute field confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute field must be a valid date.',
    'date_equals' => 'The :attribute field must be a date equal to :date.',
    'date_format' => 'The :attribute field must match the format :format.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'declined' => 'The :attribute field must be declined.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => 'The :attribute field and :other must be different.',
    'digits' => 'The :attribute field must be :digits digits.',
    'digits_between' => 'The :attribute field must be between :min and :max digits.',
    'dimensions' => 'The :attribute field has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => 'The :attribute field must be a valid email address.',
    'ends_with' => 'The :attribute field must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute field must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'array' => 'The :attribute field must have more than :value items.',
        'file' => 'The :attribute field must be greater than :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than :value.',
        'string' => 'The :attribute field must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The :attribute field must have :value items or more.',
        'file' => 'The :attribute field must be greater than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be greater than or equal to :value.',
        'string' => 'The :attribute field must be greater than or equal to :value characters.',
    ],
    'image' => 'The :attribute field must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field must exist in :other.',
    'integer' => 'The :attribute field must be an integer.',
    'ip' => 'The :attribute field must be a valid IP address.',
    'ipv4' => 'The :attribute field must be a valid IPv4 address.',
    'ipv6' => 'The :attribute field must be a valid IPv6 address.',
    'json' => 'The :attribute field must be a valid JSON string.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => 'The :attribute field must have less than :value items.',
        'file' => 'The :attribute field must be less than :value kilobytes.',
        'numeric' => 'The :attribute field must be less than :value.',
        'string' => 'The :attribute field must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute field must not have more than :value items.',
        'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'string' => 'The :attribute field must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute field must not have more than :max items.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'mimetypes' => 'The :attribute field must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute field must have at least :min items.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute field format is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => 'The :attribute field must contain :size items.',
        'file' => 'The :attribute field must be :size kilobytes.',
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'starts_with' => 'The :attribute field must start with one of the following: :values.',
    'string' => 'The :attribute field must be a string.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'The :attribute field must be a valid URL.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',

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
        'first_name' => 'first name',
        'last_name' => 'last name',
        'avatar' => 'avatar',
        'gender' => 'gender',
        'language' => 'language',
        'type' => 'type',
        'file' => 'file',
        'expiry_date' => 'expiry date',
        'street' => 'street',
        'city' => 'city',
        'province' => 'province',
        'postal_code' => 'postal code',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'is_primary' => 'primary address',
        'business_name' => 'business name',
        'business_type' => 'business type',
        'business_phone' => 'business phone',
        'tax_id' => 'tax ID',
        'business_license_number' => 'business license number',
        'bank_account_number' => 'bank account number',
        'preferences' => 'preferences',
        'loyalty_points' => 'loyalty points',
        'membership_level' => 'membership level',
        'brand' => 'brand',
        'model' => 'model',
        'year' => 'year',
        'color' => 'color',
        'license_plate' => 'license plate',
        'sim_number' => 'SIM number',
        'sim_expiry_date' => 'SIM expiry date',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Error Messages for Exceptions
    |--------------------------------------------------------------------------
    |
    | These are used by exceptions in the Profile module
    |
    */

    'profile_not_found' => 'Profile not found.',
    'profile_already_exists' => 'Profile already exists for this user.',
    'address_not_found' => 'Address not found.',
    'invalid_address_type' => 'Invalid address type.',
    'invalid_coordinates' => 'Invalid latitude or longitude coordinates.',
    'primary_address_required' => 'At least one primary address is required.',
    'bank_not_found' => 'Bank not found.',
    'bank_code_exists' => 'Bank code already exists.',
    'invalid_bank_code' => 'Bank code must be alphanumeric and uppercase.',
    'driver_profile_not_found' => 'Driver profile not found.',
    'vehicle_not_found' => 'Vehicle not found.',
    'document_not_found' => 'Document not found.',
    'invalid_vehicle_type' => 'Invalid vehicle type.',
    'invalid_document_type' => 'Invalid document type.',
    'missing_required_documents' => 'Missing required documents for verification.',
    'no_vehicle_registered' => 'At least one vehicle must be registered.',
    'already_verified' => 'Profile is already verified.',
    'verification_pending' => 'Verification is still pending.',
    'merchant_profile_not_found' => 'Merchant profile not found.',
    'invalid_business_type' => 'Invalid business type.',
    'customer_profile_not_found' => 'Customer profile not found.',
    'file_upload_failed' => 'File upload failed.',
    'invalid_avatar_file' => 'Invalid avatar file. Only JPEG, PNG, JPG files under 5MB are allowed.',
    'avatar_upload_failed' => 'Avatar upload failed.',
    'invalid_document_file' => 'Invalid document file for type: :type.',
    'document_upload_failed' => 'Document upload failed for type: :type.',
    'error_occurred' => 'An error occurred while processing your request.',
    'type_required' => 'Type is required.',
    'expiry_date_must_be_future' => 'Expiry date must be in the future.',
    'update_failed' => 'Update failed.',
    'failed' => 'Operation failed.',
    'invalid_gender' => 'Invalid gender specified.',
    'invalid_verification_status' => 'Invalid verification status.',
    'file_required' => 'File is required.',
    'invalid_file_type' => 'Invalid file type.',
    'file_too_large' => 'File size exceeds the maximum limit.',
    'invalid_token' => 'Invalid or missing authentication token.',
    'access_denied' => 'Access denied. Insufficient permissions.',
    'verification_requested' => 'Verification request submitted successfully.',
    'verification_status_retrieved' => 'Verification status retrieved successfully.',
    'documents_submitted' => 'Documents submitted successfully.',
    'documents_retrieved' => 'Documents retrieved successfully.',
    'vehicles_retrieved' => 'Vehicles retrieved successfully.',
    'vehicle_registered' => 'Vehicle registered successfully.',
    'vehicle_updated' => 'Vehicle updated successfully.',
    'vehicle_removed' => 'Vehicle removed successfully.',
    'all_documents_required' => 'All required documents must be submitted: :types.',
    'document_verification_pending' => 'Document verification is still pending.',
    'driver_not_verified' => 'Driver profile is not verified yet.',

    // Merchant validation messages
    'id_card_file_required' => 'ID card file is required.',
    'id_card_file_invalid_format' => 'ID card file must be JPEG, PNG, JPG, or PDF.',
    'id_card_number_required' => 'ID card number is required.',
    'id_card_number_invalid' => 'ID card number must be 16 digits.',
    'id_card_expiry_required' => 'ID card expiry date is required.',
    'store_file_required' => 'Store/business license file is required.',
    'store_file_invalid_format' => 'Store file must be JPEG, PNG, JPG, or PDF.',
    'license_number_required' => 'License number is required.',
    'license_expiry_required' => 'License expiry date is required.',
    'bank_id_required' => 'Bank selection is required.',
    'bank_id_invalid' => 'Invalid bank selected.',
    'bank_not_found' => 'Selected bank not found.',
    'account_number_required' => 'Account number is required.',
    'account_number_invalid' => 'Account number must contain only numbers.',
    'account_number_too_short' => 'Account number must be at least 10 digits.',
    'account_number_too_long' => 'Account number must not exceed 20 digits.',
    'is_primary_invalid' => 'Primary account flag must be true or false.',
    'street_required' => 'Street address is required.',
    'street_too_long' => 'Street address must not exceed 255 characters.',
    'city_required' => 'City is required.',
    'city_too_long' => 'City must not exceed 100 characters.',
    'province_required' => 'Province is required.',
    'province_too_long' => 'Province must not exceed 100 characters.',
    'postal_code_required' => 'Postal code is required.',
    'postal_code_invalid' => 'Postal code must be 5 digits.',
    'latitude_required' => 'Latitude is required.',
    'latitude_invalid' => 'Latitude must be a valid number.',
    'latitude_out_of_range' => 'Latitude must be between -90 and 90.',
    'longitude_required' => 'Longitude is required.',
    'longitude_invalid' => 'Longitude must be a valid number.',
    'longitude_out_of_range' => 'Longitude must be between -180 and 180.',
];