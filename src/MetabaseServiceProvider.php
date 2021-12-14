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
        Blade::component('metabase', MetabaseComponent::class);
    }
}
