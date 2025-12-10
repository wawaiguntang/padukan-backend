<?php

return [
    'name' => 'Region',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database connection for the Region module.
    | This allows the module to use a separate database connection if needed.
    |
    */

    'database' => [
        'driver' => 'pgsql',
        'host' => env('REGION_DB_HOST', '127.0.0.4'),
        'port' => env('REGION_DB_PORT', '5432'),
        'database' => env('REGION_DB_DATABASE', 'forge'),
        'username' => env('REGION_DB_USERNAME', 'forge'),
        'password' => env('REGION_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
