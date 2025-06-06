<?php

use Framework\Classes\Blade;

return [

    'app' => [
        'active_controllers' => 'auto',
        'app_key' => 'your_key',
        'base_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
        'app_name' => 'NthPHP',
        'debugger' => true
    ],

    'routing' => [
        'not_found' => '@render:notfound' ,//[App\Controllers\HomeController::class, 'notFound'],
        'method_not_allowed' => '@render:notfound', //[App\Controllers\HomeController::class, 'methodNotAllowed'],
        'routes_cache_enabled' => false,
        'routes_cache_file' => DIR . '/cache/routes/routes.php',
        'routes_collection_cache_file' => DIR . '/cache/routes/collector.php',
        'automatic_routes' => ['_pages', '_auth/forms_auto', '_demos'],
    ],

    'blade' => [
        'pipes' => true,
        'mode'  => Blade::MODE_SLOW,
        'comment_mode' => 0,
        'optimize' =>  true,
    ],

    'auth' => [
        'verify' => false
    ],


    'database' => [
        'host' => 'localhost:3306',
        'name' => 'nthphp',
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
