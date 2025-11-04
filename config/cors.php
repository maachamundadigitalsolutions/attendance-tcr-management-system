<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
    'http://localhost:8000',
    'http://127.0.0.1:8000',
    ], // frontend origin
    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization'],
    'supports_credentials' => false,
];

