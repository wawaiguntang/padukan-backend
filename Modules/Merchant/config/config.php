<?php

return [
    'name' => 'Merchant',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Merchant module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('MERCHANT_DB_HOST', '127.0.0.4'),
        'port' => env('MERCHANT_DB_PORT', '5432'),
        'database' => env('MERCHANT_DB_DATABASE', 'forge'),
        'username' => env('MERCHANT_DB_USERNAME', 'forge'),
        'password' => env('MERCHANT_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
