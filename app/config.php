<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;

return [
    'active_controllers' => [
        HomeController::class,
        UserController::class
    ],
    'not_found' => [
        HomeController::class, 'notFound'
    ],

    'method_not_allowed' => [
        HomeController::class, 'methodNotAllowed'
    ],

    'routes_cache_enabled' => false,
    'routes_cache_file' => __DIR__ . '/../cache/routes/routes.cache',
    'route_collection_cache_file' => __DIR__ . '/../cache/routes/collector.cache',
    'automatic_routes' => true,


    'database' => [
        'dsn' => 'mysql:host=localhost;dbname=test',
        'username' => 'root',
        'password' => ''
    ],

    'swoole_static' => '/(partytown|.well-known|public|favicon.ico|sitemap.xml|sitemap.min.xml|robots.txt|BingSiteAuth.xml|ads.txt)($|\/)/',
];