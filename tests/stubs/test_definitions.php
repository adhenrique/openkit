<?php

use OpenKit\Facades\OpenKit;

// Define uma rota de exemplo usando a Facade
// (Isso popula o singleton OpenKitBuilder)
OpenKit::defineTag('TestTag', 'Tag de Teste');
OpenKit::path('/api/new', 'get')
    ->summary('Nova Rota')
    ->tag('TestTag')
    ->withResponse(200, function ($res) {
        $res->description('Rota OK');
    });
