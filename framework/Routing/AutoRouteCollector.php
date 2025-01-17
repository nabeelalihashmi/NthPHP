<?php

namespace Framework\Routing;

use ReflectionMethod;
use ReflectionClass;
use Framework\Attributes\Route;
use Framework\Attributes\Crud;
use Framework\Classes\Config;

class AutoRouteCollector {

    public function collectRoutes($controllers) {
        $routes = [];

        if ($controllers === 'auto') {
            $controllers = $this->findControllers();
        }

        foreach ($controllers as $controller) {
            $controllerReflection = new ReflectionClass($controller);

            // Check for Crud attribute
            $crudAttribute = $controllerReflection->getAttributes(Crud::class)[0] ?? null;
            if ($crudAttribute) {
                $crud = $crudAttribute->newInstance();
                $routes = array_merge($routes, $this->generateCrudRoutes($crud, $controller));
            }

            // Collect routes from the Route attributes in the controller methods
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

        // Collect routes from Blade views
        $viewsDirectories = Config::get('routing.automatic_routes');
        if (!empty($viewsDirectories)) {
            foreach($viewsDirectories as $viewsDirectory) {
                $bladeRoutes = $this->collectRoutesFromBladeViews($viewsDirectory, $routes);
                $routes = array_merge($routes, $bladeRoutes);
            }
        }

        return $routes;
    }

    private function generateCrudRoutes(Crud $crud, $controller) {
        $routes = [];
        $path = $crud->path;

        foreach ($crud->enabledMethods as $method) {
            $middleware = $crud->middlewares[$method] ?? [];

            switch ($method) {
                case 'index':
                    $routes[] = [
                        'method' => 'GET',
                        'path' => $path,
                        'handler' => [$controller, 'index'],
                        'middleware' => $middleware
                    ];
                    break;
                case 'show':
                    $routes[] = [
                        'method' => 'GET',
                        'path' => $path . '/{id}',
                        'handler' => [$controller, 'show'],
                        'middleware' => $middleware
                    ];
                    break;
                case 'create':
                    $routes[] = [
                        'method' => 'POST',
                        'path' => $path,
                        'handler' => [$controller, 'create'],
                        'middleware' => $middleware
                    ];
                    break;
                case 'update':
                    $routes[] = [
                        'method' => 'PUT',
                        'path' => $path . '/{id}',
                        'handler' => [$controller, 'update'],
                        'middleware' => $middleware
                    ];
                    break;
                case 'delete':
                    $routes[] = [
                        'method' => 'DELETE',
                        'path' => $path . '/{id}',
                        'handler' => [$controller, 'delete'],
                        'middleware' => $middleware
                    ];
                    break;
            }
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
    
        $base = str_replace('/', '.', $viewsDirectory); 

        $viewsDirectory = DIR . '/app/Views/' . $viewsDirectory;
    
        $files = $this->getBladeFiles($viewsDirectory);
        
        foreach ($files as $file) {
            $relativePath = str_replace([$viewsDirectory . '/', '.blade.php'], '', $file);
            
            $routePath = '/' . strtolower(str_replace(['\\', '/'], '/', $relativePath));
            
            $routePath = preg_replace('#/(index)$#', '', $routePath) ?: '/';

            foreach ($controllerRoutes as $controllerRoute) {
                $is_get = false;
                if (is_string($controllerRoute['method'])) {
                    $is_get = $controllerRoute['method'] == 'GET';
                } else {
                    $is_get = in_array('GET', $controllerRoute['method']);
                }
                if ($controllerRoute['path'] == $routePath && $is_get) {
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
