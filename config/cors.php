<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,DELETE,OPTIONS')),
    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') === '*' ? ['*'] : explode(',', env('CORS_ALLOWED_ORIGINS', '*')),
    'allowed_origins_patterns' => [],
    'allowed_headers' => env('CORS_ALLOWED_HEADERS') === '*' ? ['*'] : explode(',', env('CORS_ALLOWED_HEADERS', '*')),
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),
];
