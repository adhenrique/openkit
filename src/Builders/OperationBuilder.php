<?php

namespace OpenKit\Builders;

class OperationBuilder
{
    protected array $data = [];

    public function __construct(string $uri, string $method)
    {
        $this->data = [
            'tags' => [],
            'summary' => '',
            'description' => '',
            'parameters' => [],
            'responses' => [],
        ];
    }

    public function tag(string $tagName): self
    {
        $this->data['tags'][] = $tagName;
        return $this;
    }

    public function summary(string $summary): self
    {
        $this->data['summary'] = $summary;
        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function withParameter(string $name, string $in, callable $callback): self
    {
        $paramBuilder = new ParameterBuilder($name, $in);

        $callback($paramBuilder);

        $this->data['parameters'][] = $paramBuilder->build();
        return $this;
    }

    public function withResponse(int $status, callable $callback): self
    {
        $responseBuilder = new ResponseBuilder($status);
        $callback($responseBuilder);
        $this->data['responses'][(string) $status] = $responseBuilder->build();
        return $this;
    }

    public function build(): array
    {
        return array_filter($this->data);
    }
}