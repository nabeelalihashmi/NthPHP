<?php

namespace Framework\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public array $method,
        public string $path,
        public array $middleware = []
    ) {}
}
