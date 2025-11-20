<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // 'allowed_origins' => ['http://127.0.0.1:8000'], // 👈 frontend origin
    // 'allowed_origins' => ['*'],
    'allowed_origins' => ['http://192.168.1.27:8000'],
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];



