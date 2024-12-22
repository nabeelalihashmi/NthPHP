<?php

use App\Controllers\HomeController;

return [
    'active_controllers' => [
        HomeController::class,
    ],
    'not_found' => [
        HomeController::class, 'notFound'
    ],

    'method_not_allowed' => [
        HomeController::class, 'methodNotAllowed'
    ],

    'routes_cache_enabled' => false,
    'routes_cache_file' => __DIR__ . '/../cache/routes.cache',
    'route_collection_cache_file' => __DIR__ . '/../cache/collector.cache'
];