<?php

namespace Framework\Classes;

use InvalidArgumentException;

class Config {
    private static array $config = [];
    public static function all(): array {
        return self::$config;
    }
    public static function load(string $filePath): void {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Config file not found: {$filePath}");
        }

        $nestedConfig = require $filePath;

        if (!is_array($nestedConfig)) {
            throw new InvalidArgumentException("Config file must return an array: {$filePath}");
        }

        self::$config = self::flatten($nestedConfig);
    }

    public static function get(string $key, $default = null) {
        return self::$config[$key] ?? $default;
    }

    
    private static function flatten(array $array, string $prefix = ''): array {
        $flat = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            if (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    $flat[$fullKey] = $value;
                } else {
                    $flat = array_merge($flat, self::flatten($value, $fullKey));
                }
            } else {
                $flat[$fullKey] = $value;
            }
        }
        return $flat;
    }
}
