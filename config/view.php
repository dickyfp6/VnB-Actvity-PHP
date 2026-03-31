<?php

return [
    'cache' => env('VIEW_CACHE_EXPIRATION'),

    'paths' => [
        resource_path('views'),
    ],

    'compiled' => realpath(storage_path('framework/views')),
];
