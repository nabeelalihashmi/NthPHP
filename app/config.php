<?php

return [

    'app' => [
        'active_controllers' => 'auto',
        'app_key' => 'your_key',
        'base_url' => 'http://localhost/NthPHP',
        'app_name' => 'NthPHP',
    ],

    'routing' => [
        'not_found' => [App\Controllers\HomeController::class, 'notFound'],
        'method_not_allowed' => [App\Controllers\HomeController::class, 'methodNotAllowed'],
        'routes_cache_enabled' => false,
        'routes_cache_file' => DIR . '/cache/routes/routes.php',
        'routes_collection_cache_file' => DIR . '/cache/routes/collector.php',
        'automatic_routes' => ['_pages', '_auth/forms_auto'],
    ],

    'blade' => [
        'pipes' => true,
        'mode'  => 0,
        'comment_mode' => 0
    ],


    'database' => [
        'dsn' => 'mysql:host=localhost;dbname=test',
        'username' => 'root',
        'password' => '',
        'freeze' => false
    ],

    'server' => [
        'swoole_static' => '/(partytown|.well-known|public|favicon.ico|sitemap.xml|sitemap.min.xml|robots.txt|BingSiteAuth.xml|ads.txt)($|\/)/',
        'swoole_host' => '127.0.0.1',
        'swoole_port' => 9501
    ],

    'smtp' => [
        'host' => 'smtp.host.io',
        'port' => 0,
        'username' => 'user@host.com',
        'password' => 'your_password',
        'sender' => 'John Doe',
        'security' => 'tls'
    ],
];
