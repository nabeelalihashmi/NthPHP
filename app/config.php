<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;

return [

    'app' => [
        'active_controllers' => [
            HomeController::class,
            UserController::class
        ]
    ],

    'routing' => [
        'not_found' => [HomeController::class, 'notFound'],
        'method_not_allowed' => [HomeController::class, 'methodNotAllowed'],
        'routes_cache_enabled' => false,
        'routes_cache_file' => DIR . '/cache/routes/routes.cache',
        'routes_collection_cache_file' => DIR . '/cache/routes/collector.cache',
        'automatic_routes' => '_pages',
    ],


    'database' => [
        'dsn' => 'mysql:host=localhost;dbname=test',
        'username' => 'root',
        'password' => ''
    ],

    'server' => [
        'swoole_static' => '/(partytown|.well-known|public|favicon.ico|sitemap.xml|sitemap.min.xml|robots.txt|BingSiteAuth.xml|ads.txt)($|\/)/',
        'swoole_host' => '127.0.0.1',
        'swoole_port' => 9501
    ]
];
