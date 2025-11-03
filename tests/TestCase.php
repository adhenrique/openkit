<?php

namespace OpenKit\Tests;

use OpenKit\Facades\OpenKit;
use OpenKit\OpenKitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('openkit.info', [
            'title' => 'API de Teste',
            'version' => '1.0.0',
        ]);
        $app['config']->set('openkit.path', 'openkit-test-docs');
        $app['config']->set('openkit.json_file_name', 'api-test.json');
        $app['config']->set('openkit.ui', [
            'title' => 'Minha API de Teste (UI)',
            'cdn_url' => 'https://fake-cdn-url.com',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            OpenKitServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'OpenKit' => OpenKit::class,
        ];
    }
}
