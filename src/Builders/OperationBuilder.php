<?php

namespace OpenKit\Builders;

class OperationBuilder
{
    protected array $data = [
        'tags' => null,
        'summary' => null,
        'description' => null,
        'operationId' => null,
        'deprecated' => null,
        'parameters' => null,
        'requestBody' => null,
        'responses' => null, // Respostas são obrigatórias, mas validadas no OpenKitBuilder
        'security' => null,
    ];

    public function tag(string $tagName): self
    {
        if (is_null($this->data['tags'])) {
            $this->data['tags'] = [];
        }
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

    public function operationId(string $operationId): self
    {
        $this->data['operationId'] = $operationId;

        return $this;
    }

    public function deprecated(bool $deprecated = true): self
    {
        $this->data['deprecated'] = $deprecated;

        return $this;
    }

    public function withParameter(string $name, string $in, callable $callback): self
    {
        if (is_null($this->data['parameters'])) {
            $this->data['parameters'] = [];
        }

        $paramBuilder = new ParameterBuilder($name, $in);

        $callback($paramBuilder);

        $this->data['parameters'][] = $paramBuilder->build();

        return $this;
    }

    public function withResponse(int $status, callable $callback): self
    {
        if (is_null($this->data['responses'])) {
            $this->data['responses'] = [];
        }

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
        if (is_null($this->data['security'])) {
            $this->data['security'] = [];
        }

        $this->data['security'][] = [
            $schemeName => $scopes,
        ];

        return $this;
    }

    public function build(): array
    {
        return array_filter($this->data, fn ($value) => ! is_null($value));
    }
}
