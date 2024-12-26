<?php

namespace Framework\Routing;

use ReflectionMethod;
use Framework\Attributes\Route;
use Framework\Classes\Config;

class AutoRouteCollector {

    public function collectRoutes(array $controllers, string $viewsDirectory) {
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
            $bladeRoutes = $this->collectRoutesFromBladeViews($viewsDirectory, $routes);
            $routes = array_merge($routes, $bladeRoutes);
        }

        return $routes;
    }

    private function collectRoutesFromBladeViews(string $viewsDirectory, $controllerRoutes) {
        $routes = [];
    
        $viewsDirectory = DIR . '/app/Views/' . $viewsDirectory;
    
        $files = $this->getBladeFiles($viewsDirectory);
    
        $base = Config::get('routing.automatic_routes');
    
        foreach ($files as $file) {
            $relativePath = str_replace([$viewsDirectory . '/', '.blade.php'], '', $file);
            
            $routePath = '/' . strtolower(str_replace(['_', '\\', '/'], '/', $relativePath));
            
            $routePath = preg_replace('#/(index)$#', '', $routePath) ?: '/';

            foreach ($controllerRoutes as $controllerRoute) {
                if ($controllerRoute['path'] === $routePath) {
                    continue 2;
                }
            }
    

            $relativePath = str_replace('/', '.', $relativePath);
            $routes[] = [
                'method' => 'GET',
                'path' => $routePath,
                'handler' => ['@render:' . $base . '.' . $relativePath],
            ];
        }
    
        return $routes;
    }
    

    private function getBladeFiles(string $directory): array {
        $files = [];

        foreach (glob($directory . '/*') as $file) {
            if (is_dir($file)) {
                $files = array_merge($files, $this->getBladeFiles($file));
            } elseif (is_file($file) && str_ends_with($file, '.blade.php')) {
                $files[] = $file;
            }
        }

        return $files;
    }
}
