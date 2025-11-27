<?php

return [
    'name' => 'Authorization',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Authentication module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('AUTHORIZATION_DB_HOST', '127.0.0.2'),
        'port' => env('AUTHORIZATION_DB_PORT', '5432'),
        'database' => env('AUTHORIZATION_DB_DATABASE', 'forge'),
        'username' => env('AUTHORIZATION_DB_USERNAME', 'forge'),
        'password' => env('AUTHORIZATION_DB_PASSWORD', ''),
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
    | Here you may configure the cache settings for the Authorization module.
    | TTL values are in seconds.
    |
    */

    'cache' => [
        'user_roles_ttl' => env('AUTHORIZATION_CACHE_USER_ROLES_TTL', 3600), // 1 hour
        'user_permissions_ttl' => env('AUTHORIZATION_CACHE_USER_PERMISSIONS_TTL', 3600), // 1 hour
        'role_permissions_ttl' => env('AUTHORIZATION_CACHE_ROLE_PERMISSIONS_TTL', 3600), // 1 hour
        'lookup_ttl' => env('AUTHORIZATION_CACHE_LOOKUP_TTL', 3600), // 1 hour
        'policy_ttl' => env('AUTHORIZATION_CACHE_POLICY_TTL', 1800), // 30 minutes
    ],
];
