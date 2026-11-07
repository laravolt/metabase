<?php

/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        // Off by default: the route signs an embed for any dashboard id the caller names.
        // Enable it only behind middleware that checks the viewer may see those dashboards.
        'enabled' => env('METABASE_ROUTE_ENABLED', false),
        'middleware' => ['web', 'auth'],
        'prefix' => 'metabase',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.centered',
    ],
];
