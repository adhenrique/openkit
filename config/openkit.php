<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caminho base (prefixo) das rotas da OpenKit
    |--------------------------------------------------------------------------
    |
    | Define o prefixo das rotas utilizadas para acessar a documentação.
    | Exemplo: http://seu-app.test/openkit/docs
    |
    */

    'path' => env('OPENKIT_PATH', 'openkit'),

    /*
    |--------------------------------------------------------------------------
    | Nome do arquivo JSON
    |--------------------------------------------------------------------------
    |
    | Define o nome do arquivo JSON exportado contendo a especificação OpenAPI.
    |
    */

    'json_file_name' => env('OPENKIT_JSON_FILE_NAME', 'openapi.json'),

    /*
    |--------------------------------------------------------------------------
    | Informações da documentação
    |--------------------------------------------------------------------------
    |
    | Dados apresentados no cabeçalho da documentação gerada, como título,
    | descrição, versão da API e contato técnico.
    |
    */

    'info' => [
        'title' => env('OPENKIT_TITLE', env('APP_NAME', 'Laravel API')),
        'description' => env('OPENKIT_DESCRIPTION', 'Documentação da API gerada pelo OpenKit.'),
        'version' => env('OPENKIT_API_VERSION', '1.0.0'),
        'contact' => [
            'email' => env('OPENKIT_CONTACT_EMAIL', 'api@example.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Caminho das definições OpenAPI
    |--------------------------------------------------------------------------
    |
    | Caminho do arquivo onde serão registradas as rotas e definições da API
    | usando a interface fluente do OpenKit.
    |
    */

    'definitions' => env('OPENKIT_DEFINITIONS', base_path('routes/openkit.php')),

    /*
    |--------------------------------------------------------------------------
    | Configurações da interface visual (Swagger UI)
    |--------------------------------------------------------------------------
    |
    | Define o título da página da documentação e a CDN utilizada para carregar
    | o Swagger UI.
    |
    */

    'ui' => [
        'title' => env('OPENKIT_UI_TITLE', env('APP_NAME', 'Laravel API').' Docs'),
        'cdn_url' => env('OPENKIT_UI_CDN_URL', 'https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.9.0'),
    ],

];
