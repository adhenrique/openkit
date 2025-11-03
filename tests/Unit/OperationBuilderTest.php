<?php

namespace OpenKit\Tests\Unit;

use OpenKit\Builders\OperationBuilder;
use OpenKit\Builders\ParameterBuilder;
use OpenKit\Builders\RequestBodyBuilder;
use OpenKit\Builders\ResponseBuilder;
use OpenKit\Tests\TestCase;

class OperationBuilderTest extends TestCase
{
    public function test_it_builds_a_minimal_operation_and_cleans_empty_values()
    {
        $builder = new OperationBuilder;
        $builder->summary('Test Summary'); // Define apenas 1 propriedade

        $result = $builder->build();

        $expected = [
            'summary' => 'Test Summary',
        ];

        // Garante que chaves 'null' (tags, parameters, etc.) foram removidas
        $this->assertEquals($expected, $result);
        $this->assertArrayNotHasKey('tags', $result);
        $this->assertArrayNotHasKey('parameters', $result);
        $this->assertArrayNotHasKey('responses', $result);
    }

    public function test_it_builds_with_all_simple_properties()
    {
        $builder = new OperationBuilder;

        $builder->tag('Users')
            ->tag('Auth')
            ->summary('Full Operation')
            ->description('A description')
            ->operationId('getUserOp')
            ->deprecated(true);

        $result = $builder->build();

        $expected = [
            'tags' => ['Users', 'Auth'],
            'summary' => 'Full Operation',
            'description' => 'A description',
            'operationId' => 'getUserOp',
            'deprecated' => true,
        ];

        // Compara apenas as chaves esperadas (ignora 'parameters', etc.)
        $this->assertEquals($expected, $result);
    }

    public function test_it_builds_with_parameters()
    {
        $builder = new OperationBuilder;

        $builder->withParameter('id', 'path', function (ParameterBuilder $param) {
            $param->description('User ID');
        });

        $result = $builder->build();

        // Confia que o ParameterBuilder foi testado e fez seu trabalho
        $this->assertCount(1, $result['parameters']);
        $this->assertEquals('id', $result['parameters'][0]['name']);
        $this->assertEquals('path', $result['parameters'][0]['in']);
        $this->assertTrue($result['parameters'][0]['required']); // Lógica do ParameterBuilder
    }

    public function test_it_builds_with_responses()
    {
        $builder = new OperationBuilder;

        $builder->withResponse(200, function (ResponseBuilder $res) {
            $res->description('Success');
        });
        $builder->withResponse(404, function (ResponseBuilder $res) {
            $res->description('Not Found');
        });

        $result = $builder->build();

        $this->assertArrayHasKey('200', $result['responses']);
        $this->assertArrayHasKey('404', $result['responses']);
        $this->assertEquals('Success', $result['responses']['200']['description']);
    }

    public function test_it_builds_with_request_body()
    {
        $builder = new OperationBuilder;

        $builder->withRequestBody(function (RequestBodyBuilder $body) {
            $body->jsonContent(['type' => 'string']);
        });

        $result = $builder->build();

        $this->assertArrayHasKey('requestBody', $result);
        $this->assertArrayHasKey('content', $result['requestBody']);
        $this->assertArrayHasKey('application/json', $result['requestBody']['content']);
    }

    public function test_it_builds_with_security()
    {
        $builder = new OperationBuilder;

        $builder->securedBy('bearerAuth')
            ->securedBy('oauth2', ['read:users']);

        $result = $builder->build();

        $expected = [
            ['bearerAuth' => []],
            ['oauth2' => ['read:users']],
        ];

        $this->assertEquals($expected, $result['security']);
    }
}
