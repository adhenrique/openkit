<?php

namespace OpenKit\Tests;

use OpenKit\OpenKitServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            OpenKitServiceProvider::class,
        ];
    }
}