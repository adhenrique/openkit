<?php

namespace OpenKit\Tests\Unit;

use OpenKit\Builders\ResponseBuilder;
use OpenKit\Tests\TestCase;

class ResponseBuilderTest extends TestCase
{
    public function test_it_throws_an_exception_if_description_is_missing_on_build()
    {
        // Espera que uma exceção seja lançada
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A descrição da resposta (description) é obrigatória');

        $builder = new ResponseBuilder;
        $builder->build();
    }

    public function test_it_builds_a_minimal_valid_response()
    {
        $builder = new ResponseBuilder;
        $builder->description('Recurso não encontrado');

        $result = $builder->build();

        $expected = [
            'description' => 'Recurso não encontrado',
        ];

        // Não deve ter 'content' ou 'headers'
        $this->assertEquals($expected, $result);
        $this->assertArrayNotHasKey('content', $result);
        $this->assertArrayNotHasKey('headers', $result);
    }

    public function test_it_correctly_builds_a_json_content_response()
    {
        $builder = new ResponseBuilder;
        $schema = [
            'type' => 'object',
            'properties' => ['id' => ['type' => 'integer']],
        ];

        $builder->description('Sucesso')
            ->jsonContent($schema);

        $result = $builder->build();

        $expected = [
            'description' => 'Sucesso',
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_correctly_builds_a_generic_content_response()
    {
        $builder = new ResponseBuilder;
        $schema = ['type' => 'string'];

        $builder->description('Retorno em XML')
            ->content('application/xml', $schema); // <-- Usando o método genérico

        $result = $builder->build();

        $expected = [
            'description' => 'Retorno em XML',
            'content' => [
                'application/xml' => [
                    'schema' => $schema,
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_correctly_adds_headers()
    {
        $builder = new ResponseBuilder;
        $headerDef = [
            'description' => 'Limite de requisições',
            'schema' => ['type' => 'integer'],
        ];

        $builder->description('Muitas requisições')
            ->header('X-Rate-Limit', $headerDef);

        $result = $builder->build();

        $expected = [
            'description' => 'Muitas requisições',
            'headers' => [
                'X-Rate-Limit' => $headerDef,
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
