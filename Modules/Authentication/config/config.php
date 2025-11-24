<?php

return [
    'name' => 'Authentication',

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
        'host' => env('AUTHENTICATION_DB_HOST', '127.0.0.2'),
        'port' => env('AUTHENTICATION_DB_PORT', '5432'),
        'database' => env('AUTHENTICATION_DB_DATABASE', 'forge'),
        'username' => env('AUTHENTICATION_DB_USERNAME', 'forge'),
        'password' => env('AUTHENTICATION_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
