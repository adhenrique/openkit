<?php

namespace OpenKit\Tests\Unit;

use OpenKit\Builders\RequestBodyBuilder;
use OpenKit\Tests\TestCase;

class RequestBodyBuilderTest extends TestCase
{
    public function test_it_throws_an_exception_if_content_is_missing_on_build()
    {
        // O OpenAPI exige que um RequestBody tenha 'content'
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('deve ter pelo menos um tipo de "content"');

        $builder = new RequestBodyBuilder;
        $builder->description('Isto vai falhar');
        $builder->build(); // <--- Deve falhar aqui
    }

    public function test_it_builds_a_basic_json_content_body()
    {
        $builder = new RequestBodyBuilder;
        $schema = ['type' => 'string'];

        $builder->description('Payload JSON')
            ->required(true)
            ->jsonContent($schema);

        $result = $builder->build();

        $expected = [
            'description' => 'Payload JSON',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_defaults_required_to_false()
    {
        $builder = new RequestBodyBuilder;
        $schema = ['type' => 'string'];

        $builder->jsonContent($schema); // Não chamamos ->required()
        $result = $builder->build();

        // Verifica se 'required' é false ou está ausente (o array_filter pode remover se for null/false)
        $this->assertArrayHasKey('required', $result);
        $this->assertFalse($result['required']);
    }

    public function test_it_correctly_builds_a_multipart_form_data_body()
    {
        $builder = new RequestBodyBuilder;
        $properties = [
            'file' => ['type' => 'string', 'format' => 'binary'],
            'caption' => ['type' => 'string'],
        ];

        $builder->description('Upload de arquivo')
            ->multipartFormData($properties); // <-- Testando o novo helper

        $result = $builder->build();

        $expected = [
            'description' => 'Upload de arquivo',
            'required' => false,
            'content' => [
                'multipart/form-data' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => $properties,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_does_not_include_empty_description()
    {
        $builder = new RequestBodyBuilder;
        $builder->jsonContent(['type' => 'string']);
        $result = $builder->build();

        $this->assertArrayNotHasKey('description', $result);
    }
}
