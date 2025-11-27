<?php

return [
    'name' => 'Profile',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Profile module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('PROFILE_DB_HOST', '127.0.0.3'),
        'port' => env('PROFILE_DB_PORT', '5432'),
        'database' => env('PROFILE_DB_DATABASE', 'forge'),
        'username' => env('PROFILE_DB_USERNAME', 'forge'),
        'password' => env('PROFILE_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the cache settings for the Profile module.
    | TTL values are in seconds.
    |
    */

    'cache' => [
        'profile_ttl' => env('PROFILE_CACHE_PROFILE_TTL', 900), // 15 minutes
        'addresses_ttl' => env('PROFILE_CACHE_ADDRESSES_TTL', 900), // 15 minutes
        'banks_ttl' => env('PROFILE_CACHE_BANKS_TTL', 3600), // 1 hour
        'driver_profile_ttl' => env('PROFILE_CACHE_DRIVER_PROFILE_TTL', 900), // 15 minutes
        'driver_vehicles_ttl' => env('PROFILE_CACHE_DRIVER_VEHICLES_TTL', 900), // 15 minutes
        'driver_documents_ttl' => env('PROFILE_CACHE_DRIVER_DOCUMENTS_TTL', 900), // 15 minutes
        'merchant_profile_ttl' => env('PROFILE_CACHE_MERCHANT_PROFILE_TTL', 900), // 15 minutes
        'merchant_banks_ttl' => env('PROFILE_CACHE_MERCHANT_BANKS_TTL', 900), // 15 minutes
        'merchant_addresses_ttl' => env('PROFILE_CACHE_MERCHANT_ADDRESSES_TTL', 900), // 15 minutes
        'merchant_documents_ttl' => env('PROFILE_CACHE_MERCHANT_DOCUMENTS_TTL', 900), // 15 minutes
        'customer_profile_ttl' => env('PROFILE_CACHE_CUSTOMER_PROFILE_TTL', 900), // 15 minutes
        'customer_documents_ttl' => env('PROFILE_CACHE_CUSTOMER_DOCUMENTS_TTL', 900), // 15 minutes
        'lookup_ttl' => env('PROFILE_CACHE_LOOKUP_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the file upload settings for the Profile module.
    |
    */

    'uploads' => [
        'disk' => env('PROFILE_UPLOAD_DISK', 'public'),
        'max_file_size' => env('PROFILE_MAX_FILE_SIZE', 5120), // KB (5MB)
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/jpg',
            'application/pdf',
        ],
        'avatar_path' => 'profiles/avatars',
        'document_path' => 'profiles/documents',
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the verification settings for the Profile module.
    |
    */

    'verification' => [
        'auto_verify_documents' => env('PROFILE_AUTO_VERIFY_DOCUMENTS', false),
        'require_document_expiry' => env('PROFILE_REQUIRE_DOCUMENT_EXPIRY', true),
        'verification_expiry_days' => env('PROFILE_VERIFICATION_EXPIRY_DAYS', 365), // 1 year
    ],
];
