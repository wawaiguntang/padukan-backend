<?php

return [
    'name' => 'Driver',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Driver module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('DRIVER_DB_HOST', '127.0.0.3'),
        'port' => env('DRIVER_DB_PORT', '5432'),
        'database' => env('DRIVER_DB_DATABASE', 'forge'),
        'username' => env('DRIVER_DB_USERNAME', 'forge'),
        'password' => env('DRIVER_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
