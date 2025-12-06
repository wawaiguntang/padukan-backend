<?php

return [
    'name' => 'Product',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Product module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('PRODUCT_DB_HOST', '127.0.0.2'),
        'port' => env('PRODUCT_DB_PORT', '5432'),
        'database' => env('PRODUCT_DB_DATABASE', 'forge'),
        'username' => env('PRODUCT_DB_USERNAME', 'forge'),
        'password' => env('PRODUCT_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
