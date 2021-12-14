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
        $this->loadViewsFrom($viewPath, 'metabase');
        $this->publishes([$viewPath => resource_path('views/vendor/metabase')]);

        Blade::component('metabase', MetabaseComponent::class);
    }
}
