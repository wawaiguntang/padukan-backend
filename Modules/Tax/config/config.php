<?php

return [
    'name' => 'Tax',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Tax module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('TAX_DB_HOST', '127.0.0.4'),
        'port' => env('TAX_DB_PORT', '5432'),
        'database' => env('TAX_DB_DATABASE', 'forge'),
        'username' => env('TAX_DB_USERNAME', 'forge'),
        'password' => env('TAX_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
