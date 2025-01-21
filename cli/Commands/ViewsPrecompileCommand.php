<?php

namespace Cli\Commands;

use Cli\BaseCommand;
use Framework\Classes\Blade;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ViewsPrecompileCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("views:precompile", "Precompile all Blade views into the cache.");
    }

    public function execute(array $options): void {

        $viewsDirectory = DIR . '/app/Views';
        $cacheDirectory = DIR . '/cache/compiled';

        if (!is_dir($viewsDirectory)) {
            echo "[ERROR] Views directory not found: {$viewsDirectory}\n";
            return;
        }

        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
            echo "Cache directory created: {$cacheDirectory}\n";
        }

        // Initialize Blade instance
        $blade = new Blade($viewsDirectory, $cacheDirectory);

        // Scan the views directory for Blade files
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsDirectory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $viewPath = str_replace([$viewsDirectory, '.blade.php'], '', $file->getPathname());
                $viewName = trim(str_replace(DIRECTORY_SEPARATOR, '.', $viewPath), '.');

                echo "Caching view: {$viewName}\n";

                // Compile the view
                $blade->compile($viewName);
            }
        }

        echo "All views have been precompiled successfully!\n";
    }
}
