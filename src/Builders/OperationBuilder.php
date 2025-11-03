<?php

namespace OpenKit\Builders;

class OperationBuilder
{
    protected array $data = [
        'tags' => [],
        'summary' => '',
        'description' => '',
        'parameters' => [],
        'responses' => [],
        'requestBody' => null,
        'security' => [],
    ];

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
        $responseBuilder = new ResponseBuilder;
        $callback($responseBuilder);
        $this->data['responses'][(string) $status] = $responseBuilder->build();

        return $this;
    }

    public function withRequestBody(callable $callback): self
    {
        $requestBodyBuilder = new RequestBodyBuilder;
        $callback($requestBodyBuilder);
        $this->data['requestBody'] = $requestBodyBuilder->build();

        return $this;
    }

    public function securedBy(string $schemeName, array $scopes = []): self
    {
        $this->data['security'][] = [
            $schemeName => $scopes,
        ];

        return $this;
    }

    public function build(): array
    {
        return array_filter($this->data);
    }
}
