<?php

namespace Laravolt\Metabase;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MetabaseServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
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
        if (config('metabase.route.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        Blade::component('metabase', MetabaseComponent::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/metabase.php', 'metabase');
    }
}
