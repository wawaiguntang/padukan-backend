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
];
