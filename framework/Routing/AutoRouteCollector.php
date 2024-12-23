<?php

namespace Framework\Routing;

use ReflectionMethod;
use Framework\Attributes\Route;
use Framework\Classes\Blade;

class AutoRouteCollector
{
    public function collectRoutes(array $controllers, string $viewsDirectory)
    {
        $routes = [];

        foreach ($controllers as $controller) {
            $controllerReflection = new \ReflectionClass($controller);

            foreach ($controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $routeAttributes = $method->getAttributes(Route::class);

                foreach ($routeAttributes as $attribute) {
                    $routeInstance = $attribute->newInstance();

                    $routes[] = [
                        'method' => $routeInstance->method,
                        'path' => $routeInstance->path,
                        'handler' => [$controller, $method->getName()],
                        'middleware' => $routeInstance->middleware ?? []
                    ];
                }
            }
        }

        if ($viewsDirectory) {
            $bladeRoutes = $this->collectRoutesFromBladeViews($viewsDirectory);
            $routes = array_merge($routes, $bladeRoutes);
        }

        return $routes;
    }

    private function collectRoutesFromBladeViews(string $viewsDirectory)
    {
        $routes = [];

        $files = glob($viewsDirectory . '/*.blade.php');

        foreach ($files as $file) {
            $fileName = basename($file, '.blade.php');
            $routePath = '/' . strtolower(str_replace('_', '/', $fileName));
            $routes[] = [
                'method' => 'GET',
                'path' => $routePath,
                'handler' => [function () use ($fileName) {
                    echo Blade::run( '_pages.' . str_replace('/', '.', $fileName));
                }]
            ];
        }

        return $routes;
    }
}
