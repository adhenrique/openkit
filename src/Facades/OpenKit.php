<?php

namespace OpenKit\Facades;

use Illuminate\Support\Facades\Facade;
use OpenKit\Builders\OperationBuilder;
use OpenKit\OpenKitBuilder;

/**
 * @see OpenKitBuilder
 *
 * @method static OperationBuilder path(string $uri, string $method)
 * @method static OpenKitBuilder defineTag(string $name, string $description)
 * @method static OpenKitBuilder defineBearerAuth(string $name = 'bearerAuth', string $bearerFormat = 'JWT')
 * @method static OpenKitBuilder defineApiKeyHeader(string $name = 'apiKey', string $headerName = 'X-API-KEY')
 * @method static OpenKitBuilder defineSchema(string $name, array $schema)
 * @method static array generate()
 */
class OpenKit extends Facade
{
    /**
     * Obtém o nome do componente registrado no Service Container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'openkit.builder';
    }
}