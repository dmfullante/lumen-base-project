<?php

return [
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        // add other stores as needed
    ],

    'prefix' => env('CACHE_PREFIX', 'lumen_cache')
];
