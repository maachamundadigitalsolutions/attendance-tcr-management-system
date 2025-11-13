<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://127.0.0.1:8000'], // frontend origin
    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization'],
    'supports_credentials' => true,
];

