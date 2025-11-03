<?php

namespace OpenKit\Builders;

class RequestBodyBuilder
{
    protected array $data = [
        'description' => '',
        'required' => false,
        'content' => [],
    ];

    public function __construct()
    {
        //
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->data['required'] = $required;
        return $this;
    }

    public function jsonContent(array $schema): self
    {
        $this->data['content']['application/json'] = [
            'schema' => $schema
        ];
        return $this;
    }

    public function content(string $mimeType, array $schema): self
    {
        $this->data['content'][$mimeType] = [
            'schema' => $schema
        ];
        return $this;
    }

    public function build(): array
    {
        // Em um RequestBody, o 'content' é obrigatório.
        if (empty($this->data['content'])) {
            throw new \InvalidArgumentException(
                'O corpo da requisição (requestBody) deve ter pelo menos um tipo de "content" (ex: jsonContent).'
            );
        }

        return array_filter($this->data);
    }
}