<?php

use Framework\Routing\AutoRouteCollector;
use FastRoute\RouteCollector;
use Framework\HTTP\Response;
use RedBeanPHP\R;

define('DIR', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$config = require 'app/config.php';

R::setup($config['database']['dsn'], $config['database']['username'], $config['database']['password']);

$active_controllers = $config['active_controllers'];
$notFoundHandler = $config['not_found'];
$methodNotAllowedHandler = $config['method_not_allowed'];
$cacheEnabled = $config['routes_cache_enabled'];
$routeCacheFile = $config['routes_cache_file'];
$routeCollectionCacheFile = $config['route_collection_cache_file'];
$automaticRoutes = $config['automatic_routes'] ? DIR . '/app/Views/_pages' : false;

if ($cacheEnabled && file_exists($routeCollectionCacheFile)) {
    $routes = unserialize(file_get_contents($routeCollectionCacheFile));
} else {

    $routeCollector = new AutoRouteCollector();
    $routes = $routeCollector->collectRoutes($active_controllers, $automaticRoutes);

    if ($cacheEnabled) {
        file_put_contents($routeCollectionCacheFile, serialize($routes));
    }
}


$dispatcher = FastRoute\cachedDispatcher(function (RouteCollector $r) use ($routes) {
    foreach ($routes as $route) {
        $r->addRoute($route['method'], $route['path'], [...$route['handler'], $route['middleware'] ?? []]);
    }
}, [
    'cacheFile' => $cacheEnabled ? $routeCacheFile : false,
    'cacheDisabled' => !$cacheEnabled,
]);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = makeRequestUri();


$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        [$controller, $method] = $notFoundHandler;
        $controllerInstance = new $controller();
        call_user_func([$controllerInstance, $method]);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        [$controller, $method] = $methodNotAllowedHandler;
        $controllerInstance = new $controller();
        call_user_func([$controllerInstance, $method]);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        if (is_callable($handler[0])) {
            ($handler[0])();
            return;
        }

        $vars = $routeInfo[2];

        [$controller, $method, $middlewares] = $handler;
        if (isset($middlewares) && is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $response = call_user_func_array([$middlewareInstance, 'handle'], array_values($vars));
                if ($response !== true) {
                    handleResponse($response);
                    return;
                }
            }
        }

        $controllerInstance = new $controller();
        $response = call_user_func_array([$controllerInstance, $method], array_values($vars));
        if ($response) {
            handleResponse($response);
        }

        break;
}

function handleResponse($response) {
    if ($response !== null) {
        if (is_string($response)) {
            echo $response;
        } elseif ($response instanceof Response) {
            $response->send();
        }
    }
}

function makeRequestUri() {
    $script = $_SERVER['SCRIPT_NAME'];
    $dirname = dirname($script);
    $dirname = $dirname === '/' ? '' : $dirname;
    $basename = basename($script);
    
    // Extract the URI, removing the script's directory and basename
    $uri = str_replace([$dirname, $basename], "", $_SERVER['REQUEST_URI']);
    
    // Clear multiple slashes and trim the URI
    $uri = trim(preg_replace('~/{2,}~', '/', $uri), '/');
    
    // Return the cleaned route, defaulting to '/' if empty
    return $uri === '' ? '/' : "/{$uri}";
}
