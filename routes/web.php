<?php

use Illuminate\Support\Facades\Route;
use Laravolt\Metabase\Controllers\EmbedController;

/** @var mixed $prefix */
$prefix = config('metabase.route.prefix');
/** @var mixed $middleware */
$middleware = config('metabase.route.middleware');

Route::group(
    [
        'prefix' => $prefix,
        'as' => 'metabase::',
        'middleware' => $middleware,
    ],
    function () {
        Route::resource('embed', EmbedController::class)->only('show');
    }
);
