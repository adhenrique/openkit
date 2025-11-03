<?php

namespace OpenKit\Tests\Unit;

use OpenKit\Builders\ParameterBuilder;
use PHPUnit\Framework\TestCase;

class ParameterBuilderTest extends TestCase
{
    public function test_it_builds_a_basic_parameter()
    {
        $builder = new ParameterBuilder('id', 'query');
        $result = $builder->build();

        $expected = [
            'name' => 'id',
            'in' => 'query',
            'required' => false,
            'schema' => ['type' => 'string'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_correctly_builds_a_complex_parameter()
    {
        $builder = new ParameterBuilder('status', 'query');

        $builder->description('Descrição do status')
            ->required()
            ->deprecated()
            ->example('active')
            ->schema([
                'type' => 'string',
                'enum' => ['active', 'inactive'],
            ]);

        $result = $builder->build();

        $expected = [
            'name' => 'status',
            'in' => 'query',
            'description' => 'Descrição do status',
            'required' => true,
            'deprecated' => true,
            'example' => 'active',
            'schema' => [
                'type' => 'string',
                'enum' => ['active', 'inactive'],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_it_forces_required_for_path_parameters()
    {
        // 1. Tenta definir como 'false' (deve ser ignorado)
        $builder = new ParameterBuilder('user_id', 'path');
        $builder->required(false); // Tenta sobrescrever
        $result = $builder->build();

        $this->assertTrue(
            $result['required'],
            "Parâmetros 'path' devem ser sempre 'required' por padrão."
        );

        // 2. O padrão já deve ser 'true'
        $builderDefault = new ParameterBuilder('post_id', 'path');
        $resultDefault = $builderDefault->build();

        $this->assertTrue(
            $resultDefault['required'],
            "Parâmetros 'path' devem ser 'required' por padrão."
        );
    }

    public function test_it_does_not_include_empty_values_in_build()
    {
        // Apenas 'name' e 'in' são definidos
        $builder = new ParameterBuilder('page', 'query');
        $result = $builder->build();

        // Não deve conter 'description', 'example', 'deprecated', etc.
        $this->assertArrayNotHasKey('description', $result);
        $this->assertArrayNotHasKey('example', $result);
        $this->assertArrayNotHasKey('deprecated', $result);

        // 'required' e 'schema' têm valores padrão, então devem existir
        $this->assertArrayHasKey('required', $result);
        $this->assertArrayHasKey('schema', $result);
    }
}
