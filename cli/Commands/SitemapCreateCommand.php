<?php

namespace Cli\Commands;

use Framework\Routing\AutoRouteCollector;
use Framework\Classes\Config;
use Cli\BaseCommand;
use ReflectionMethod;

class SitemapCreateCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("sitemap:create", "Generate a sitemap XML file for all routes.");
    }

    public function execute(array $options): void {
        $outputPath = $options[0] ?? DIR . '/sitemap.xml';

        $viewsDirectory = Config::get('routing.automatic_routes') ? Config::get('routing.automatic_routes') : false;
        $autoRouteCollector = new AutoRouteCollector();
        $routes = $autoRouteCollector->collectRoutes('auto', $viewsDirectory);

        if (empty($routes)) {
            echo "No routes found to generate sitemap.\n";
            return;
        }

        $sitemapEntries = [];

        foreach ($routes as $route) {
            $handler = $route['handler'];

            // if ($this->hasNoSitemapAttribute($handler)) {
                continue;
            }

            $methods = (array)$route['method'];
            if (in_array('GET', array_map('strtoupper', $methods))) {
                $sitemapEntries[] = $this->createSitemapEntry($route['path']);
            }
        }

        if (empty($sitemapEntries)) {
            echo "No valid routes found for sitemap generation.\n";
            return;
        }

        $sitemapContent = $this->generateSitemap($sitemapEntries);

        // Ensure the directory exists before saving the file
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($outputPath, $sitemapContent);

        echo "Sitemap successfully created at: $outputPath\n";
    }

    private function hasNoSitemapAttribute($handler): bool {
        // Check if the handler is a controller method
        if (is_array($handler) && count($handler) == 2) {
            list($controller, $method) = $handler;
            try {
                $reflectionMethod = new ReflectionMethod($controller, $method);
                $attributes = $reflectionMethod->getAttributes('NoSitemap');
                return !empty($attributes);
            } catch (\ReflectionException $e) {
                // If ReflectionMethod fails, assume NoSitemap is not present
                return false;
            }
        }

        // For non-controller routes, NoSitemap is not applicable
        return false;
    }

    private function createSitemapEntry(string $path): string {
        $fullPath = rtrim(Config::get('app.url'), '/') . '/' . ltrim($path, '/');
        $escapedPath = htmlspecialchars($fullPath, ENT_XML1, 'UTF-8');
        return "<url><loc>$escapedPath</loc></url>";
    }

    private function generateSitemap(array $entries): string {
        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $header .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        $footer = "</urlset>\n";
        return $header . implode("\n", $entries) . "\n" . $footer;
    }
}