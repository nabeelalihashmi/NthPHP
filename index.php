<?php

use Framework\Routing\AutoRouteCollector;
use FastRoute\RouteCollector;
use Framework\Classes\Config;
use Framework\HTTP\Response;
use RedBeanPHP\R;
use Tracy\Debugger;

define('DIR', __DIR__);

require __DIR__ . '/vendor/autoload.php';

define('REDBEAN_MODEL_PREFIX', '\\App\\Models\\');

Config::load('app/config.php');
define('BASEURL', Config::get('app.base_url'));
define('APPNAME', Config::get('app.app_name'));

Debugger::enable();


$active_controllers         = Config::get('app.active_controllers');
$notFoundHandler            = Config::get('routing.not_found');
$methodNotAllowedHandler    = Config::get('routing.method_not_allowed');
$cacheEnabled               = Config::get('routing.routes_cache_enabled');
$routeCacheFile             = Config::get('routing.routes_cache_file');
$routeCollectionCacheFile   = Config::get('routing.routes_collection_cache_file');
$automaticRoutes            = Config::get('routing.automatic_routes');
$dsn                        = Config::get('database.dsn');
$username                   = Config::get('database.username');
$password                   = Config::get('database.password');
$freeze                     = Config::get('database.freeze');


R::setup($dsn, $username, $password);


if ($freeze) {
    R::freeze(true);
}

if ($cacheEnabled && file_exists($routeCollectionCacheFile)) {
    $routes = include $routeCollectionCacheFile;
} else {
    $routeCollector = new AutoRouteCollector();
    $routes = $routeCollector->collectRoutes($active_controllers, $automaticRoutes);

    if ($cacheEnabled) {
        $exportedRoutes = "<?php\n\nreturn " . var_export($routes, true) . ";\n";
        file_put_contents($routeCollectionCacheFile, $exportedRoutes);
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
        handleResponse(call_user_func([$controllerInstance, $method]));
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        [$controller, $method] = $methodNotAllowedHandler;
        $controllerInstance = new $controller();
        handleResponse(call_user_func([$controllerInstance, $method]));
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        if (is_string($handler[0]) && strpos($handler[0], '@render') === 0) {
            $handler[0] = function () use ($handler) {
                return \Framework\Classes\Blade::view(str_replace('@render:', '', $handler[0]));
            };
        }
        if (is_callable($handler[0])) {
            handleResponse(($handler[0])());
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
    $uri = str_replace([$dirname, $basename], "", $_SERVER['REQUEST_URI']);
    $uri = trim(preg_replace('~/{2,}~', '/', explode('?', $uri)[0]), '/');
    return $uri === '' ? '/' : "/{$uri}";
}


function getCurrentUrl(array $params = []) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $current_url = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = parse_url($current_url, PHP_URL_PATH);
    $query = [];
    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $query);
    }
 
    $query = array_merge($query, $params);
    $url .= '?' . http_build_query($query);
 
    return $url;
 }

 function cfg($key) {
    return Config::get($key);
 }

 function baseurl($url) {
    return BASEURL . '/' . $url;
 }