<?php

namespace Laravolt\Metabase;

use Illuminate\Support\Facades\Blade;
use Laravolt\Support\Base\BaseServiceProvider;

class MetabaseServiceProvider extends BaseServiceProvider
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'metabase';
    }

    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Blade::component('metabase', MetabaseComponent::class);
    }
}
