<?php

namespace Cli\Commands;

use Cli\BaseCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ViewsClearCacheCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("views:clearcache", "Clear all cached Blade views.");
    }

    public function execute(array $options): void {
        $cacheDirectory = DIR . '/cache/compiled';

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

        echo "All cached views have been cleared successfully!\n";
    }
}
