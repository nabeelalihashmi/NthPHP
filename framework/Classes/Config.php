<?php

namespace Framework\Classes;

use InvalidArgumentException;

class Config {
    private static array $config = [];
    private static array $nestedConfig = []; // Store original nested array

    public static function all(): array {
        return self::$nestedConfig;
    }

    public static function load(string $filePath): void {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Config file not found: {$filePath}");
        }

        $nestedConfig = require $filePath;

        if (!is_array($nestedConfig)) {
            throw new InvalidArgumentException("Config file must return an array: {$filePath}");
        }

        self::$nestedConfig = $nestedConfig;  // Store the original array
        self::$config = self::flatten($nestedConfig);
    }

    public static function get(string $key, $default = null) {
        // Check if the flattened version has the key
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        // Check if the original array has it (for array values)
        return self::getNestedValue(self::$nestedConfig, explode('.', $key), $default);
    }

    private static function getNestedValue(array $array, array $keys, $default) {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return $default;
            }
            $array = $array[$key];  // Traverse down the nested array
        }
        return $array;
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
