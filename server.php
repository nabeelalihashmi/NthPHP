<?php


use Framework\Routing\AutoRouteCollector;
use FastRoute\RouteCollector;
use Framework\Classes\Config;
use Framework\HTTP\Response;
use RedBeanPHP\R;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response as SwooleResponse;

define('DIR', __DIR__);


require __DIR__ . '/vendor/autoload.php';

define('REDBEAN_MODEL_PREFIX', '\\App\\Models\\');


$publicDir = DIR . '/public';

Config::load('app/config.php');
define('BASEURL', Config::get('app.base_url'));
define('APPNAME', Config::get('app.app_name'));


$active_controllers         = Config::get('app.active_controllers');
$notFoundHandler            = Config::get('routing.not_found');
$methodNotAllowedHandler    = Config::get('routing.method_not_allowed');
$cacheEnabled               = Config::get('routing.routes_cache_enabled');
$routeCacheFile             = Config::get('routing.routes_cache_file');
$routeCollectionCacheFile   = Config::get('routing.routes_collection_cache_file');
$automaticRoutes            = Config::get('routing.automatic_routes');
$host                       = Config::get('database.host');
$name                       = Config::get('database.name');
$username                   = Config::get('database.username');
$password                   = Config::get('database.password');
$freeze                     = Config::get('database.freeze');
$swooleStatic               = Config::get('server.swoole_static') ?? 'public';
$swooleHost                 = Config::get('server.swoole_host') ?? '127.0.0.1';
$swoolePort                 = Config::get('server.swoole_port') ?? 9501;

$dsn = "mysql:host={$host};dbname={$name}";
R::setup($dsn, $username, $password);

if ($freeze) {
    R::freeze(true);
}

include DIR . '/app/boot.php';

$dispatcher = FastRoute\cachedDispatcher(function (RouteCollector $r) use ($cacheEnabled, $routeCollectionCacheFile, $active_controllers, $automaticRoutes) {
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
    foreach ($routes as $route) {
        $r->addRoute($route['method'], $route['path'], [...$route['handler'], $route['middleware'] ?? []]);
    }
}, [
    'cacheFile' => $cacheEnabled ? $routeCacheFile : false,
    'cacheDisabled' => !$cacheEnabled,
]);

$server = new Server($swooleHost, $swoolePort);
$server->on("Start", function (Server $server) {
    echo "Swoole http server is started at http://{$server->host}:{$server->port}\n";
});

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
            if (is_string($handler[0]) && strpos($handler[0], '@render') === 0) {
                $handler[0] = function () use ($handler) {
                    return \Framework\Classes\Blade::view(str_replace('@render:', '', $handler[0]));
                };
            }

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


function cfg($key) {
    return Config::get($key);
}

function baseurl($url) {
    return BASEURL . '/' . $url;
}
