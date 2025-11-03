<?php

namespace OpenKit\Builders;

class ParameterBuilder
{
    protected array $data = [
        'name' => '',
        'in' => '',
        'description' => '',
        'required' => false,
        'schema' => [],
    ];

    public function __construct(string $name, string $in)
    {
        $this->data['name'] = $name;
        $this->data['in'] = $in;

        // Regra do OpenAPI: Parâmetros de 'path' SÃO sempre obrigatórios.
        if ($in === 'path') {
            $this->data['required'] = true;
        }
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function required(bool $required = true): self
    {
        if ($this->data['in'] !== 'path') {
            $this->data['required'] = $required;
        }

        return $this;
    }

    public function schema(array $schema): self
    {
        $this->data['schema'] = $schema;

        return $this;
    }

    public function deprecated(bool $deprecated = true): self
    {
        $this->data['deprecated'] = $deprecated;

        return $this;
    }

    public function example(mixed $example): self
    {
        $this->data['example'] = $example;

        return $this;
    }

    public function build(): array
    {
        // Garante que o schema não fique vazio se não for definido
        if (empty($this->data['schema'])) {
            // Define um schema padrão 'string' se nenhum for fornecido
            $this->data['schema'] = ['type' => 'string'];
        }

        return array_filter($this->data, fn ($value) => ! is_null($value));
    }
}
