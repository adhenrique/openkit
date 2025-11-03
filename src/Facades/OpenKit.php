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