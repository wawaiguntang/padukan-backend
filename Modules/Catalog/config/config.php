<?php

return [
    'name' => 'Catalog',
    'elasticsearch' => [
        'hosts' => explode(',', env('ELASTICSEARCH_HOSTS', 'localhost:9200')),
        'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'padukan_'),
    ],
];
