<?php

namespace OpenKit;

use OpenKit\Builders\OperationBuilder;

class OpenKitBuilder
{
    protected array $tags = [];
    protected array $paths = [];
    protected array $securitySchemes = [];

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

    public function defineSecurityScheme(string $name, array $definition): self
    {
        $this->securitySchemes[$name] = $definition;
        return $this;
    }

    public function defineBearerAuth(string $name = 'bearerAuth', string $bearerFormat = 'JWT'): self
    {
        $this->securitySchemes[$name] = [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => $bearerFormat,
        ];
        return $this;
    }

    public function defineApiKeyHeader(string $name = 'apiKey', string $headerName = 'X-API-KEY'): self
    {
        $this->securitySchemes[$name] = [
            'type' => 'apiKey',
            'in' => 'header',
            'name' => $headerName,
        ];
        return $this;
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

        if (!empty($this->securitySchemes)) {
            $spec['components']['securitySchemes'] = $this->securitySchemes;
        }

        foreach ($this->paths as $uri => $methods) {
            foreach ($methods as $method => $operationBuilder) {
                // Chama o método build() de cada builder
                $spec['paths'][$uri][$method] = $operationBuilder->build();
            }
        }

        return $spec;
    }
}