<?php

namespace OpenKit\Tests\Feature;

use OpenKit\Tests\TestCase;

class DocsRouteTest extends TestCase
{
    public function test_it_loads_the_documentation_ui_route_correctly()
    {
        // 1. ARRANGE
        // Pega os valores da config que definimos no TestCase
        $routePrefix = config('openkit.path'); // 'openkit-test-docs'
        $expectedTitle = config('openkit.ui.title'); // 'Minha API de Teste (UI)'
        $expectedJsonUrl = asset(config('openkit.json_file_name')); // 'http://localhost/api-test.json'
        $expectedCdnUrl = config('openkit.ui.cdn_url'); // 'https://fake-cdn-url.com'

        // 2. ACT
        // Acessa a rota (ex: /openkit-test-docs/docs)
        $response = $this->get("/{$routePrefix}/docs");

        // 3. ASSERT

        // A rota respondeu com 200 OK?
        $response->assertStatus(200);

        // A view correta foi carregada? (namespace::view)
        $response->assertViewIs('openkit::index');

        // O título da config está no HTML?
        $response->assertSee($expectedTitle);

        // O container da UI do Swagger está no HTML?
        // (usamos 'false' para não escapar o HTML na busca)
        $response->assertSee('id="swagger-ui"', false);

        // A URL do JSON (lida da config) está no script?
        $response->assertSee($expectedJsonUrl);

        // A URL do CDN (lida da config) está no <link>?
        $response->assertSee($expectedCdnUrl);
    }

    public function test_it_returns_404_for_non_existing_docs_routes()
    {
        // Acessa uma rota que não existe no nosso pacote
        $response = $this->get('/openkit-test-docs/outra-coisa');

        $response->assertStatus(404);
    }
}
