<?php

return [
    'name' => 'Customer',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Customer module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('CUSTOMER_DB_HOST', '127.0.0.3'),
        'port' => env('CUSTOMER_DB_PORT', '5432'),
        'database' => env('CUSTOMER_DB_DATABASE', 'forge'),
        'username' => env('CUSTOMER_DB_USERNAME', 'forge'),
        'password' => env('CUSTOMER_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
