<?php

return [
    'name' => 'Setting',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Setting module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('SETTING_DB_HOST', '127.0.0.3'),
        'port' => env('SETTING_DB_PORT', '5432'),
        'database' => env('SETTING_DB_DATABASE', 'forge'),
        'username' => env('SETTING_DB_USERNAME', 'forge'),
        'password' => env('SETTING_DB_PASSWORD', ''),
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
    | Here you may configure the cache settings for the Setting module.
    | TTL values are in seconds.
    |
    */

    'cache' => [
        'lookup_ttl' => env('SETTING_CACHE_LOOKUP_TTL', 3600), // 1 hour
        'group_ttl' => env('SETTING_CACHE_GROUP_TTL', 1800), // 30 minutes
        'keys_ttl' => env('SETTING_CACHE_KEYS_TTL', 1800), // 30 minutes
    ],
];
