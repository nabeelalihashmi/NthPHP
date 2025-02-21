<?php

namespace Framework\Classes;

class Defer {
    private static array $stack = [];

    public static function defer(callable $callback): void {
        self::$stack[] = $callback;
        if (empty(self::$stack) === false) {
            register_shutdown_function([self::class, 'runDeferred']);
        }
    }

    public static function runDeferred(): void {
        while ($callback = array_pop(self::$stack)) {
            call_user_func($callback);
        }
    }
}