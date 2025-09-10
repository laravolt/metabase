<?php

use Illuminate\Support\Facades\Route;
use Laravolt\Metabase\Controllers\EmbedController;

Route::group(
    [
        'prefix' => config('metabase.route.prefix'),
        'as' => 'metabase::',
        'middleware' => config('metabase.route.middleware'),
    ],
    function () {
        Route::resource('embed', EmbedController::class)->only('show');
    }
);
