<?php

namespace OpenKit\Builders;

class ResponseBuilder
{
    protected array $data = [
        'description' => '',
        'content' => [],
        'headers' => [],
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

    public function jsonContent(array $schema): self
    {
        $this->data['content']['application/json'] = [
            'schema' => $schema,
        ];

        return $this;
    }

    public function content(string $mimeType, array $schema, array $examples = []): self
    {
        $mediaTypeObject = ['schema' => $schema];

        if (! empty($examples)) {
            $mediaTypeObject['examples'] = $examples;
        }

        $this->data['content'][$mimeType] = $mediaTypeObject;

        return $this;
    }

    public function header(string $name, array $headerDefinition): self
    {
        $this->data['headers'][$name] = $headerDefinition;

        return $this;
    }

    public function build(): array
    {
        if (empty($this->data['description'])) {
            throw new \InvalidArgumentException(
                'A descrição da resposta (description) é obrigatória para todas as respostas.'
            );
        }

        return array_filter($this->data);
    }
}
