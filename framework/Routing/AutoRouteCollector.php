<?php

namespace Framework\Routing;

use ReflectionMethod;
use ReflectionClass;
use Framework\Attributes\Route;
use Framework\Classes\Config;

class AutoRouteCollector {
    private static array $manualRoutes = [];

    public function collectRoutes($controllers) {
        $routes = [];

        if ($controllers === 'auto') {
            $controllers = $this->findControllers();
        }

        foreach ($controllers as $controller) {
            $controllerReflection = new ReflectionClass($controller);

            foreach ($controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $routeAttributes = $method->getAttributes(Route::class);

                foreach ($routeAttributes as $attribute) {
                    $routeInstance = $attribute->newInstance();
                    $routes[] = [
                        'method' => (array) $routeInstance->method,
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
            foreach ($viewsDirectories as $viewsDirectory) {
                $bladeRoutes = $this->collectRoutesFromBladeViews($viewsDirectory, $routes);
                $routes = array_merge($routes, $bladeRoutes);
            }
        }

        // Collect routes from /app/Routes/ directory
        $routesDir = DIR . '/app/Routes/';
        if (is_dir($routesDir)) {
            foreach (glob($routesDir . '*.php') as $routeFile) {
                $filename = pathinfo($routeFile, PATHINFO_FILENAME);
                $fileRoutes = require $routeFile;

                if (is_array($fileRoutes)) {
                    foreach ($fileRoutes as &$route) {
                        if ($filename !== 'root') {
                            $route['path'] = '/' . $filename . $route['path'];
                        }
                    }
                    $routes = array_merge($routes, $fileRoutes);
                }
            }
        }

        return array_merge($routes, self::$manualRoutes);
    }

    public static function addRouteManual(array|string $method, string $path, callable|array|string $handler, array $middleware = []) {
        self::$manualRoutes[] = [
            'method' => array_map('strtoupper', (array) $method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
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
            if (strpos(basename($file), '__') === 0) {
                continue;
            }

            $relativePath = str_replace([$viewsDirectory . '/', '.blade.php'], '', $file);

            $routePath = '/' . strtolower(str_replace(['\\', '/'], '/', $relativePath));

            $routePath = preg_replace('#/(index)$#', '', $routePath) ?: '/';

            foreach ($controllerRoutes as $controllerRoute) {
                $is_get = false;
                if (is_array($controllerRoute['method'])) {
                    $is_get = in_array('GET', $controllerRoute['method']);
                } else {
                    $is_get = $controllerRoute['method'] == 'GET';
                }
                if ($controllerRoute['path'] == $routePath && $is_get) {
                    continue 2;
                }
            }

            $relativePath = str_replace('/', '.', $relativePath);
            $routes[] = [
                'method' => ['GET'],
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
