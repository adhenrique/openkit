<?php

namespace OpenKit;

use OpenKit\Builders\OperationBuilder;

class OpenKitBuilder
{
    protected array $tags = [];
    protected array $paths = [];

    public function defineTag(string $name, string $description): self
    {
        $this->tags[] = ['name' => $name, 'description' => $description];
        return $this;
    }

    public function path(string $uri, string $method): OperationBuilder
    {
        // Garante que a URI segue o padrão OpenAPI (ex: /users/{id})
        $openapiUri = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '{$1}', $uri);

        $operationBuilder = new OperationBuilder($uri, $method);

        $this->paths[$openapiUri][strtolower($method)] = $operationBuilder;

        return $operationBuilder;
    }

    public function generate(): array
    {
        $spec = [
            'openapi' => '3.0.3',
            'info' => config('openkit.info'),
            'servers' => [
                ['url' => url('/'), 'description' => 'Servidor Principal']
            ],
            'tags' => $this->tags,
            'paths' => [],
        ];

        foreach ($this->paths as $uri => $methods) {
            foreach ($methods as $method => $operationBuilder) {
                // Chama o método build() de cada builder
                $spec['paths'][$uri][$method] = $operationBuilder->build();
            }
        }

        return $spec;
    }
}