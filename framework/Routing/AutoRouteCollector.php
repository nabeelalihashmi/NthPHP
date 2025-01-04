<?php

namespace Framework\Routing;

use ReflectionMethod;
use Framework\Attributes\Route;
use Framework\Classes\Config;

class AutoRouteCollector {

    public function collectRoutes($controllers, string $viewsDirectory) {
        $routes = [];

        if ($controllers === 'auto') {
            $controllers = $this->findControllers();
        }

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

    private function findControllers(): array {
        $controllers = [];
        $directory = DIR . '/app/Controllers';
        
        $files = $this->getPhpFiles($directory);

        foreach ($files as $file) {
            $relativePath = str_replace([$directory . '/', '.php'], '', $file);
            $className = str_replace('/', '\\', 'App\\Controllers\\' . $relativePath);

            if (class_exists($className)) {
                $controllers[] = $className;
            }
        }

        return $controllers;
    }

    private function getPhpFiles(string $directory): array {
        $files = [];

        foreach (glob($directory . '/*') as $file) {
            if (is_dir($file)) {
                $files = array_merge($files, $this->getPhpFiles($file));
            } elseif (is_file($file) && str_ends_with($file, '.php')) {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function collectRoutesFromBladeViews(string $viewsDirectory, $controllerRoutes) {
        $routes = [];
    
        $viewsDirectory = DIR . '/app/Views/' . $viewsDirectory;
    
        $files = $this->getBladeFiles($viewsDirectory);
    
        $base = Config::get('routing.automatic_routes');
    
        foreach ($files as $file) {
            $relativePath = str_replace([$viewsDirectory . '/', '.blade.php'], '', $file);
            
            $routePath = '/' . strtolower(str_replace(['\\', '/'], '/', $relativePath));
            
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
