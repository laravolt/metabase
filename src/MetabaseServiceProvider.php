<?php

namespace Laravolt\Metabase;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MetabaseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $viewPath = __DIR__.'/../resources/views';
        $configPath = __DIR__.'/../config/metabase.php';
        
        $this->loadViewsFrom($viewPath, 'metabase');
        
        // Publish views
        $this->publishes([
            $viewPath => resource_path('views/vendor/metabase')
        ], 'metabase-views');
        
        // Publish config
        $this->publishes([
            $configPath => config_path('metabase.php')
        ], 'metabase-config');

        // Load routes if enabled
        /** @var mixed $routeEnabled */
        $routeEnabled = config('metabase.route.enabled', true);
        if ($routeEnabled) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        Blade::component('metabase', MetabaseComponent::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/metabase.php', 'metabase');
    }
}
