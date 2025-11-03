<?php

return [
    'path' => 'openkit',

    'json_file_name' => 'openapi.json',

    'info' => [
        'title' => env('APP_NAME', 'Laravel API'),
        'description' => 'Documentação da API gerada pelo OpenKit.',
        'version' => '1.0.0',
        'contact' => [
            'email' => 'api@example.com',
        ],
    ],

    'definitions' => base_path('routes/openkit.php'),

    'ui' => [
        'title' => env('APP_NAME', 'Laravel API').' Docs',
        'cdn_url' => 'https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.9.0',
    ],
];
