<?php

namespace OpenKit\Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use OpenKit\Facades\OpenKit;
use OpenKit\OpenKitBuilder;
use OpenKit\Tests\TestCase;

class OpenKitBuilderTest extends TestCase
{
    /** @var OpenKitBuilder */
    private mixed $builder;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->app->make(OpenKitBuilder::class);
    }

    public function test_it_is_registered_as_a_singleton()
    {
        $builder1 = $this->app->make(OpenKitBuilder::class);
        $builder2 = $this->app->make(OpenKitBuilder::class);

        // Verifica se ambas as variáveis apontam para o MESMO objeto
        $this->assertSame($builder1, $builder2);
        $this->assertSame($this->builder, $builder1);
    }

    public function test_it_can_define_tags_and_schemas()
    {
        $this->builder->defineTag('Tag 1', 'Desc 1');
        $this->builder->defineSchema('Schema 1', ['type' => 'string']);

        $spec = $this->builder->generate();

        $expectedTags = [
            ['name' => 'Tag 1', 'description' => 'Desc 1'],
        ];
        $expectedSchemas = [
            'Schema 1' => ['type' => 'string'],
        ];

        $this->assertEquals($expectedTags, $spec['tags']);
        $this->assertEquals($expectedSchemas, $spec['components']['schemas']);
    }

    public function test_it_can_define_security_schemes()
    {
        $this->builder->defineBearerAuth('bearer');
        $this->builder->defineApiKeyHeader('apiKey', 'X-Key');

        $spec = $this->builder->generate();

        $this->assertArrayHasKey('bearer', $spec['components']['securitySchemes']);
        $this->assertArrayHasKey('apiKey', $spec['components']['securitySchemes']);
        $this->assertEquals('http', $spec['components']['securitySchemes']['bearer']['type']);
        $this->assertEquals('apiKey', $spec['components']['securitySchemes']['apiKey']['type']);
    }

    public function test_it_uses_config_for_info_and_servers()
    {
        // Usamos a config definida em `tests/TestCase.php`
        $spec = $this->builder->generate();

        $this->assertEquals('API de Teste', $spec['info']['title']);
        $this->assertEquals('1.0.0', $spec['info']['version']);

        // Verifica se o helper `url('/')` foi usado (padrão do Testbench é 'http://localhost')
        $this->assertEquals('http://localhost', $spec['servers'][0]['url']);
    }

    public function test_it_builds_paths_from_operation_builders()
    {
        OpenKit::path('/users', 'get')
            ->summary('Get Users')
            ->withResponse(200, function ($res) {
                $res->description('OK');
            });

        // Pega a instância (que é a mesma, por ser singleton)
        // e gera o spec
        $spec = $this->builder->generate();

        $this->assertArrayHasKey('/users', $spec['paths']);
        $this->assertArrayHasKey('get', $spec['paths']['/users']);
        $this->assertEquals('Get Users', $spec['paths']['/users']['get']['summary']);
        $this->assertEquals('OK', $spec['paths']['/users']['get']['responses']['200']['description']);
    }
}
