<?php

namespace OpenKit;

use Illuminate\Support\ServiceProvider;
use OpenKit\Console\GenerateDocsCommand;
use OpenKit\Facades\OpenKit as OpenKitFacade;

class OpenKitServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenKitBuilder::class, function ($app) {
            return new OpenKitBuilder;
        });

        $this->app->alias(OpenKitBuilder::class, 'openkit.builder');
        $this->app->alias(OpenKitFacade::class, 'OpenKit');

        $this->mergeConfigFrom(
            __DIR__.'/../config/openkit.php', 'openkit'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'openkit');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateDocsCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/openkit.php' => config_path('openkit.php'),
        ], 'openkit-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/openkit'),
        ], 'openkit-views');
    }
}
