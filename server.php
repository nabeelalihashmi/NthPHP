<?php


use Framework\Routing\AutoRouteCollector;
use FastRoute\RouteCollector;
use Framework\HTTP\Response;
use RedBeanPHP\R;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response as SwooleResponse;

define('DIR', __DIR__);


require __DIR__ . '/vendor/autoload.php';

$publicDir = DIR . '/public';
$config = require 'app/config.php';

R::setup($config['database']['dsn'], $config['database']['username'], $config['database']['password']);

$active_controllers = $config['active_controllers'];
$notFoundHandler = $config['not_found'];
$methodNotAllowedHandler = $config['method_not_allowed'];
$cacheEnabled = $config['routes_cache_enabled'];
$routeCacheFile = $config['routes_cache_file'];
$routeCollectionCacheFile = $config['route_collection_cache_file'];
$automaticRoutes = $config['automatic_routes'] ? DIR . '/app/Views/_pages' : false;
$swooleStatic = $config['swoole_static'] ?? 'public';

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

$server = new Server("127.0.0.1", 9501);

$server->on("request", function (Request $req, SwooleResponse $res) use ($dispatcher, $notFoundHandler, $methodNotAllowedHandler, $swooleStatic) {
    $httpMethod = $req->server['request_method'];
    $uri = $req->server['request_uri'];

    if (preg_match($swooleStatic, $uri)) {
        $res->sendfile(DIR . '/' . $uri);
        return;
    }


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
                handleResponse(($handler[0])(), $res);
                return;
            }

            $vars = $routeInfo[2];

            [$controller, $method, $middlewares] = $handler;
            if (isset($middlewares) && is_array($middlewares)) {
                foreach ($middlewares as $middleware) {
                    $middlewareInstance = new $middleware();
                    $response = call_user_func_array([$middlewareInstance, 'handle'], array_values($vars));
                    if ($response !== true) {
                        handleResponse($response, $res);
                        return;
                    }
                }
            }

            $controllerInstance = new $controller();
            $response = call_user_func_array([$controllerInstance, $method], array_values($vars));
            if ($response) {
                handleResponse($response, $res);
            }

            break;
    }
});

$server->start();

function handleResponse($response, SwooleResponse $swooleResponse) {
    if ($response !== null) {
        if (is_string($response)) {
            $swooleResponse->end($response);
        } elseif ($response instanceof Response) {
            $response->send($swooleResponse);
        }
    }
}
