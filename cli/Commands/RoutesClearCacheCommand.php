<?php

namespace Cli\Commands;

use Cli\BaseCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RoutesClearCacheCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("routes:clearcache", "Clear all routes cache files.");
    }

    public function execute(array $options): void {
        $cacheDirectory = DIR . '/cache/routes';

        if (!is_dir($cacheDirectory)) {
            echo "[WARNING] Cache directory not found or already cleared: {$cacheDirectory}\n";
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile() || $file->isLink()) {
                unlink($file->getPathname());
            } elseif ($file->isDir()) {
                rmdir($file->getPathname());
            }
        }

        echo "All cache files have been cleared successfully!\n";
    }
}
