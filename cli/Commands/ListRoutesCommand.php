<?php

namespace Cli\Commands;

use Framework\Routing\AutoRouteCollector;
use Framework\Classes\Config;
use Cli\BaseCommand;
use ReflectionMethod;

class ListRoutesCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("list:routes", "List all the routes with their method, URI, and file path.");
    }

    public function execute(array $options): void {
        
        $viewsDirectory = Config::get('routing.automatic_routes') ? Config::get('routing.automatic_routes') : false;

        // Initialize AutoRouteCollector and collect all routes
        $autoRouteCollector = new AutoRouteCollector();
        $routes = $autoRouteCollector->collectRoutes('auto', $viewsDirectory);

        if (empty($routes)) {
            echo "No routes found.\n";
            return;
        }

        // Loop through all collected routes and display them
        foreach ($routes as $route) {
            $handler = $route['handler'];

            // If the handler is a controller method (array with two elements)
            if (is_array($handler) && count($handler) == 2) {
                list($controller, $method) = $handler;

                try {
                    $reflectionMethod = new ReflectionMethod($controller, $method);

                    $filePath = $reflectionMethod->getFileName();
                    $lineNumber = $reflectionMethod->getStartLine();

                    foreach ((array)$route['method'] as $methodType) {
                        echo sprintf(
                            "%-7s %-25s %-50s:%d\n",
                            strtoupper($methodType),
                            $route['path'], 
                            $filePath, 
                            $lineNumber
                        );
                    }
                } catch (\ReflectionException $e) {
                    // If ReflectionMethod fails, print the error message (optional)
                    echo "[ERROR] Could not reflect method '{$method}' in controller '{$controller}'.\n";
                }
            }
            // If the handler starts with @render, process it as a Blade view
            elseif (is_array($handler) && isset($handler[0]) && strpos($handler[0], '@render:') === 0) {
                // Extract the view path by removing '@render:' and replacing '.' with '/'
                $viewPath = str_replace('@render:', '', $handler[0]);
                $viewPath = str_replace('.', '/', $viewPath) . '.blade.php';

                // Adjust the route path to remove the subdirectory and make it a valid route
                $adjustedPath = '/' . trim($route['path'], '/');

                // Get the full path to the Blade view file
                // $fullViewPath = DIR . '/app/Views/' . $viewsDirectory . '/' . $viewPath;
                $fullViewPath = DIR . '/app/Views/'. $viewPath;

                
                // Check if the Blade file exists
                if (file_exists($fullViewPath)) {
                    echo sprintf(
                        "%-7s %-25s %-50s:%d\n",  // Only GET method for Blade views
                        'GET',
                        $adjustedPath,  // Adjusted path for Blade view
                        realpath($fullViewPath),  // Full absolute path for the Blade view
                        1  // Line number is 1 for Blade views
                    );
                } else {
                    echo sprintf(
                        "%-7s %-25s %s (file not found)\n",
                        'GET',
                        $adjustedPath,
                        $fullViewPath
                    );
                }
            }
            // Handle route with an unknown handler
            else {
                $methodType = (array)$route['method']; // Ensure it's treated as an array
                echo sprintf(
                    "%-7s %-25s %s (unknown handler)\n", 
                    strtoupper($methodType[0] ?? 'GET'), // Default to GET if method is not found
                    $route['path'], 
                    print_r($handler, true)  // Print handler as a string to avoid array-to-string conversion
                );
            }
        }
    }
}
