<?php

/*
 * Set specific configuration variables here
 */
return [
    'route' => [
        'enabled' => true,
        'middleware' => ['web', 'auth'],
        'prefix' => 'metabase',
    ],
    'view' => [
        'layout' => 'laravolt::layouts.centered',
    ],
];
