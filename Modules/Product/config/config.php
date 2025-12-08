<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the Product module including
    | cache settings, database connections, and other module-specific
    | configurations.
    |
    */

    'name' => 'Product',

    'database' => [
        'connection' => 'pgsql',
        'host' => env('PRODUCT_DB_HOST', '127.0.0.1'),
        'port' => env('PRODUCT_DB_PORT', '3306'),
        'database' => env('PRODUCT_DB_DATABASE', 'product'),
        'username' => env('PRODUCT_DB_USERNAME', 'forge'),
        'password' => env('PRODUCT_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Cache Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for caching in the Product module. All cache settings
        | are centralized here for easy maintenance and environment-specific
        | tuning.
        |
        */

        'enabled' => env('PRODUCT_CACHE_ENABLED', true),

        'ttl' => [
            'entity' => env('PRODUCT_CACHE_ENTITY_TTL', 900),    // 15 minutes
            'list' => env('PRODUCT_CACHE_LIST_TTL', 1800),       // 30 minutes
            'tree' => env('PRODUCT_CACHE_TREE_TTL', 3600),       // 1 hour
            'stats' => env('PRODUCT_CACHE_STATS_TTL', 7200),     // 2 hours
        ],

        'prefix' => env('PRODUCT_CACHE_PREFIX', 'product'),

        'tags' => [
            'enabled' => env('PRODUCT_CACHE_TAGS_ENABLED', true),
            'categories' => 'product:categories',
            'products' => 'product:products',
            'variants' => 'product:variants',
        ],

        /*
        |--------------------------------------------------------------------------
        | Category Cache Configuration
        |--------------------------------------------------------------------------
        |
        | Specific cache settings for category operations.
        |
        */

        'category' => [
            'enabled' => env('PRODUCT_CATEGORY_CACHE_ENABLED', true),
            'entity_ttl' => env('PRODUCT_CATEGORY_ENTITY_TTL', 900),    // 15 min
            'list_ttl' => env('PRODUCT_CATEGORY_LIST_TTL', 1800),       // 30 min
            'tree_ttl' => env('PRODUCT_CATEGORY_TREE_TTL', 3600),       // 1 hour
            'max_children_cache' => env('PRODUCT_CATEGORY_MAX_CHILDREN_CACHE', 100),
        ],

        /*
        |--------------------------------------------------------------------------
        | Product Cache Configuration
        |--------------------------------------------------------------------------
        |
        | Specific cache settings for product operations.
        |
        */

        'product' => [
            'enabled' => env('PRODUCT_PRODUCT_CACHE_ENABLED', true),
            'entity_ttl' => env('PRODUCT_PRODUCT_ENTITY_TTL', 600),     // 10 min
            'list_ttl' => env('PRODUCT_PRODUCT_LIST_TTL', 1200),        // 20 min
            'search_ttl' => env('PRODUCT_PRODUCT_SEARCH_TTL', 1800),    // 30 min
        ],

        /*
        |--------------------------------------------------------------------------
        | Cache Warming Configuration
        |--------------------------------------------------------------------------
        |
        | Settings for cache warming operations.
        |
        */

        'warming' => [
            'enabled' => env('PRODUCT_CACHE_WARMING_ENABLED', true),
            'schedule' => env('PRODUCT_CACHE_WARMING_SCHEDULE', '0 */4 * * *'), // Every 4 hours
            'timeout' => env('PRODUCT_CACHE_WARMING_TIMEOUT', 300),     // 5 minutes
        ],

        /*
        |--------------------------------------------------------------------------
        | Cache Monitoring Configuration
        |--------------------------------------------------------------------------
        |
        | Settings for cache monitoring and health checks.
        |
        */

        'monitoring' => [
            'enabled' => env('PRODUCT_CACHE_MONITORING_ENABLED', true),
            'health_check_interval' => env('PRODUCT_CACHE_HEALTH_CHECK_INTERVAL', 300), // 5 min
            'alert_threshold' => env('PRODUCT_CACHE_ALERT_THRESHOLD', 80), // 80% hit rate
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Elasticsearch integration in the Product module.
    |
    */

    'elasticsearch' => [
        'enabled' => env('PRODUCT_ELASTICSEARCH_ENABLED', false),
        'hosts' => env('PRODUCT_ELASTICSEARCH_HOSTS', 'localhost:9200'),
        'index' => env('PRODUCT_ELASTICSEARCH_INDEX', 'products'),
        'sync_enabled' => env('PRODUCT_ELASTICSEARCH_SYNC_ENABLED', false),
        'sync_batch_size' => env('PRODUCT_ELASTICSEARCH_SYNC_BATCH_SIZE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads in the Product module.
    |
    */

    'uploads' => [
        'disk' => env('PRODUCT_UPLOAD_DISK', 'public'),
        'path' => env('PRODUCT_UPLOAD_PATH', 'products'),
        'max_size' => env('PRODUCT_UPLOAD_MAX_SIZE', 5120), // KB
        'allowed_mimes' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for business rules and limits.
    |
    */

    'business' => [
        'max_categories_per_merchant' => env('PRODUCT_MAX_CATEGORIES_PER_MERCHANT', 100),
        'max_products_per_category' => env('PRODUCT_MAX_PRODUCTS_PER_CATEGORY', 1000),
        'max_variants_per_product' => env('PRODUCT_MAX_VARIANTS_PER_PRODUCT', 50),
        'max_images_per_product' => env('PRODUCT_MAX_IMAGES_PER_PRODUCT', 10),
    ],
];
