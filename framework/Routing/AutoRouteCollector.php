<?php

namespace Framework\Routing;

use ReflectionMethod;
use Framework\Attributes\Route;

class AutoRouteCollector
{
    public function collectRoutes(array $controllers)
    {
        $routes = [];

        foreach ($controllers as $controller) {
            // Reflect on the controller class
            $controllerReflection = new \ReflectionClass($controller);

            // Loop through each method of the controller
            foreach ($controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                // Check if the method has a Route attribute
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

        return $routes;
    }
}
