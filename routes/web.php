<?php

Route::group(
    [
        'prefix' => config('laravolt.metabase.route.prefix'),
        'as' => 'metabase::',
        'middleware' => config('laravolt.metabase.route.middleware'),
    ],
    function () {
        Route::resource('embed', \Laravolt\Metabase\Controllers\EmbedController::class)->only('show');
    }
);
